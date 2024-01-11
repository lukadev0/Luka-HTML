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
    $username = $_GET['username'];
}

if (isset($username)) {
    $selectedUsername = $username;
    $getPostQuery = "SELECT * FROM post WHERE username = :username ORDER BY data_creazione DESC LIMIT 1";
    $getPostStmt = $connection->prepare($getPostQuery);
    $getPostStmt->bindParam(':username', $selectedUsername, PDO::PARAM_STR);
    $getPostStmt->execute();
    $selectedPost = $getPostStmt->fetch(PDO::FETCH_ASSOC);

    $post_id = $selectedPost['id'];
} else {
    header("Location: Homepage.php");
    exit();
}

// Dopo l'elaborazione del modulo
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['btnreply'])) {
    // Elabora il modulo e salva nel database
    $message = $_POST['Rmsg'];
    $currentTime = date("Y-m-d H:i:s");
    $parentCommentId = isset($_POST['parent_comment_id']) ? $_POST['parent_comment_id'] : null;

    $saveReplyQuery = "INSERT INTO commenti (utente_id, username, post_id, contenuto, data_creazione, parent_comment_id) VALUES (:utente_id, :username, :post_id, :message, :data_creazione, :parent_comment_id)";
    $saveReplyStmt = $connection->prepare($saveReplyQuery);
    $saveReplyStmt->bindParam(':utente_id', $id_utente, PDO::PARAM_INT);
    $saveReplyStmt->bindParam(':username', $username, PDO::PARAM_STR);
    $saveReplyStmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
    $saveReplyStmt->bindParam(':message', $message, PDO::PARAM_STR);
    $saveReplyStmt->bindParam(':data_creazione', $currentTime, PDO::PARAM_STR);
    $saveReplyStmt->bindParam(':parent_comment_id', $parentCommentId, PDO::PARAM_INT);

    $saveReplyStmt->execute();

    $updateReplyCountQuery = "UPDATE utenti SET reply_count = reply_count + 1 WHERE id = :user_id";
    $updateReplyCountStmt = $connection->prepare($updateReplyCountQuery);
    $updateReplyCountStmt->bindParam(':user_id', $id_utente, PDO::PARAM_INT);
    $updateReplyCountStmt->execute();

    // Dopo l'elaborazione, reindirizza l'utente a questa stessa pagina con un parametro nell'URL
    header("Location: " . $_SERVER['PHP_SELF'] . "?success=true");
    exit();
}

// Verifica se il parametro di successo è presente nell'URL
if (isset($_GET['success']) && $_GET['success'] === 'true') {
    // Rimuovi il parametro dall'URL e aggiorna la pagina
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['remove'])) {
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

    
}

function getUsernameById($userId, $replies) {
    foreach ($replies as $reply) {
        if ($reply['id'] == $userId) {
            return $reply['username'];
        }
    }
    return '';
}

function getMessageById($parentId, $replies) {
    foreach ($replies as $reply) {
        if ($reply['id'] == $parentId) {
            return $reply['contenuto'];
        }
    }
    return '';
}


$query = "SELECT * FROM commenti WHERE post_id = :post_id ORDER BY data_creazione DESC";
$stmt = $connection->prepare($query);
$stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
$stmt->execute();

$allReplies = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: array();
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
    </header>

    <div class="container">

        <div class="panel panel-default" style="margin-top: 50px">
            <div class="panel-body">
                <h3>Question of <?= htmlspecialchars($username) ?></h3>
                <div class="post-box">
                    <p><strong>Username:</strong> <?= htmlspecialchars($selectedPost['username']) ?></p>
                    <p><strong>Posts:</strong> <?= htmlspecialchars($selectedPost['posts']) ?></p>
                    <p><strong>Data Creazione:</strong> <?= htmlspecialchars($selectedPost['data_creazione']) ?></p>
                </div>
            </div>
        </div>

        <div id="replies-section" class="panel panel-default">
            <div class="panel-body">
                <h4>Recent Replies</h4>
                <table class="table" id="MyTable">
                    <tbody id="record">
                        <?php foreach ($allReplies as $reply) { ?>
                            <tr>
                                <td>
                                    <div class="post-box" data-reply-id="<?= $reply['id'] ?>">
                                        <p><strong>Username:</strong> <?= htmlspecialchars($reply['username']) ?></p>
                                        <?php if (!empty($reply['parent_comment_id'])) { ?>
                                            <p><strong class = "replyto">Replying to:</strong> <?= getMessageById($reply['parent_comment_id'], $allReplies)?>  <strong class = "replyto">of:</strong> <?= getUsernameById($reply['parent_comment_id'], $allReplies)?></p>
                                        <?php } ?>
                                        <p><strong>Reply:</strong> <?= htmlspecialchars($reply['contenuto']) ?></p>
                                        <p><strong>Data Creazione:</strong> <?= htmlspecialchars($reply['data_creazione']) ?></p>

                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>


    <script>
        document.getElementById('logo').addEventListener('click', function () {
            window.location.href = 'Not_Logged_In_Forum.php';
        });
    </script>


</body>

</html>
