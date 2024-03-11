<?php
include 'connect-to-db.php';
header('Content-Type: application/json');

session_start();

$username = $_GET['username'];

// Controllo se l'amicizia esiste già
$sql_check_friendship = "SELECT * FROM Friends WHERE (friend1 = ? AND friend2 = ?) OR (friend1 = ? AND friend2 = ?)";
$stmt_check_friendship = $conn->prepare($sql_check_friendship);
$stmt_check_friendship->bind_param('ssss', $_SESSION['login-info'], $username, $username, $_SESSION['login-info']);
$stmt_check_friendship->execute();
$stmt_check_friendship->store_result();

if ($stmt_check_friendship->num_rows > 0) {
    // L'amicizia esiste già, restituisci un errore
    $response = ["success" => false, "error" => "You are already friends with '$username'"];
} else {
    // Se l'amicizia non esiste già, procedi con l'inserimento dell'amicizia
    $sql_insert_friendship = "INSERT INTO Friends(friend1, friend2) VALUES (?,?)";
    $stmt_insert_friendship = $conn->prepare($sql_insert_friendship);
    $stmt_insert_friendship->bind_param('ss', $_SESSION['login-info'], $username);

    if ($stmt_insert_friendship->execute()) {
        $response = ["success" => true];
    } else {
        $response = ["success" => false, "error" => $conn->error];
    }

    $stmt_insert_friendship->close();
}

$stmt_check_friendship->close();
$conn->close();

echo json_encode($response);
?>