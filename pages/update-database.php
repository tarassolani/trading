<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbRegolare";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$receivedHash = $_POST['hash'];

$receivedHash = substr($receivedHash, 0, 50);

$userID = $_SESSION['login-info'] ?? $_COOKIE['login-info'] ?? null;

if ($userID) {
    $sql = "UPDATE Users SET hash = ? WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $receivedHash, $userID);

    if (!$stmt->execute()) {
        trigger_error("Can't update database", E_USER_ERROR);
    }
    $stmt->close();
}

$conn->close();
?>