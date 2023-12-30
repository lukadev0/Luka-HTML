<?php
$verify = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $connection = require __DIR__ . '/database_connection.php'; // Connessione al database
    $username = $connection->quote($_POST['username']);
    $query = sprintf("SELECT * FROM utenti WHERE username = %s", $username);
    $stmt = $connection->query($query);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($_POST["password"], $user["password"])) {
        // Genera un token casuale
        $token = bin2hex(random_bytes(32)); // 32 byte di token

        // Salva il token nel database
        $updateTokenQuery = "UPDATE utenti SET token = :token WHERE id = :id";
        $updateTokenStmt = $connection->prepare($updateTokenQuery);
        $updateTokenStmt->bindParam(':token', $token, PDO::PARAM_STR);
        $updateTokenStmt->bindParam(':id', $user["id"], PDO::PARAM_INT);

        if ($updateTokenStmt->execute()) {
            // Log di successo
            echo "Token aggiornato con successo!";
        } else {
            // Log di errore
            echo "Errore nell'aggiornamento del token: " . implode(" ", $updateTokenStmt->errorInfo());
        }

        // Inizia la sessione
        session_start();
        session_regenerate_id();
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["token"] = $token;

        // Redirect dopo il login
        header("location: After_Register.php?id=" . $user["id"]);
        exit();
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
                <input id="username" name="username" type="text" placeholder="Username" required>
                <i class="fa-solid fa-user"></i>
            </div>

            <div class="input-box">
                <input id="password" name="password" type="password" placeholder="Password" required>
                <i class="fa-solid fa-eye-slash" id="eyeicon"></i>
            </div>

            <div class="remember-forgot">
                <label><input type="checkbox">Remember me</label>
                <a id="ForgotPassword" href="#">Forgot password?</a>
            </div>

            <?php if ($verify): ?>

                <div class="center-em">
                    <em>login not valid</em>
                </div>

            <?php endif; ?>

            <button type="submit" class="btn">Login</button>

            <div class="register-link">
                <p>Don't have an account? <a href="Registration.php">Register</a></p>
            </div>
        </form>
    </div>

    <script>
        let eyeicon = document.getElementById("eyeicon");
        let password = document.getElementById("password");

        eyeicon.onclick = function () {
            if (password.type === "password") {
                password.type = "text";
                eyeicon.classList.remove("fa-solid", "fa-eye-slash");
                eyeicon.classList.add("fa-solid", "fa-eye");
            } else {
                password.type = "password";
                eyeicon.classList.remove("fa-solid", "fa-eye");
                eyeicon.classList.add("fa-solid", "fa-eye-slash");
            }
        }
    </script>

    <script> //in caso di click su animanga
        document.getElementById('header').addEventListener('click', function () {
            window.location.href = 'Homepage.php';
        });
    </script>

    <script> //in caso di click forgotPassword
        document.getElementById('ForgotPassword').addEventListener('click', function () {
            window.location.href = 'Forgot_Password.php';
        });
    </script>
</body>
</html>
