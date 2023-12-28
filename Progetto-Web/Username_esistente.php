<?php

$connessione = require __DIR__ . "/database_connection.php";

$sql = "SELECT * FROM utenti WHERE username = :username";

$stmt = $connessione->prepare($sql);
$stmt->bindParam(':username', $_GET["username"], PDO::PARAM_STR);
$stmt->execute();

// Fetch dei risultati
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Verifica se l'email è disponibile
$is_available = empty($result);

header("Content-Type: application/json");

echo json_encode(["available" => $is_available]);