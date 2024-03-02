<?php
ini_set('display_errors', 1);

// Function to upload a PNG image to the database
function uploadImage($filename, $cryptoName) {
    // Database connection
    $conn = connectDB();

    // Check file
    if (!file_exists($filename)) {
        echo "Error: File " . $filename . " not found.\n";
        return;
    }

    // Load image
    $imageData = file_get_contents($filename);

    // Update query
    $sql = "UPDATE Crypto SET icon = ? WHERE coinCode = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(1, $imageData, PDO::PARAM_LOB);
    $stmt->bindParam(2, $cryptoName);
    
    // Execute query
    if ($stmt->execute()) {
        echo "Image for " . $cryptoName . " updated successfully.\n";
    } else {
        echo "Error updating " . $cryptoName . ": " . $stmt->errorInfo()[2] . "\n";
    }

    // Close statement and connection
    $stmt->closeCursor();
    $conn = null;
}

// Function to connect to the database
function connectDB() {
    // Database credentials
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "dbRegolare";

    // Create connection
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "Database connection successful\n";
        return $conn;
    } catch(PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}

// Images directory
$dir = "icons/";

// List files
$files = array_diff(scandir($dir), array('.', '..'));

// Loop to upload images
foreach ($files as $file) {
    // Extract cryptocurrency name
    $cryptoName = pathinfo($file, PATHINFO_FILENAME);

    // Upload image
    uploadImage($dir . $file, $cryptoName);
}
?>