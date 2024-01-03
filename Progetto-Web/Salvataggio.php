<?php
    // Registrazione lato server

    if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        die("You Need To Put a Valid Email!");
    }

    if (empty($_POST["username"])) {
        die("Username Required");
    }

    if (strlen($_POST["password"]) < 8) {
        die("Password needs to be a minimum of 8 characters long");
    }

    if (!preg_match("/[0-9]/", $_POST["password"])) {
        die("Password needs to contain at least one number.");
    }

    $connection = require __DIR__ . '/database_connection.php'; // Connessione al database

    $mail = $_POST["email"];
    $username = $_POST["username"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT); // Password criptata

    // Inserimento dati

    $query = "INSERT INTO utenti (email, username, password) VALUES (:email, :username, :password)";
    $stmt = $connection->prepare($query); // Prepariamo la query

    if (!$stmt) {
        die("SQL Error" . print_r($connection->errorInfo(), true));
    }

    // Associa i parametri
    $stmt->bindParam(':email', $mail, PDO::PARAM_STR);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->bindParam(':password', $password, PDO::PARAM_STR);

    $stmt->execute();
    $stmt->closeCursor();


    header("Location: Login.php");
?>
