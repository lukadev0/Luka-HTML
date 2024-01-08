<?php
session_start();

$connection = require __DIR__ . "/../database_connection.php";

if (isset($_SESSION['user_id'])) {
    $id_utente = $_SESSION['user_id'];
    $query = "SELECT * FROM utenti WHERE id = :id_utente";
    $stmt = $connection->prepare($query);
    $stmt->bindParam(':id_utente', $id_utente, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $username = $result['username'];
}

if (isset($_GET['username'])) {
    $_SESSION['username'] = $_GET['username'];
}

if (isset($_SESSION['username'])) {
    // Recupera il post associato all'utente selezionato
    $selectedUsername = $_SESSION['username'];
    $getPostQuery = "SELECT * FROM post WHERE username = :username ORDER BY data_creazione DESC LIMIT 1";
    $getPostStmt = $connection->prepare($getPostQuery);
    $getPostStmt->bindParam(':username', $selectedUsername, PDO::PARAM_STR);
    $getPostStmt->execute();
    $selectedPost = $getPostStmt->fetch(PDO::FETCH_ASSOC);

    $post_id = $selectedPost['id'];

} else {
    // Utente non selezionato
    header("Location: Homepage.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['btnreply'])) {
    // Salva il reply nel database
    $message = $_POST['Rmsg'];
    $currentTime = date("Y-m-d H:i:s");

    $saveReplyQuery = "INSERT INTO commenti (utente_id, username, post_id, contenuto, data_creazione) VALUES (:utente_id, :username, :post_id, :message, :data_creazione)";
    $saveReplyStmt = $connection->prepare($saveReplyQuery);
    $saveReplyStmt->bindParam(':utente_id', $id_utente, PDO::PARAM_INT);
    $saveReplyStmt->bindParam(':username', $username, PDO::PARAM_STR);
    $saveReplyStmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
    $saveReplyStmt->bindParam(':message', $message, PDO::PARAM_STR);
    $saveReplyStmt->bindParam(':data_creazione', $currentTime, PDO::PARAM_STR);

    $saveReplyStmt->execute();

    // Incrementa il contatore post_count
    $updateReplyCountQuery = "UPDATE utenti SET reply_count = reply_count + 1 WHERE id = :user_id";
    $updateReplyCountStmt = $connection->prepare($updateReplyCountQuery);
    $updateReplyCountStmt->bindParam(':user_id', $id_utente, PDO::PARAM_INT);
    $updateReplyCountStmt->execute();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['remove'])) {
    // Rimuovi il post dal database
    $replyIdToRemove = $_POST['post_id'];  
    
    $removeReplyQuery = "DELETE FROM commenti WHERE id = :id AND utente_id = :utente_id";
    $removeReplyStmt = $connection->prepare($removeReplyQuery);
    $removeReplyStmt->bindParam(':id', $replyIdToRemove, PDO::PARAM_INT);
    $removeReplyStmt->bindParam(':utente_id', $id_utente, PDO::PARAM_INT);
    $removeReplyStmt->execute();

    $updateReplyCountQuery = "UPDATE utenti SET reply_count = GREATEST(reply_count - 1, 0) WHERE id = :user_id";
    $updateReplyCountStmt = $connection->prepare($updateReplyCountQuery);
    $updateReplyCountStmt->bindParam(':user_id', $id_utente, PDO::PARAM_INT);  
    $updateReplyCountStmt->execute();

    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

// Query modificata per recuperare commenti relativi al post corrente e quelli senza post associato
$query = "SELECT * FROM commenti WHERE post_id = :post_id OR post_id IS NULL ORDER BY data_creazione DESC";
$stmt = $connection->prepare($query);
$stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
$stmt->execute();
$Replies = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: array();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conversation Modal</title>
    <link rel="stylesheet" href="conversation_style.css">
    <script src="https://kit.fontawesome.com/186eb98a62.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</head>
<body>

  <header>
    <h2><span id="logo">AniManga Forum</span></h2>
    
    <div class="buttons">
        <button id="btnForum" class="btn-forum">Forum</button>
    </div>
  </header>

  <div class="container">
    <div class="panel panel-default" style="margin-top:50px">
        <div class="panel-body">
            <h3>Question of <?= htmlspecialchars($_SESSION['username']) ?></h3>
            <div class="post-box">
                <p><strong>Username:</strong> <?= htmlspecialchars($selectedPost['username']) ?></p>
                <p><strong>Posts:</strong> <?= htmlspecialchars($selectedPost['posts']) ?></p>
                <p><strong>Data Creazione:</strong> <?= htmlspecialchars($selectedPost['data_creazione']) ?></p>
                    
                <button class="reply-button" onclick="openRepliesModal(<?= $post_id ?>)">Reply</button>
            </div>
        </div>
    </div>



    <div id="replies-section" class="panel panel-default">
        <div class="panel-body">
            <h4>Recent Replies</h4>
            <table class="table" id="MyTable">
                <tbody id="record">
                <?php foreach ($Replies as $reply) { ?>

                    
                        <div class="post-box">
                            <p><strong>Username:</strong> <?= htmlspecialchars($reply['username']) ?></p>
                            <p><strong>Commenti:</strong> <?= htmlspecialchars($reply['contenuto']) ?></p>
                            <p><strong>Data Creazione:</strong> <?= htmlspecialchars($reply['data_creazione']) ?></p>

                            <button class="reply-button" onclick="replyConversation('<?= htmlspecialchars($reply['username']) ?>')">Reply</button>

                            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $reply['utente_id']) { ?>
                            <form method="post" style="display: inline;">
                                <input type="hidden" name="post_id" value="<?= $reply['id'] ?>">
                                <input type="submit" name="remove" class="remove-button" value="Remove">
                            </form>
                        </div>
                    <?php } ?>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
  

    <!-- Modal -->
  <div id="ReplyModal" class="modal">
    <div class="modal-content">
        
        <span class="close" onclick="closeModal()">&times;</span>
        <h4 class="modal-title">Reply Box</h4>
        
        <form id="replyForm" method="post">
            
            <input type="hidden" id="commentid" name="Rcommentid">
            <input type="hidden" id="post_id" name="post_id">
            
            <div class="form-group">
                <label for="usr">Write your reply down there, <span id="welcomeUsername"><?= htmlspecialchars($username) ?></span>!<hr></label>
            </div>
            
            <div class="form-group">
                <label for="comment">Write your reply:</label>
                <div><textarea class="form-reply" rows="5" id="Rmsg" name="Rmsg" required></textarea></div>
            
            </div>
            <input type="submit" id="btnreply" name="btnreply" class="btn btn-primary" value="Reply">
        </form>
    </div>
  </div>

  <script>
    function replyConversation(username) {
        // Costruisci l'URL con il parametro necessario (puoi aggiungere altri parametri se necessario)
        const url = 'reply_comments.php?username=' + encodeURIComponent(username);
        
        // Effettua il reindirizzamento
        window.location.href = url;
    }
    </script>


  <script>
    document.getElementById('btnForum').addEventListener('click', function() {
        window.location.href = 'Forum.php';
    });
  </script>

  <script>
    function openRepliesModal() {
        $('#post_id').val(post_id);
        $('#ReplyModal').fadeIn();
    }

    function closeModal() {
        $('#ReplyModal').fadeOut();
    }
  </script>
    
    <script>
    // Funzione per gestire l'invio del modulo quando si preme il tasto "Invio"
    function handleKeyPress(event) {
        if (event.keyCode === 13 && !event.shiftKey) { // 13 è il codice del tasto "Invio"
            event.preventDefault(); // Impedisce l'invio del modulo tramite "Invio" per evitare conflitti
            document.getElementById('btnreply').click(); // Simula il clic sul pulsante "Send"
        }
    }

    // Aggiungi l'ascoltatore degli eventi alla textarea
    document.querySelector('textarea[name="Rmsg"]').addEventListener('keypress', handleKeyPress);
    </script>
  


</body>
</html>
