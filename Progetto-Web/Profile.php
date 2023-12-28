<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href ="Profile.css">
</head>
<body>
    
<header id="header">
    <h2>AniManga</h2>
    <div class="buttons">
                <button id="btnLogout" class="btn-Logout">Logout</button>
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
            <h2>Username</h2>

            
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
            window.location.href = 'After_Register.html';
        }
    });

    document.getElementById('btnLogout').addEventListener('click', function() {
        window.location.href = 'Homepage.html';
    });
    </script>

    

    <script src="immagini/vanilla-tilt.js"></script>
</body>
</html>
