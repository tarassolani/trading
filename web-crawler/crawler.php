<?php
ini_set('display_errors', 1);

// API key definition
$apiKey = "a3975305-35e9-47e3-baae-a04ab54de810";

// cURL settings
$ch = curl_init();

// Function for JSON decoding with error handling
function json_decode_safe($response) {
    $data = json_decode($response, true);

    if (!$data) {
        echo "JSON decoding error: " . json_last_error_msg() . "\n";
        return null;
    }

    return $data;
}

// Loop for data pages
for ($page = 1; $page <= 10000; $page++) {
    echo "**Page $page**\n";

    // API request
    echo "API request...\n";
    $url = "https://pro-api.coinmarketcap.com/v1/cryptocurrency/listings/latest?start=$page&limit=100&convert=USD&CMC_PRO_API_KEY=$apiKey";
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Accept: application/json'
    ));

    $response = curl_exec($ch);

    // Check for errors
    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
        echo "cURL error: $error_msg\n";
        continue;
    }

    // Decode the JSON and handle the response
    $data = json_decode_safe($response);

    if (!$data) {
        // Handle JSON decoding error
        continue;
    } else {
        // Process cryptocurrency data
        foreach ($data['data'] as $crypto) {
            echo "Processing cryptocurrency: " . $crypto['name'] . "\n";

            // Values to insert into the database
            $name = $crypto['name'];
            $supply = (int) $crypto['circulating_supply']; // Conversion to integer
            $coinCode = $crypto['symbol'];

            // Database connection
            echo "Connecting to the database...\n";
            $conn = connectDB();

            // Insert query
            $sql = "INSERT IGNORE INTO Crypto (name, supply, coinCode) VALUES (?, ?, ?)";

            // Query preparation
            echo "Preparing query...\n";
            $stmt = $conn->prepare($sql);

            // Bind values to the query
            echo "Binding values to the query...\n";
            $stmt->bind_param("sis", $name, $supply, $coinCode); // "ssis" for data types

            // Query execution
            echo "Executing query...\n";
            $stmt->execute();

            // Check the result
            if ($stmt->error) {
                echo "Error inserting " . $crypto['name'] . ": " . $stmt->error . "\n";
            } else {
                echo "Data for " . $crypto['name'] . " inserted successfully.\n";
            }

            // Close the query
            echo "Closing query...\n";
            $stmt->close();

            // Close connection
            $conn->close();
        }
    }
}

// Close cURL
curl_close($ch);

echo "**End of script**\n";

// Function to connect to the database
function connectDB() {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "dbRegolare";

    // Create connection
    echo "Creating database connection...\n";
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Database connection error: " . $conn->connect_error);
    }

    return $conn;
}
?>