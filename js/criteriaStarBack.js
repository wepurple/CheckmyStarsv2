// Récupère le numéro d'étoile depuis l'URL
const urlParams = new URLSearchParams(window.location.search);
const star = urlParams.get('star') || 1;

// Fetch les données
fetch(`models/crud/getCriteriaByEtoile.php?star=${star}`)
    .then(response => response.json())
    .then(data => {

        setupSearch();

        const tbody = document.getElementById('table-body');
        
        data.forEach(critere => {
            const row = tbody.insertRow();
            row.innerHTML = `
                <td>${critere.Critere_ID}</td>
                <td>${critere.Critere_description}</td>
                <td>${critere.Critere_statut}</td>
                <td>${critere.Critere_points || '-'}</td>
            `;
        });
    })
    .catch(error => {
        console.error('Erreur:', error);
        document.getElementById('table-body').innerHTML = 
            '<tr><td colspan="4" class="text-danger">Erreur de chargement</td></tr>';
    });

// Fonction de recherche dynamique
function setupSearch() {
    const searchInput = document.getElementById('searchInput');
    
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('#table-body tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });
}
