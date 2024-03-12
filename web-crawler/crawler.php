<?php
ini_set('display_errors', 1);

// Definizione della chiave API
$apiKey = "a3975305-35e9-47e3-baae-a04ab54de810";

// Impostazioni cURL
$ch = curl_init();

// Funzione per il decoding JSON con gestione degli errori
function json_decode_safe($response) {
    $data = json_decode($response, true);

    if (!$data) {
        echo "Errore di decodifica JSON: " . json_last_error_msg() . "\n";
        return null;
    }

    return $data;
}

// Loop per le pagine dei dati
for ($page = 1; $page <= 10000; $page++) {
    echo "**Pagina $page**\n";

    // Richiesta API
    echo "Richiesta API...\n";
    $url = "https://pro-api.coinmarketcap.com/v1/cryptocurrency/listings/latest?start=$page&limit=100&convert=USD&CMC_PRO_API_KEY=$apiKey";
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Accept: application/json'
    ));

    $response = curl_exec($ch);

    // Verifica degli errori
    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
        echo "Errore cURL: $error_msg\n";
        continue;
    }

    // Decodifica il JSON e gestisce la risposta
    $data = json_decode_safe($response);

    if (!$data) {
        // Gestione dell'errore di decodifica JSON
        continue;
    } else {
        // Elabora i dati delle criptovalute
        foreach ($data['data'] as $crypto) {
            echo "Elaborazione criptovaluta: " . $crypto['name'] . "\n";

            // Valori da inserire nel database
            $name = $crypto['name'];
            $supply = (int) $crypto['circulating_supply']; // Conversione in intero
            $coinCode = $crypto['symbol'];

            // Connessione al database
            echo "Connessione al database...\n";
            $conn = connectDB();

            // Query di inserimento
            $sql = "INSERT IGNORE INTO Crypto (name, supply, coinCode) VALUES (?, ?, ?)";

            // Preparazione della query
            echo "Preparazione query...\n";
            $stmt = $conn->prepare($sql);

            // Associazione dei valori alla query
            echo "Associazione dei valori alla query...\n";
            $stmt->bind_param("sis", $name, $supply, $coinCode); // "ssis" per i tipi di dati

            // Esecuzione della query
            echo "Esecuzione query...\n";
            $stmt->execute();

            // Verifica del risultato
            if ($stmt->error) {
                echo "Errore nell'inserimento di " . $crypto['name'] . ": " . $stmt->error . "\n";
            } else {
                echo "Dati per " . $crypto['name'] . " inseriti con successo.\n";
            }

            // Chiusura della query
            echo "Chiusura query...\n";
            $stmt->close();

            // Chiusura della connessione
            $conn->close();
        }
    }
}

// Chiude cURL
curl_close($ch);

echo "**Fine dello script**\n";

// Funzione per la connessione al database
function connectDB() {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "dbRegolare";

    // Crea la connessione
    echo "Creazione della connessione al database...\n";
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verifica della connessione
    if ($conn->connect_error) {
        die("Errore di connessione al database: " . $conn->connect_error);
    }

    return $conn;
}
?>
