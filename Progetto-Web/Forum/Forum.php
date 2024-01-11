<?php

define('DEBUG', false);

function handlePostRequestPost($table, $json_data, $pdo) {
    $set = json_decode($json_data, true);

    // Rimuovi caratteri non validi dai nomi delle colonne
    $columns = array_map(function ($column) {
        return preg_replace('/[^a-z0-9_]+/i', '', $column);
    }, array_keys($set));

    // Usa prepared statements per evitare SQL injection
    $values = array_map(function ($value) use ($pdo) {
        return $pdo->quote($value);
    }, array_values($set));

    $columns_string = implode(', ', $columns);
    $values_string = implode(', ', $values);

    $sql = "INSERT INTO `$table` ($columns_string) VALUES ($values_string)";
   

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $lastInsertId = $pdo->lastInsertId();
        $response = array('status' => 'success', 'message' => 'INSERT OK', 'inserted_id' => $lastInsertId);
        header('Content-Type: application/json');
        echo json_encode($response);
    } catch (PDOException $e) {
        http_response_code(500);  // Internal Server Error
        $response = array('status' => 'error', 'message' => $e->getMessage());
        header('Content-Type: application/json');
        echo json_encode($response);
    }
}

function handlePutCounterUpdate($table, $key, $pdo) {
    $input = json_decode(file_get_contents('php://input'), true);

    // Verifica se l'input contiene il campo post_count
    if (isset($input['post_count'])) {
        // Estrai il valore corrente del post_count dal database
        $currentPostCount = getCurrentPostCount($table, $key, $pdo);
    
        // Aggiungi il valore del campo post_count all'attuale contatore
        $newPostCount = $currentPostCount + $input['post_count'];
    
        // Aggiorna il valore del post_count nel database
        updatePostCount($table, $key, $newPostCount, $pdo);
    
        // Rispondi con successo
        $response = array('status' => 'success', 'message' => 'Post count updated successfully');
        header('Content-Type: application/json');
        echo json_encode($response);
    } else {
        // Rispondi con errore se il campo post_count non è presente nell'input
        http_response_code(400); // Bad Request
        $response = array('status' => 'error', 'message' => 'Missing post_count field in the request');
        header('Content-Type: application/json');
        echo json_encode($response);
    }
}

function getCurrentPostCount($table, $key, $pdo) {
    // Query per ottenere il valore corrente del post_count
    $sql = "SELECT post_count FROM `$table` WHERE id = " . $pdo->quote($key);
    $result = $pdo->query($sql);
    
    // Estrai il valore del post_count dal risultato della query
    $currentPostCount = $result->fetchColumn();

    return $currentPostCount;
}

function updatePostCount($table, $key, $newPostCount, $pdo) {
    // Query per aggiornare il valore del post_count
    $sql = "UPDATE `$table` SET post_count = " . $pdo->quote($newPostCount) . " WHERE id = " . $pdo->quote($key);

    // Esegui la query di aggiornamento
    $pdo->exec($sql);
}

function handleDeletePost($table, $key, $pdo) {
    $sql = "DELETE FROM `$table` WHERE id = " . $pdo->quote($key);
    try {
        $statement = $pdo->query($sql);
        $response = array('status' => 'success', 'message' => 'DELETE OK');
        header('Content-Type: application/json');
        echo json_encode($response);
    } catch (PDOException $e) {
        http_response_code(404);
        $response = array('status' => 'error', 'message' => $e->getMessage());
        header('Content-Type: application/json');
        echo json_encode($response);
    }
}



// get the HTTP method, path, and body of the request
$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'], '/'));
$input = json_decode(file_get_contents('php://input'), true);

// connect to the mysql database
$connessione = require __DIR__ . "/../database_connection.php";

// retrieve the table and key from the path
$table = preg_replace('/[^a-z0-9_]+/i', '', array_shift($request));
$_key = array_shift($request);
$key = $_key;
//$key = $_key + 0;

// escape the columns and values from the input object
if (isset($input)) {
    $columns = preg_replace('/[^a-z0-9_]+/i', '', array_keys($input));
    $values = array_map(function ($value) use ($connessione) {
        if ($value === null) return null;
        return $connessione->quote($value);
    }, array_values($input));
}

// build the SET part of the SQL command
if (isset($input)) {
    $set = '';
    for ($i = 0; $i < count($columns); $i++) {
        $set .= ($i > 0 ? ',' : '') . '`' . $columns[$i] . '`=';
        $set .= ($values[$i] === null ? 'NULL' : '"' . $values[$i] . '"');
    }
}

// create SQL based on HTTP method
switch ($method) {
    case 'POST':
    handlePostRequestPost($table, file_get_contents('php://input'), $connessione);
    break;
    
    case 'PUT':
    handlePutCounterUpdate($table, $key, $connessione);
    getCurrentPostCount($table, $key, $pdo);
    updatePostCount($table, $key, $newPostCount, $pdo);
    break;
    
    case 'DELETE':
    handleDeletePost($table, $key, $connessione);
    break;
}
