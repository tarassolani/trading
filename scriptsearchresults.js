//All'avvio della pagina php index, esegue la query con AJAX
//Mostra tutti i risultati in maniera asincrona sotto alle crypto più cercate

document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.querySelector('.search-input');
    const searchResultsContainer = document.getElementById('search-results');

    searchInput.addEventListener('input', function () {
        var searchText = searchInput.value.trim();
        if (searchText.length > 0) {
            fetch(`getsearchresults.php?searchText=${searchText}`)
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
            const resultItem = document.createElement('div');
            resultItem.className = 'search-result-item';

            const imgElement = document.createElement('img');
            imgElement.src = 'data:image/png;base64,' + crypto.Icon;
            imgElement.alt = crypto.coinCode;
            imgElement.className = 'crypto-img';

            const coinCodeElement = document.createElement('span');
            coinCodeElement.textContent = crypto.coinCode;
            coinCodeElement.className = 'crypto-code';

            const nameElement = document.createElement('span');
            nameElement.textContent = crypto.name;
            nameElement.className = 'crypto-name';

            const supplyElement = document.createElement('span');
            supplyElement.textContent = `Supply:  ${crypto.supply}`;
            supplyElement.className = 'crypto-supply';

            resultItem.appendChild(imgElement);
            resultItem.appendChild(coinCodeElement);
            resultItem.appendChild(nameElement);
            resultItem.appendChild(supplyElement);

            searchResultsContainer.appendChild(resultItem);
            searchResultsContainer.style.visibility = "visible";
        });
    }

    //Funzione che pulisce i risultati di ricerca, quando l'input è vuoto
    function clearSearchResults() {
        searchResultsContainer.innerHTML = '';
        searchResultsContainer.style.visibility = "hidden";
    }
});