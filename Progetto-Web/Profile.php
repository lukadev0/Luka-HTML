<?php
// Funzione per ottenere il conteggio dei post di un utente
function getUserPostCount($userId, $connection)
{
    $query = "SELECT post_count FROM utenti WHERE id = :user_id";
    $stmt = $connection->prepare($query);
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return isset($result['post_count']) ? $result['post_count'] : 0;
}

function getUserReplyCount($userId, $connection)
{
    $query = "SELECT reply_count FROM utenti WHERE id = :user_id";
    $stmt = $connection->prepare($query);
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return isset($result['reply_count']) ? $result['reply_count'] : 0;
}

// Funzione per eliminare il token dall'utente
function deleteToken($userId, $connection)
{
    $deleteTokenQuery = "UPDATE utenti SET token = NULL WHERE id = :user_id";
    $deleteTokenStmt = $connection->prepare($deleteTokenQuery);
    $deleteTokenStmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $deleteTokenStmt->execute();
}

// Funzione per eliminare l'account dell'utente
function deleteAccount($userId, $connection)
{
    // Elimina i commenti associati ai posts dell'utente
    $deleteCommentsQuery = "DELETE FROM commenti WHERE post_id IN (SELECT id FROM post WHERE utente_id = :utente_id)";
    $deleteCommentsStmt = $connection->prepare($deleteCommentsQuery);
    $deleteCommentsStmt->bindParam(':utente_id', $userId, PDO::PARAM_INT);
    $deleteCommentsStmt->execute();

    // Elimina i posts associati all'utente
    $deletePostsQuery = "DELETE FROM post WHERE utente_id = :utente_id";
    $deletePostsStmt = $connection->prepare($deletePostsQuery);
    $deletePostsStmt->bindParam(':utente_id', $userId, PDO::PARAM_INT);
    $deletePostsStmt->execute();

    // Elimina l'utente
    $deleteUserQuery = "DELETE FROM utenti WHERE id = :utente_id";
    $deleteUserStmt = $connection->prepare($deleteUserQuery);
    $deleteUserStmt->bindParam(':utente_id', $userId, PDO::PARAM_INT);
    $deleteUserStmt->execute();
}

session_start();

if (isset($_GET["id"])) {
    $id_utente = $_GET["id"];
    $connection = require __DIR__ . "/database_connection.php";
    $query = "SELECT * FROM utenti WHERE id = :id_utente";
    $stmt = $connection->prepare($query);
    $stmt->bindParam(':id_utente', $id_utente, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $username = $result['username'];
}

// Logout: elimina il token e reindirizza alla homepage
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['logout'])) {
    // Controlla se l'utente è loggato e ha un token
    if (isset($_SESSION['user_id']) && isset($_SESSION['token'])) {
        // Elimina il token dal database
        deleteToken($_SESSION['user_id'], $connection);

        // Cancella le variabili di sessione
        session_unset();
        session_destroy();
    }

    // Reindirizza alla homepage
    header("Location: Homepage.php");
    exit();
}

// Delete Account: elimina definitivamente l'account
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_account'])) {
    // Controlla se l'utente è loggato
    if (isset($_SESSION['user_id'])) {
        // Elimina l'account dell'utente
        deleteAccount($_SESSION['user_id'], $connection);

        // Logout: elimina il token e reindirizza alla homepage
        deleteToken($_SESSION['user_id'], $connection);
        session_unset();
        session_destroy();

        // Reindirizza alla homepage
        header("Location: Homepage.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="Profile.css">
</head>
<body>
    
<header id="header" class="custom-header">
    <h2 class="logo">AniManga</h2>
    <div class="buttons">
        <form action="" method="post">
            <input type="hidden" name="logout" value="1">
            <button id="btnLogout" class="btn-Logout">Logout</button>
        </form>
    </div>
</header>

<div class="container">
    <div class="card" data-tilt>
        <?php
        $imageFolder = 'PFP/';
        $images = glob($imageFolder . '*.jpg');
        $randomImage = $images[array_rand($images)];
        echo '<img src="' . $randomImage . '" alt="Profile Picture" class="profile-img">';
        ?>
        <h2><?= htmlspecialchars($username) ?></h2>

        <div class="row">
            <p>Posts:  <?= htmlspecialchars(getUserPostCount($id_utente, $connection)) ?></p>
            <p>Replies: <?=htmlspecialchars(getUserReplyCount($id_utente, $connection)) ?></p>
        </div>

         <!-- Aggiungi il form per il "Delete Account" -->
         <form action="" method="post" onsubmit="return confirm('Sei sicuro di voler cancellare definitivamente il tuo account <?= htmlspecialchars($username) ?>?');">
            <input type="hidden" name="delete_account" value="1">
            <button class="btn-DeleteAccount">Delete Account</button>
        </form>
    </div>
</div>

<script>
    document.getElementById('header').addEventListener('click', function (event) {
        // Controllo se il click è avvenuto sull'elemento 'h2' (logo)
        if (event.target.tagName.toLowerCase() === 'h2') {
            window.location.href = 'After_Register.php';
        }
    });

    document.getElementById('btnLogout').addEventListener('click', function () {
        document.querySelector('form').submit();
    });
</script>

<script src="immagini/vanilla-tilt.js"></script>
</body>
</html>
