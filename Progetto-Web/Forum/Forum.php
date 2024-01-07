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

$_SESSION['username'] = $username;

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['save'])) {
    // Salva il post nel database
    $message = $_POST['msg'];
    $currentTime = date("Y-m-d H:i:s");

    $savePostQuery = "INSERT INTO post (utente_id, username, posts, data_creazione) VALUES (:utente_id, :username, :message, :data_creazione)";
    $savePostStmt = $connection->prepare($savePostQuery);
    $savePostStmt->bindParam(':utente_id', $id_utente, PDO::PARAM_INT);
    $savePostStmt->bindParam(':username', $username, PDO::PARAM_STR);
    $savePostStmt->bindParam(':message', $message, PDO::PARAM_STR);
    $savePostStmt->bindParam(':data_creazione', $currentTime, PDO::PARAM_STR);

    $savePostStmt->execute();

    
    // Incrementa il contatore post_count
    $updatePostCountQuery = "UPDATE utenti SET post_count = post_count + 1 WHERE id = :user_id";
    $updatePostCountStmt = $connection->prepare($updatePostCountQuery);
    $updatePostCountStmt->bindParam(':user_id', $id_utente, PDO::PARAM_INT);
    $updatePostCountStmt->execute();


    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['remove'])) {
    // Rimuovi il post dal database
    $postIdToRemove = $_POST['post_id'];
    
    $removePostQuery = "DELETE FROM post WHERE id = :post_id AND utente_id = :utente_id";
    $removePostStmt = $connection->prepare($removePostQuery);
    $removePostStmt->bindParam(':post_id', $postIdToRemove, PDO::PARAM_INT);
    $removePostStmt->bindParam(':utente_id', $id_utente, PDO::PARAM_INT);
    $removePostStmt->execute();

    $updatePostCountQuery = "UPDATE utenti SET post_count = GREATEST(post_count - 1, 0) WHERE id = :user_id";
    $updatePostCountStmt = $connection->prepare($updatePostCountQuery);
    $updatePostCountStmt->bindParam(':user_id', $id_utente, PDO::PARAM_INT);
    $updatePostCountStmt->execute();

    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

$query = "SELECT * FROM post ORDER BY data_creazione DESC";
$stmt = $connection->prepare($query);
$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: array();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AniManga Forum</title>
    <link rel="stylesheet" href="Forum_style.css">
    <script src="https://kit.fontawesome.com/186eb98a62.js" crossorigin="anonymous"></script>
</head>
<body>
    <header>
        <h2><span id="logo">AniManga Forum</span></h2>

        <nav class="navigation">
            <a href="#">Anime</a>
            <a href="#">Manga</a>
        </nav>
    </header>


      <!-- Modal
        <div id="ReplyModal" class="modal fade" role="dialog">
            <div class="modal-dialog">
        
            <!-- Modal content
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Reply Box</h4>
                </div>
                
                <div class="modal-body">
                <form name="frm1" method="post">
                    <input type="hidden" id="commentid" name="Rcommentid">
                    <div class="form-group">
                        <label for="usr">Write your reply down there , <span id="welcomeUsername"></span>!<hr></label>
                    </div>
                    <div class="form-group">
                        
                        <label for="comment">Write your reply:</label>
                        
                        <div><textarea class="form-reply" rows="5" name="Rmsg" required></textarea></div>
                        
                    </div>
                    <input type="button" id="btnreply" name="btnreply" class="btn btn-primary" value="Reply">
                </form>
            
            </div>
      
        </div> -->


    <div class="container">
        <div class="panel panel-default" style="margin-top:50px">
            <div class="panel-body">
                <h3>Animanga forum</h3>
                <form name="frm" method="post">
                    <input type="hidden" id="commentid" name="Pcommentid" value="0">
                    <div class="form-group">
                        <label for="usr">Welcome <span id="welcomeUsername"><?= htmlspecialchars($_SESSION['username']) ?></span>, write in the box down below to share your thoughts! <hr></label>
                    </div>
                    <div class="form-group">
                        <label for="comment">Write your question:</label>
                        <textarea class="form-control" rows="5" name="msg" required></textarea>
                    </div>
                    <input type="submit" id="butsave" name="save" class="btn btn-primary" value="Send">
                </form>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-body">
                <h4>Recent questions</h4>
                <table class="table" id="MyTable">
                    <tbody id="record">
                        <?php foreach ($posts as $post) : ?>
                            <div class="post-box">
                                <p><strong>Username:</strong> <?= htmlspecialchars($post['username']) ?></p>
                                <p><strong>Posts:</strong> <?= htmlspecialchars($post['posts']) ?></p>
                                <p><strong>Data Creazione:</strong> <?= htmlspecialchars($post['data_creazione']) ?></p>

                                
                                <button class="reply-button" onclick="replyToUser('<?= htmlspecialchars($post['username']) ?>')">Reply</button>

                            
                                <?php
                                    // Verifica se l'utente autenticato è il proprietario del post
                                    if(isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['utente_id']) {
                                        // Se sì, mostra il bottone "Remove"
                                ?>
                                    <form method="post" style="display: inline;">
                                        <input type="hidden" name="post_id" value="<?= htmlspecialchars($post['id']) ?>">
                                        <input type="submit" name="remove" class="remove-button" value="Remove">
                                    </form>
                                <?php
                                    }
                                ?>
                            
                            </div>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        
    </div>


    <script>
        function replyToUser(username) {
            // Aggiungi qui la logica per gestire la risposta all'utente specificato
            // Ad esempio, potresti aprire una finestra modale o reindirizzare l'utente a una pagina di risposta.
            alert("Rispondi a " + username);
        }
    </script>



    <script>
            // Aggiorna il messaggio di benvenuto con l'username dell'utente
            var user = {
                username: <?= json_encode($_SESSION['username']) ?> // Utilizza json_encode per gestire correttamente le stringhe JSON
            };
            document.getElementById("welcomeUsername").innerText = user.username;
        </script>

    

    <script>
    // Funzione per gestire l'invio del modulo quando si preme il tasto "Invio"
    function handleKeyPress(event) {
        if (event.keyCode === 13 && !event.shiftKey) { // 13 è il codice del tasto "Invio"
            event.preventDefault(); // Impedisce l'invio del modulo tramite "Invio" per evitare conflitti
            document.getElementById('butsave').click(); // Simula il clic sul pulsante "Send"
        }
    }

    // Aggiungi l'ascoltatore degli eventi alla textarea
    document.querySelector('textarea[name="msg"]').addEventListener('keypress', handleKeyPress);
    </script>




    <script>
        // In caso di clic su animanga
        document.getElementById('logo').addEventListener('click', function () {
            window.location.href = '../Homepage.php';
        });
    </script>
</body>
</html>
