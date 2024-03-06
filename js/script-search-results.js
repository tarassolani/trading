//All'avvio della pagina php index, esegue la query con AJAX
//Mostra tutti i risultati in maniera asincrona sotto alle crypto più cercate

document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.querySelector('.search-input');
    const searchResultsContainer = document.getElementById('search-results');

    searchInput.addEventListener('input', function () {
        var searchText = searchInput.value.trim();
        if (searchText.length > 0) {
            fetch(`pages/get-search-results.php?searchText=${searchText}`)
                .then(response => response.json())
                .then(data => {
                    displaySearchResults(data);
                })
                .catch(error => console.error('Error:', error));
        } else {
            clearSearchResults();
        }
    });

    //Funzione che mostra tutti i risultati di ricerca
    function displaySearchResults(results) {
        clearSearchResults();

        results.forEach(crypto => {
            const tr = searchResultsContainer.insertRow();
            tr.className = 'search-result-item';

            const td = tr.insertCell();
            td.className='td-crypto-img';
            const img = document.createElement('img');
            img.src = 'data:image/png;base64,' + crypto.Icon;
            img.alt = crypto.coinCode;
            img.className = 'crypto-img';
            td.appendChild(img);

            const td2 = tr.insertCell();
            td2.textContent = crypto.coinCode;
            td2.className = 'td-crypto-code';

            const td3 = tr.insertCell();
            td3.textContent = crypto.name;
            td3.className = 'td-crypto-name';

            const td4 = tr.insertCell();
            td4.textContent = `Supply: ${crypto.supply}`;
            td4.className = 'td-crypto-supply';

            searchResultsContainer.style.visibility = "visible";
        });
    }

    //Funzione che pulisce i risultati di ricerca, quando l'input è vuoto
    function clearSearchResults() {
        searchResultsContainer.innerHTML = '';
        searchResultsContainer.style.visibility = "hidden";
    }
});