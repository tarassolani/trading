function copyContent() {
    var walletHash = document.getElementById("wallet-hash").textContent;

    navigator.clipboard.writeText(walletHash)
        .then(function () {
            alert("Wallet hash copied!");
        })
        .catch(function (error) {
            console.error('Copy failed: ', error);
        });
}


function createWallet() {
    var walletButton = document.getElementById("wallet-button");

    walletButton.disabled = true;
    walletButton.classList.add("loading");
    var loader = document.getElementById("loader");
    loader.style.display = "inline-block";

    setTimeout(function () {
        var uniqueHash = generateUniqueHash();

        var hashContainer = document.createElement("span");
        hashContainer.id = "wallet-hash";
        hashContainer.textContent = uniqueHash;

        var walletInfo = document.querySelector(".wallet-info");
        walletButton.style.display = "none";
        walletInfo.appendChild(hashContainer);

        updateDatabaseWithHash(uniqueHash);

        document.getElementById("btn-inviz").style.visibility = "visible";
    }, 2000);

}

function generateUniqueHash() {
    var characters = 'ABCDEF0123456789';
    var hash = '';

    for (var i = 0; i < 50; i++) {
        hash += characters.charAt(Math.floor(Math.random() * characters.length));
    }

    return hash;
}

function updateDatabaseWithHash(hash) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', '../pages/update-database.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            console.log('Database updated successfully.');
        }
    };
    xhr.send('hash=' + hash);
}

document.addEventListener('DOMContentLoaded', function () {
    const searchResultsContainer = document.getElementById('search-results-wider');

    fetch(`get-user-positions.php`)
        .then(response => response.json())
        .then(data => {
            if ('message' in data && data.message === 'No positions found for the user') {
                //Quando non trovo nessuna posizione, non stampo nulla a schermo, ma comunico nella console
                console.log('No positions found for the user');
            } else {
                //Se ci sono posizioni, elaboro i dati
                displaySearchResults(data);
            }
        })
        .catch(error => console.error('Error:', error));

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
            td.className = 'td-det-crypto-img';
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

            if (`${crypto.percent_change}` > 0) {//Stile variazione + prezzo, in base al segno + o - della variazione percentuale
                span.textContent = `+${crypto.percent_change}%`;
                span.classList.add('highlight-green');
                td4.className = 'td-det-crypto-price-green';
            }
            else {
                span.textContent = `${crypto.percent_change}%`;
                span.classList.add('highlight-red');
                td4.className = 'td-det-crypto-price-red';
            }
            td5.appendChild(span);

            const td6 = tr.insertCell();
            td6.textContent = `${crypto.amount}`;
            td6.className = 'td-det-crypto-amount';
        });
    }

    //Funzione che pulisce i risultati di ricerca, quando l'input è vuoto
    function clearSearchResults() {
        searchResultsContainer.innerHTML = '';
        searchResultsContainer.style.visibility = "hidden";
    }



});