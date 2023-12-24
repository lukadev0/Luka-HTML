<?php

    $verify=false;
    if($_SERVER["REQUEST_METHOD"]==="POST"){
        $connection = require __DIR__ . '/database_connection.php'; // Connessione al database
        $username = $connection->quote($_POST['username']);
        $query = sprintf("SELECT *FROM utenti WHERE username = %s", $username);
        $stmt = $connection->query($query);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if(password_verify($_POST["password"],$user["password"])){
            session_start();
            session_regenerate_id();
            $_SESSION["user_id"] = $user["id"];
            header("location:After_Register.html");
        }  

        $verify = true;
    }

?>

<!DOCTYPE html>


<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="Login_style.css">
    <script src="https://kit.fontawesome.com/186eb98a62.js" crossorigin="anonymous"></script>
</head>
<body>
    
    <header id="header"><h2>AniManga</h2></header>

    <div class="wrapper">
        <form action="" method="post">   
            
            <h1>Login</h1>
            <div class="input-box">
                <input id ="username" name = "username" type="text" placeholder="Username" required>
                <i class="fa-solid fa-user"></i>
            </div>

            <div class="input-box">
                <input id = "password" name = "password" type="password" placeholder="Password" required>
                <i class="fa-solid fa-eye-slash" id="eyeicon"></i>
            </div>

            <div class="remember-forgot">
                <label><input type="checkbox">Remember me</label>
                <a href="#">Forgot password?</a>
            </div>

            <button type = "submit" class="btn">Login</button>

            <div class="register-link">
                <p>Don't have an account? <a href="Registration.html">Register</a></p>
            </div>
        </form>
    </div>


    <script>
        let eyeicon = document.getElementById("eyeicon");
        let password = document.getElementById("password");

        eyeicon.onclick = function () {
            if (password.type === "password") {
                password.type = "text";
                eyeicon.classList.remove("fa-solid","fa-eye-slash");
                eyeicon.classList.add("fa-solid", "fa-eye");
            } else {
                password.type = "password";
                eyeicon.classList.remove("fa-solid", "fa-eye");
                eyeicon.classList.add("fa-solid","fa-eye-slash");
            }
        }
    </script>




    
    <script>
        document.getElementById('header').addEventListener('click', function() {
            window.location.href = 'Homepage.html';
        });
    </script>
</body>
</html>