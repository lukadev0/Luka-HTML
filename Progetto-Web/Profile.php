<?php //Prendiamo lo username in modo da metterlo nel profile
session_start();
    if(isset($_GET["id"])){
        $id_utente = $_GET["id"];
        $connection = require __DIR__."/database_connection.php";
        $query = "SELECT * FROM utenti WHERE id = :id_utente";
        $stmt = $connection -> prepare("$query");
        $stmt->bindParam(':id_utente',$id_utente,PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $username = $result['username'];


    }


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href ="Profile.css">
</head>
<body>
    
<header id="header" class="custom-header">
    <h2 class="logo">AniManga</h2>
    <div class="buttons">
        <button id="btnLogout" class="btn-Logout">Logout</button>
    </div>
</header>

</header>

    <div class="container">
        <div class="card" data-tilt>
            <?php
                $imageFolder = 'PFP/';
                $images = glob($imageFolder . '*.jpg'); 
                $randomImage = $images[array_rand($images)]; 
                echo '<img src="' . $randomImage . '" alt="Profile Picture" class="profile-img">';
            ?>
            <h2><?=htmlspecialchars($username)?></h2>

            
            <div class="row">
                <div>
                    <p>Followers</p>
                </div>

                
                    <p>Following</p>
                </div>
            </div>
        </div>

    </div>

    <script>
    document.getElementById('header').addEventListener('click', function(event) {
        // Controllo se il click è avvenuto sull'elemento 'h2' (logo)
        if (event.target.tagName.toLowerCase() === 'h2') {
            window.location.href = 'After_Register.php';
        }
    });

    document.getElementById('btnLogout').addEventListener('click', function() {
        window.location.href = 'Homepage.html';
    });
    </script>

    

    <script src="immagini/vanilla-tilt.js"></script>
</body>
</html>
