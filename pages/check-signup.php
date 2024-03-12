<?php
include 'connect-to-db.php';

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $stmt = $conn->prepare("INSERT INTO Users (name, surname, birthDate, country, city, street, streetNumber, phoneNumber, email, username, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("sssssssssss", $name, $surname, $birthdate, $country, $city, $street, $street_number, $phone_number, $email, $username, $password);

    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $birthdate = $_POST['birth-date'];
    $country = $_POST['country'];
    $city = $_POST['city'];
    $street = $_POST['street'];
    $street_number = $_POST['street-number'];
    $phone_number = $_POST['phone-number'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    $_SESSION['login-info'] = $username;

    if ($stmt->execute()) {
        header("Location: account.php");
        exit();
    } else {
        echo "Error inserting record: " . $stmt->error;
    }
    $stmt->close();

}

$conn->close();
?>