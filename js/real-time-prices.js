// Funzione per ottenere i dati aggiornati
function fetchUpdatedData() {
    fetch('pages/update-prices.php')
        .catch(error => console.error('Errore durante il recupero dei dati:', error));
}

// Esegui la prima richiesta all'avvio della pagina
fetchUpdatedData();

// Esegui le richieste aggiornate ogni 10 secondi
setInterval(fetchUpdatedData, 10000);