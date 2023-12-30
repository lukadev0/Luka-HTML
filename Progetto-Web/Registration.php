<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <link rel="stylesheet" href="Registration_style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://kit.fontawesome.com/186eb98a62.js" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/just-validate@latest/dist/just-validate.production.min.js" defer></script>
    <script src="Convalida.js" defer></script>
</head>
<body>

    <header id="header"><h2>AniManga</h2></header>    
    

    <div class="wrapper">
        <form action="Salvataggio.php" method="post" id = "registrazione">
            
            <h1>Registration</h1>
            <div class="input-box">
                <input id = "email" name="email" type="email" placeholder="Email" required>
                <i class='bx bxs-envelope'></i>

            </div>

            <div class="input-box">
                <input id = "username" name="username" type="text" placeholder="Username" required>
                <i class='bx bxs-user'></i>
            </div>

            <div class="input-box">
                <input id = "password" name="password" type="password" placeholder="Password" required>
                <i class="fa-solid fa-eye-slash" id="eyeicon"></i>
            </div>

            <button type = "submit" class="btn">Register</button>

            <div class="login-link">
                <p>Already have an account? <a href="Login.php">Login</a></p>
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



    <script>        //in caso di click su logo
        document.getElementById('header').addEventListener('click', function() {
            window.location.href = 'Homepage.php';
        });
    </script>
</body>
</html>