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
            tr.addEventListener("click", () => {
                window.location.href = `pages/crypto-info.php?coinCode=${crypto.coinCode}`;
              });
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
            td4.textContent = `$${crypto.price}`; 
            td4.className = 'td-crypto-price';

            const td5 = tr.insertCell();
            const span = document.createElement('span');
            span.textContent = `${crypto.percent_change}%`; 

            if(`${crypto.percent_change}` > 0){
                span.classList.add('highlight-green');
            }
            else{
                span.classList.add('highlight-red');
            }
            td5.appendChild(span); 
            td5.className = 'td-crypto-price';

            searchResultsContainer.style.visibility = "visible";
        });
    }

    //Funzione che pulisce i risultati di ricerca, quando l'input è vuoto
    function clearSearchResults() {
        searchResultsContainer.innerHTML = '';
        searchResultsContainer.style.visibility = "hidden";
    }
});