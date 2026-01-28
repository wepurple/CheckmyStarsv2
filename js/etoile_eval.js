document.addEventListener('DOMContentLoaded', function() {
    const liste = document.getElementById('selectEtoiles');

    liste.addEventListener('change', function() {
        
        const etoiles = this.value 
        console.log(etoiles);

    });
});