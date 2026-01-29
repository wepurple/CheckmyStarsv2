document.addEventListener('DOMContentLoaded', function() {
    const liste = document.getElementById('selectEtoiles');

    let allData = [];

    fetch(`models/crud/getCriteresByEtoiles.php?star=${etoiles}`)

    liste.addEventListener('change', function() {
        
        const etoiles = this.value 
        return etoiles;
    });

    console.log(etoiles);


});