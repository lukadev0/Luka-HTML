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
            <a href="Forum/Not_Logged_In_Forum.php">Forum</a>
            <a href="Contact.php">Contact</a>
            <button id="btnLogin" class="btnLogin-popup">Login</button>

        </nav>
    </header>

    <div class="content">
        <small>Welcome to our</small>
        <h1>AniManga Forum</h1>

    </div>

    <script> //in caso di pressione sul bottone login andiamo e accediamo alla sezione login
        document.getElementById('btnLogin').addEventListener('click', function() {
            window.location.href = 'Login.php';
        });
    </script>
</body>
</html>