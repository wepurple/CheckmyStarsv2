document.addEventListener('DOMContentLoaded', function() {
    let liste = document.getElementById('selectEtoiles');

    let allData = [];

    fetch(`models/crud/getCriteresByEtoiles.php?star=${etoiles}`)
    .then(response => {
        console.log('Reponse fetch; ', response);
        return response.json();
    })
    .then(data => {
        console.log("Données recues: ", data);
        allData = data;
        displayDara(data);
        setupFilters(data);
    })
    .catch(error => console.log("Erreur fetch : ", error));

    liste.addEventListener('change', function() {
        
        let etoiles = this.value 
        return etoiles;
    });

    console.log(etoiles);


});