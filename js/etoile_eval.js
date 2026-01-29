document.addEventListener('DOMContentLoaded', function() {
    let liste = document.getElementById('selectEtoiles');

    let allData = [];

    liste.addEventListener('change', function() {
        
        let etoiles = this.value 
        
        fetch(`models/crud/getCriteresByEtoile.php?star=${etoiles}`)
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

        console.log(etoiles);

    return etoiles;

    });

});