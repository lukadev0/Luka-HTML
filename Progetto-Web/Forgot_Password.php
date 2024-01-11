<?php //modifica password
    $connection = require __DIR__ . '/database_connection.php';

    $verify = false;
    $usernameError = '';
    $passwordError = '';
    $resetSuccess = false;


    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $username = $_POST['username'];

        // Verifica se lo username esiste nel database
        $query = "SELECT * FROM utenti WHERE username = :username";
        $stmt = $connection->prepare($query);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();


        if ($stmt->rowCount() == 0) {
            $usernameError = 'Username does not exist.';
        } else {
            
            $newPassword = $_POST['newPassword'];
            $confirmPassword = $_POST['confirmPassword'];
        
            if ($newPassword !== $confirmPassword) {
                
                $passwordError = 'Passwords dont match';
            
            } elseif (strlen($newPassword) < 8) {
                // La password è troppo corta
                $passwordError = 'password needs to be at least 8 characters long';
            
            }elseif (!preg_match("/[0-9]/", $newPassword)) {
               
                $passwordError = 'Password needs to contain at least one number';
            
            }else{
                // Le password combaciano e contengono almeno un numero
                $newPasswordHashed = password_hash($newPassword, PASSWORD_BCRYPT);
        
                // Sostituisci la vecchia password nel database
                $updateQuery = "UPDATE utenti SET password = :password WHERE username = :username";
                $updateStmt = $connection->prepare($updateQuery);
                $updateStmt->bindParam(':password', $newPasswordHashed, PDO::PARAM_STR);
                $updateStmt->bindParam(':username', $username, PDO::PARAM_STR);
        
                if ($updateStmt->execute()) {
                    $resetSuccess = true;
                }
            }
        }
    }
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="Login_style.css">
    <script src="https://kit.fontawesome.com/186eb98a62.js" crossorigin="anonymous"></script>
</head>
<body>
    

    <header><h2><span id = "logo">AniManga</span></h2></header>

    <div class="wrapper">
        <form action="" method="post">   
            
            <h1>Modify</h1>


            <div class="input-box">
                <input id="username" name="username" type="text" placeholder="Username" required>
                <i class="fa-solid fa-user"></i>
                
                <?php if ($usernameError): ?>
                    <p class="error-message"><?= $usernameError; ?></p>
                <?php endif; ?>
            </div>


            <div class="input-box">
                <input id="newPassword" name="newPassword" type="password" placeholder="New Password" required>
                <i class="fa-solid fa-eye-slash" id="eyeicon"></i>
            </div>

            <div class="input-box">
                <input id="confirmPassword" name="confirmPassword" type="password" placeholder="Confirm Password" required>
                <i class="fa-solid fa-eye-slash" id="eyeicon_conf"></i>

                <?php if ($passwordError): ?>
                     <p class="error-message"><?= $passwordError; ?></p>
                <?php endif; ?>
            </div>
            

            <?php if ($resetSuccess): ?>
                <div class="center-em">
                    <em>Password reset successful!</em>
                </div>
            <?php endif; ?>
                
            <button type="submit" class="btn">Reset Password</button>

            <div class="login-link">
                <p>Remember your password? <a href="Login.php">Login</a></p>
            </div>


            
            


        </form>
    </div>

    <script>
        let eyeicon = document.getElementById("eyeicon");
        let password = document.getElementById("newPassword");
    
        eyeicon.onclick = function () {
            togglePasswordVisibility(password, eyeicon);
        }
    
        let eyeiconConf = document.getElementById("eyeicon_conf");
        let confirmPassword = document.getElementById("confirmPassword");
    
        eyeiconConf.onclick = function () {
            togglePasswordVisibility(confirmPassword, eyeiconConf);
        }
    
        function togglePasswordVisibility(inputField, eyeIcon) {
            if (inputField.type === "password") {
                inputField.type = "text";
                eyeIcon.classList.remove("fa-solid", "fa-eye-slash");
                eyeIcon.classList.add("fa-solid", "fa-eye");
            } else {
                inputField.type = "password";
                eyeIcon.classList.remove("fa-solid", "fa-eye");
                eyeIcon.classList.add("fa-solid", "fa-eye-slash");
            }
        }
    </script>
    


    <script>  //in caso di click su animanga
        document.getElementById('logo').addEventListener('click', function() {
            window.location.href = 'Homepage.php';
        });
    </script>




</body>
</html>