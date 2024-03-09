// Funzione per ottenere i dati aggiornati
function fetchUpdatedData() {
    fetch(`../pages/update-prices.php`)
        .catch(error => console.error('Errore durante il recupero dei dati:', error));
}

fetchUpdatedData();

setInterval(fetchUpdatedData, 10000);