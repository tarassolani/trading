<?php
include 'connect-to-db.php';
session_start();

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