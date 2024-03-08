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
            td4.textContent = `$${crypto.price}`;

            const td5 = tr.insertCell();
            const span = document.createElement('span');
            span.textContent = `${crypto.percent_change}%`;
            td5.className = 'td-det-crypto-variation';

            if(`${crypto.percent_change}` > 0){
                span.classList.add('highlight-green');
                td4.className = 'td-det-crypto-price-green';
            }
            else{
                span.classList.add('highlight-red');
                td4.className = 'td-det-crypto-price-red';
            }
            td5.appendChild(span);

            const td6 = tr.insertCell();
            td6.textContent = `${crypto.supply}`;
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