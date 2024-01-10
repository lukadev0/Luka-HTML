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
    $selectedUsername = $_SESSION['username'];
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


$query = "SELECT * FROM commenti WHERE post_id = :post_id ORDER BY data_creazione ASC";
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
    <link rel="stylesheet" href="Conversation_style.css">
    <script src="https://kit.fontawesome.com/186eb98a62.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</head>

<body>

    <header>
        <h2><span id="logo">AniManga Forum</span></h2>
        <div class="box">
            <input type="text" id="searchInput" placeholder="Type to search replies..">
            <a href="#" onclick="search()">
                <div class="icon">
                    <i id="searchIcon" class="fa-solid fa-magnifying-glass" style="color: #000000;"></i>
                </div>
            </a>
        </div>
        
    </header>

    <div class="container">

        <div class="panel panel-default" style="margin-top: 50px">
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
                        <?php foreach ($allReplies as $reply) { ?>
                            <tr>
                                <td>
                                    <div class="post-box" data-reply-id="<?= $reply['id'] ?>">
                                        <p><strong>Username:</strong> <?= htmlspecialchars($reply['username'])?></p>
                                        
                                        <?php if (!empty($reply['parent_comment_id'])) { ?>
                                            <p><strong class = "replyto">Replying to:</strong> <?= getMessageById($reply['parent_comment_id'], $allReplies)?><strong class = "replyto"> by:</strong> <?= getUsernameById($reply['parent_comment_id'], $allReplies)?></p>
                                        <?php } ?>
                                        
                                        <strong>Reply:</strong> <p class = "post-text" style = "display: inline"><?= htmlspecialchars($reply['contenuto']) ?></p>
                                        <p><strong>Data Creazione:</strong> <?= htmlspecialchars($reply['data_creazione']) ?></p>
                                        
                                        <button class="reply-button" data-parent-reply-id="<?= $reply['id'] ?>" onclick="openRepliesModal(<?= $post_id ?>, <?= $reply['id'] ?>)">Reply</button>
                                        
                                        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $reply['utente_id']) { ?>
                                            <form method="post" style="display: inline;">
                                                <input type="hidden" name="post_id" value="<?= $reply['id'] ?>">
                                                <input type="submit" name="remove" class="remove-button" value="Remove">
                                            </form>
                                        <?php } ?>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="ReplyModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <h4 class="modal-title">Reply Box</h4>
                
                <form id="replyForm" method="post">
                    <input type="hidden" id="commentid" name="Rcommentid">
                    <input type="hidden" id="post_id" name="post_id">
                    <input type="hidden" id="parent_comment_id" name="parent_comment_id" value="">
                    
                    <div class="form-group">
                        <label for="usr">
                            Write your reply down there, <span id="welcomeUsername"><?= htmlspecialchars($username) ?></span>!
                            <hr>
                        </label>
                    </div>
                    
                    <div class="form-group">
                        <label for="comment">Write your reply:</label>
                        <div><textarea class="form-reply" rows="5" id="Rmsg" name="Rmsg" required></textarea></div>
                    </div>
                    
                    <input type="submit" id="btnreply" name="btnreply" class="btn btn-primary" value="Reply">
                </form>

            </div>
        </div>
    </div>



    <script>
        function search() {
        var searchTerm = document.getElementById("searchInput").value;
        highlightSearchTerm(searchTerm);
        }

        function highlightSearchTerm(searchTerm) {
            var postElements = document.querySelectorAll('.post-box');

            postElements.forEach(function(postElement) {
                var postTextElement = postElement.querySelector('.post-text');

                // Verifica se l'elemento è nullo prima di tentare di accedere a 'innerText'
                if (postTextElement) {
                    var postText = postTextElement.innerText;
                    var highlightedText = postText.replace(new RegExp(searchTerm, "gi"), match => `<span style="background-color: #38b0ff;">${match}</span>`);
                    postTextElement.innerHTML = highlightedText;
                }
            });
        }

        function clearHighlight() {
            var postElements = document.querySelectorAll('.post-box');

            postElements.forEach(function(postElement) {
                var postTextElement = postElement.querySelector('.post-text');
                if (postTextElement) {
                    postTextElement.innerHTML = postTextElement.innerText;
                }
            });

            document.getElementById('searchInput').value = '';
        }

        function handleSearch(event) {
            if (event.key === "Enter") {
                search();
            }
        }

        function handleIconClick() {
            search();
        }

        function handleOutsideClick(event) {
            // Verifica se il clic è avvenuto al di fuori della barra di ricerca
            if (!event.target.closest('.box')) {
                clearHighlight();
            }
        }

        document.getElementById('searchInput').addEventListener('keyup', handleSearch);
        document.getElementById('searchIcon').addEventListener('click', handleIconClick);
        document.addEventListener('click', handleOutsideClick);
    </script>
    


    
    <script>

        var currentParentReplyId = null;

        function openRepliesModal(post_id, parent_comment_id) {
            $('#post_id').val(post_id);

            if (parent_comment_id) {
                currentParentReplyId = parent_comment_id;
                $('#parent_comment_id').val(parent_comment_id);
            } else {
                currentParentReplyId = null;
                $('#parent_comment_id').val('');
            }

            $('#ReplyModal').fadeIn();
        }

        function closeModal() {
            $('#ReplyModal').fadeOut();
        }

        function getUsernameById(userId, replies) {
            for (var i = 0; i < replies.length; i++) {
                if (replies[i].id === userId) {
                    return replies[i].username;
                }
            }
            return '';
        }
    </script>

    <script>
        function handleKeyPress(event) {
            if (event.keyCode === 13 && !event.shiftKey) {
                event.preventDefault();
                document.getElementById('btnreply').click();
            }
        }

        document.querySelector('textarea[name="Rmsg"]').addEventListener('keypress', handleKeyPress);
    </script>

    <script> //in caso di click forgotPassword
        document.getElementById('logo').addEventListener('click', function () {
            window.location.href = 'Forum.php';
        });
    </script>
</body>

</html>
