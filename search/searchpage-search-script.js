//All'avvio della pagina php index, esegue la query con AJAX
//Mostra tutti i risultati in maniera asincrona sotto alle crypto più cercate

document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.querySelector('.search-input');
    const searchResultsContainer = document.getElementById('search-results-wider');

    searchInput.addEventListener('input', function () {
        var searchText = searchInput.value.trim();
        if (searchText.length > 0) {
            fetch(`searchpage-get-search-results.php?searchText=${searchText}`)
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
            td.className='td-det-crypto-img';
            const img = document.createElement('img');
            img.src = 'data:image/png;base64,' + crypto.Icon;
            img.alt = crypto.coinCode;
            img.className = 'crypto-img';
            td.appendChild(img);

            const td2 = tr.insertCell();
            td2.textContent = crypto.coinCode;
            td2.className = 'td-det-crypto-code';

            const td3 = tr.insertCell();
            td3.textContent = crypto.name;
            td3.className = 'td-det-crypto-name';

            const td4 = tr.insertCell();
            td4.textContent = "69,000 USDT";         //METTERE PREZO
            td4.className = 'td-det-crypto-price';

            const td5 = tr.insertCell();
            td5.textContent = "+42.0%";         //METTERE VARIETION
            td5.className = 'td-det-crypto-variation';

            const td6 = tr.insertCell();
            td6.textContent = `Supply: ${crypto.supply}`;
            td6.className = 'td-det-crypto-supply';

            //searchResultsContainer.style.visibility = "visible";
        });

    }

    //Funzione che pulisce i risultati di ricerca, quando l'input è vuoto
    function clearSearchResults() {
        searchResultsContainer.innerHTML = '<tr id="top-row"><th colspan="3" id="th-crypto-img">Crypto coin</th><th id="th-crypto-price">Price</th><th id="th-crypto-variation">24h variation</th><th id="th-crypto-supply">Supply</th></tr>';
        //searchResultsContainer.style.visibility = "hidden";
    }
});