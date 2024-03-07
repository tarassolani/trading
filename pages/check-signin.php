<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbRegolare";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $_POST['username'];
$password = $_POST['password'];

$sql = "SELECT * FROM users WHERE username = ? AND password = ?";
$stmt = $conn->prepare($sql);

$stmt->bind_param("ss", $username, $password);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows > 0) {
    if ($_POST["remember"]) {
        $login_info = array(
            "username" => $username,
            "password" => $password
        );
        $serialized_login_info = serialize($login_info);
        setcookie("login-info", $serialized_login_info, time() + (7 * 24 * 60 * 60), "/");
    }
    header("Location: "); //pagina taras :)
    exit();
} else {
    header("Location: signin.php?error=invalid");
    exit();
}

$stmt->close();
$conn->close();
?>