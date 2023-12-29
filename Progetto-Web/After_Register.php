<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    // Utente non autenticato, reindirizza alla pagina di login o gestisci l'accesso non autorizzato
    header("location: Login.php");
    exit();
}

$id_utente = $_SESSION["user_id"];
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AniManga Forum</title>
    <link rel="stylesheet" href="Homepage_style.css">
</head>
<body>
    <header>
        <h2 class="logo">AniManga</h2>
        <nav class="navigation">
            <a href="#">Forum</a>
            <a href="Contact.html">Contact</a>
            <button id="btnLogin" class="btnLogin-popup">Profile</button>

        </nav>
    </header>

    <div class="content">
        <small>Welcome to our</small>
        <h1>AniManga Forum</h1>

    </div>

    <script>
        document.getElementById('btnLogin').addEventListener('click', function() {
            window.location.href = 'Profile.php?id=<?=$id_utente?>';
        });
    </script>
</body>
</html>