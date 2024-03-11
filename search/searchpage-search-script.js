//All'avvio della pagina php index, esegue la query con AJAX
//Mostra tutti i risultati in maniera asincrona sotto alle crypto più cercate

document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.querySelector('.search-input');
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    const searchText = urlParams.get('search');

    console.log(searchText);
    //Inserisco nella searchbox il testo della searchbox precedente
    if (searchText) {
        searchInput.value = searchText;
        getSearchResults(searchText);
    }
    const searchResultsContainer = document.getElementById('search-results-wider');

    searchInput.addEventListener('input', function () {
        var currentSearchText = searchInput.value.trim();
        if (currentSearchText.length > 0) {
            getSearchResults(currentSearchText);
        } else {
            clearSearchResults();
        }
    });

    function getSearchResults(searchText){
        fetch(`searchpage-get-search-results.php?searchText=${searchText}`) //Chiamata al file php che ritorna l'encode in json di tutte le informazioni
        .then(response => response.json())
        .then(data => {
            displaySearchResults(data);
        })
        .catch(error => console.error('Error:', error));
    }

    //Funzione che mostra tutti i risultati di ricerca
    function displaySearchResults(results) {
        clearSearchResults();

        results.forEach(crypto => {
            const tr = searchResultsContainer.insertRow();
            tr.addEventListener("click", () => { //Al click sulla riga della tabella, vengo reindirizzato alla pagina individuale della crypto
                window.location.href = `../pages/crypto-info.php?coinCode=${crypto.coinCode}`;
              });
            tr.className = 'search-result-item';

            const td = tr.insertCell();
            td.className='td-det-crypto-img';
            const img = document.createElement('img');
            img.src = 'data:image/png;base64,' + crypto.Icon;//Immagine, cioè BLOB del database
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
            td5.className = 'td-det-crypto-variation';

            if(`${crypto.percent_change}` > 0){//Stile variazione + prezzo, in base al segno + o - della variazione percentuale
                span.textContent = `+${crypto.percent_change}%`;
                span.classList.add('highlight-green');
                td4.className = 'td-det-crypto-price-green';
            }
            else{
                span.textContent = `${crypto.percent_change}%`;
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