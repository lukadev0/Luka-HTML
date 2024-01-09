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

        <div class="box">
            <input type="text" id="searchInput" placeholder="Type to search..">
            <a href="#" onclick="search()">
                <div class="icon">
                    <i id="searchIcon" class="fa-solid fa-magnifying-glass" style="color: #000000;"></i>
                </div>
            </a>
        </div>
    </header>

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
                                <strong>Post:</strong> <p class="post-text" style="display: inline"><?= htmlspecialchars($post['posts'])?></p>
                                <p><strong>Data Creazione:</strong> <?= htmlspecialchars($post['data_creazione']) ?></p>

                                <button class="joinconv-button" onclick="joinConversation('<?= htmlspecialchars($post['username']) ?>')">Join the Conversation</button>

                                <?php
                                    if(isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['utente_id']) {
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
        function search() {
            var searchTerm = document.getElementById("searchInput").value;
            highlightSearchTerm(searchTerm);
        }

        function highlightSearchTerm(searchTerm) {
            var postElements = document.querySelectorAll('.post-box');

            postElements.forEach(function(postElement) {
                var postTextElement = postElement.querySelector('.post-text');
                var postText = postTextElement.innerText;
                var highlightedText = postText.replace(new RegExp(searchTerm, "gi"), match => `<span style="background-color: #38b0ff;">${match}</span>`);
                postTextElement.innerHTML = highlightedText;
            });
        }

        function clearHighlight() {
            var postElements = document.querySelectorAll('.post-box');

            postElements.forEach(function(postElement) {
                var postTextElement = postElement.querySelector('.post-text');
                postTextElement.innerHTML = postTextElement.innerText;
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
        function joinConversation(username) {
            const url = 'Conversation.php?username=' + encodeURIComponent(username);
            window.location.href = url;
        }
    </script>

    <script>
        var user = {
            username: <?= json_encode($_SESSION['username']) ?>
        };
        document.getElementById("welcomeUsername").innerText = user.username;
    </script>

    <script>
        // Funzione per gestire l'invio del modulo quando si preme il tasto "Invio"
        function handleKeyPress(event) {
            if (event.keyCode === 13 && !event.shiftKey) {
                event.preventDefault();
                document.getElementById('butsave').click();
            }
        }

        document.querySelector('textarea[name="msg"]').addEventListener('keypress', handleKeyPress);
    </script>

    <script>
        document.getElementById('logo').addEventListener('click', function () {
            <?php if(isset($_SESSION['user_id'])) : ?>
                window.location.href = '../After_register.php';
            <?php else : ?>
                window.location.href = '../Homepage.php';
            <?php endif; ?>
        });
    </script>
</body>
</html>