//Listener del caricamento del file
//Una volta caricato il file, si leggono i risultati della query di 'getstartingoincs.php'
//I risultati della query si convertono in immagine/elementi html e vengono aggiunti alla pagina
document.addEventListener('DOMContentLoaded', function () {
    fetch(`pages/get-starting-coins.php`)
        .then(response => response.json())
        .then(data => {
            data.forEach(crypto => {
                var cryptoImg = document.createElement('img');
                cryptoImg.src = 'data:image/png;base64,' + crypto.Icon;
                cryptoImg.alt = crypto.coinCode;
                cryptoImg.className = 'crypto-img';

                var cryptoCode = document.createElement('span');
                cryptoCode.className = 'crypto-code';
                cryptoCode.textContent = crypto.coinCode;

                var cryptoDesc = document.createElement('span');
                
                if(`${crypto.percent_change}` > 0){//Colore del prezzo che cambia in base al segno + o - della variazione percentuale
                    cryptoDesc.textContent = `+${crypto.percent_change}%`;
                    cryptoDesc.className = 'td-det-crypto-price-green';
                }
                else{
                    cryptoDesc.textContent = `${crypto.percent_change}%`;
                    cryptoDesc.className = 'td-det-crypto-price-red';
                }

                var cryptoSlotElement = document.createElement('div');
                cryptoSlotElement.className = 'crypto-slot';
                cryptoSlotElement.addEventListener("click", () => {//Al click sullo slot, si apre la pagina individuale della crypto
                    window.location.href = `pages/crypto-info.php?coinCode=${crypto.coinCode}`;
                  });
                cryptoSlotElement.appendChild(cryptoImg);
                cryptoSlotElement.appendChild(cryptoCode);
                cryptoSlotElement.appendChild(document.createElement('br'));
                cryptoSlotElement.appendChild(cryptoDesc);

                document.querySelector('.crypto-slots').appendChild(cryptoSlotElement);
            });
        })
        .catch(error => console.error('Error:', error));
});