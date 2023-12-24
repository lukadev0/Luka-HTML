<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href ="Profile.css">
</head>
<body>
    
    <div class="container">
        <div class="card" data-tilt>
            <?php
                $imageFolder = 'PFP/';
                $images = glob($imageFolder . '*.jpg'); // Ottieni la lista di file JPG nella cartella
                $randomImage = $images[array_rand($images)]; // Seleziona un'immagine in modo casuale

                echo '<img src="' . $randomImage . '" alt="Profile Picture" class="profile-img">';
            ?>
            <h2>Username</h2>
            <div class="buttons">
                <button id="btnHome" class="btn-Home">Home</button>
                <button id="btnLogout" class="btn-Logout">Logout</button>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('btnLogout').addEventListener('click', function() {
            window.location.href = 'Homepage.html';
        });
    </script>

    <script>
        document.getElementById('btnHome').addEventListener('click', function() {
            window.location.href = 'After_Register.html';
        });
    </script>

    <script src="immagini/vanilla-tilt.js"></script>
</body>
</html>
