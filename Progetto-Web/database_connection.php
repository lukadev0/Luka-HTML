<?php
$servername = "localhost:3306";
$username = "root";
$password = "";
$dbname = "animanga";

try {
    $connessione = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $connessione->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connessione riuscita";
} catch(PDOException $e) {
    die("Connessione fallita: " . $e->getMessage());
}

return $connessione;
?>