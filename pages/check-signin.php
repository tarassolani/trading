<?php
include 'connect-to-db.php';

$username = $_POST['username'];
$password = $_POST['password'];

$sql = "SELECT * FROM users WHERE username = ? AND password = ?";
$stmt = $conn->prepare($sql);

$stmt->bind_param("ss", $username, $password);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows > 0) {
    if ($_POST["remember"]) {
        setcookie("login-info", $username, time() + (7 * 24 * 60 * 60), "/");
    }
    else{
        session_start();
        $_SESSION["login-info"]= $username;
    }
    header("Location: account.php");
    exit();
} else {
    header("Location: signin.php?error=invalid");
    exit();
}

$stmt->close();
$conn->close();
?>