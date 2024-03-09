<?php
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

$userID = $_SESSION['user_id'] ?? $_COOKIE['user_id'] ?? null;

if ($userID) {
    $sql = "UPDATE users SET hash = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $receivedHash, $userID);

    if ($stmt->execute()) {
        echo "Hash updated successfully.";
    } else {
        echo "Error while updating database: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "User ID not valid.";
}

$conn->close();
?>