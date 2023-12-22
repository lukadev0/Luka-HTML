<?php
$servername = "localhost:3306";
$username = "root";
$password = "";
$dbname = "animanga";

// Connessione al database
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica della connessione
if ($conn->connect_error) {
    die("Connessione al database fallita: " . $conn->connect_error);
} else {
    echo "Connessione al database riuscita!";
}

$conn->close();
?>
