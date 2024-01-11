<?php
session_start();

$connection = require __DIR__ . "/../database_connection.php";

if (isset($_SESSION['user_id'])) {
    
    $query = "SELECT * FROM utenti WHERE id = :id_utente";
    $stmt = $connection->prepare($query);
    $stmt->bindParam(':id_utente', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $username = $result['username'];


    $currentTime = date("Y-m-d H:i:s");
    $id_utente = $result['id'];}
    
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
    </header>


    <div class="container">
        <div class="panel panel-default" style="margin-top:50px">
            <div class="panel-body">
                <h3>Animanga forum</h3>
                <form name="frm" method="post">
                    <div class="form-group">
                        <label for="usr">Welcome User, to use this website function you need to <a href ="../Login.php">login</a> or <a href = "../Registration.php"> register</a>!<hr></label>
                    </div>
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
                                <p><strong>Post:</strong> <?= htmlspecialchars($post['posts']) ?></p>
                                <p><strong>Data Creazione:</strong> <?= htmlspecialchars($post['data_creazione']) ?></p>

                                
                                <button class="joinconv-button" onclick="joinConversation('<?= htmlspecialchars($post['username']) ?>')">Join the Conversation</button>
                            
                            </div>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        
    </div>

    <script>
    function joinConversation(username) {
        // Costruisci l'URL con il parametro necessario (puoi aggiungere altri parametri se necessario)
        const url = 'Not_Logged_In_Conversation.php?username=' + encodeURIComponent(username);
        
        // Effettua il reindirizzamento
        window.location.href = url;
    }
    </script>



    <script>
    // In caso di clic su animanga
    document.getElementById('logo').addEventListener('click', function () {
        <?php if(isset($_SESSION['user_id'])) : ?>
            // Se l'utente è loggato, reindirizza ad after_register.php
            window.location.href = '../After_register.php';
        <?php else : ?>
            // Se l'utente non è loggato, reindirizza a homepage.php
            window.location.href = '../Homepage.php';
        <?php endif; ?>
    });
    </script>
</body>
</html>