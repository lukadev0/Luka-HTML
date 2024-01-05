<?php
session_start();

// Verifica se l'utente è autenticato (token settato nel database)
if (!isset($_SESSION["user_id"])) {
    // Utente non autenticato, reindirizza alla pagina di login o gestisci l'accesso non autorizzato
    header("location: Login.php");
    exit();
}

// Se l'utente è autenticato, verifica la presenza del token nel database
$connection = require __DIR__ . '/database_connection.php'; // Assicurati di sostituire con il percorso effettivo del tuo file di connessione al database
$user_id = $_SESSION["user_id"];

$query = "SELECT token FROM utenti WHERE id = :user_id";
$stmt = $connection->prepare($query);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || empty($user['token'])) {
    // Se il token non è presente nel database, reindirizza ad homepage.html
    header("location: Homepage.php");
    exit();
} else {
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact</title>
    <link rel="stylesheet" href="Contact.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <header><h2>AniManga</h2></header>
        
    <div class="container">
        <div class="card" data-tilt>
            <img src="immagini/Contact.png">
            <h2>Luca Ciraolo</h2>
            <p>I'm a 3rd year student at University of Messina.<br><br>Check my contact box to see my works!</p>
            <div class="social-icons">
                <a href="https://github.com/lukadev0?tab=repositories" class="github"><i class='bx bxl-github'></i></a>
                <a href="https://www.linkedin.com/in/luca-ciraolo-6b02aa228/" class="linkedin"><i class='bx bxl-linkedin' ></i></a>
            </div>

            <div class="buttons">
                <button id="btnHome" class="btn-Home">Home</button>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('btnHome').addEventListener('click', function() {
            window.location.href = 'After_Register.php';  
        });
    </script>

    <script src="immagini/vanilla-tilt.js"></script>
</body>
</html>

<?php
    // Chiudi la parentesi graffa del blocco else
}
?>
