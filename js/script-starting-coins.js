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
                cryptoDesc.className = 'crypto-descr';
                cryptoDesc.textContent = '+12,1%';

                var cryptoSlotElement = document.createElement('div');
                cryptoSlotElement.className = 'crypto-slot';
                cryptoSlotElement.appendChild(cryptoImg);
                cryptoSlotElement.appendChild(cryptoCode);
                cryptoSlotElement.appendChild(document.createElement('br'));
                cryptoSlotElement.appendChild(cryptoDesc);

                document.querySelector('.crypto-slots').appendChild(cryptoSlotElement);
            });
        })
        .catch(error => console.error('Error:', error));
});