<?php
// Definizione API key
$apiKey = "a3975305-35e9-47e3-baae-a04ab54de810";

// Impostazioni cURL
$ch = curl_init();

// Funzione per la decodifica JSON con gestione errori
function json_decode_safe($response) {
    $data = json_decode($response, true);

    if (!$data) {
        echo "Errore nella decodifica JSON: " . json_last_error_msg() . "\n";
        return null;
    }

    return $data;
}

// Ciclo per le pagine di dati
for ($page = 1; $page <= 10; $page++) {
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

    // Controlla eventuali errori
    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
        echo "Errore cURL: $error_msg\n";
        continue;
    }

    // Decodifica il JSON e gestisci la risposta
    $data = json_decode_safe($response); 

    if (!$data) {
        // Gestisci errore di decodifica del JSON
        continue;
    } else {
        // Processa i dati delle criptovalute
        foreach ($data['data'] as $crypto) {
            echo "Elaborazione criptovaluta: " . $crypto['name'] . "\n";

            // Valori da inserire nel database
            $name = $crypto['name'];
            //$icon = file_get_contents($crypto['logo']); // Sostituisci con URL dell'icona
            $project = $crypto['description']; // Potrebbe essere necessario un parsing
            $supply = (int) $crypto['circulating_supply'];
            $coinCode = $crypto['symbol'];

            // Connessione al database
            echo "Connessione al database...\n";
            $conn = connectDB();

            // Query per l'inserimento
            $sql = "INSERT INTO Crypto(name, project, supply, coinCode) VALUES (?, ?, ?, ?)";

            // Preparazione della query
            echo "Preparazione della query...\n";
            $stmt = $conn->prepare($sql);

            // Bind dei valori alla query
            echo "Bind dei valori alla query...\n";
            $stmt->bind_param("ssis", $name, $project, $supply, $coinCode);

            // Esecuzione della query
            echo "Esecuzione della query...\n";
            $stmt->execute();

            // Controllo dell'esito
            if ($stmt->error) {
                echo "Errore durante l'inserimento di ". $crypto['name'] . ": " . $stmt->error . "\n";
            } else {
                echo "Dati per " . $crypto['name'] . " inseriti correttamente.\n";
            }

            // Chiusura della query
            echo "Chiusura della query...\n";
            $stmt->close();

            // Chiusura connessione
            $conn->close();
        }
    }
}

// Chiusura cURL
curl_close($ch);

echo "**Fine script**\n";

// Funzione per la connessione al database
function connectDB() {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "dbRegolare";

    // Creazione della connessione
    echo "Creazione connessione al database...\n";
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Controllo della connessione
    if ($conn->connect_error) {
        die("Errore di connessione al database: " . $conn->connect_error);
    }

    return $conn;
}
?>