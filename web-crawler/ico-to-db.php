<?php
ini_set('display_errors', 1);

// Funzione per caricare un'immagine PNG nel database
function uploadImage($filename, $nomeCriptovaluta) {
    include '../pages/connect-to-db.php';

    // Verifica file
    if (!file_exists($filename)) {
        echo "Errore: File " . $filename . " non trovato.\n";
        return;
    }

    // Carica l'immagine
    $imageData = file_get_contents($filename);

    // Query di aggiornamento
    $sql = "UPDATE Crypto SET icon = ? WHERE coinCode = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(1, $imageData, PDO::PARAM_LOB);
    $stmt->bindParam(2, $nomeCriptovaluta);
    
    // Esegui la query
    if ($stmt->execute()) {
        echo "Immagine per " . $nomeCriptovaluta . " aggiornata con successo.\n";
    } else {
        echo "Errore nell'aggiornamento di " . $nomeCriptovaluta . ": " . $stmt->errorInfo()[2] . "\n";
    }

    // Chiudi statement e connessione
    $stmt->closeCursor();
    $conn = null;
}

// Directory delle immagini
$dir = "icons/";

// Elenco dei file
$files = array_diff(scandir($dir), array('.', '..'));

// Loop per caricare le immagini
foreach ($files as $file) {
    // Estrarre il nome della criptovaluta
    $nomeCriptovaluta = pathinfo($file, PATHINFO_FILENAME);

    // Carica l'immagine
    uploadImage($dir . $file, $nomeCriptovaluta);
}
?>