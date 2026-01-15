// Récupère le numéro d'étoile depuis l'URL
const urlParams = new URLSearchParams(window.location.search);
const star = urlParams.get('star') || 1;

let allCriteria = []; // Stocke les données globalement

// Fetch les données
fetch(`models/crud/getCriteriaByEtoile.php?star=${star}`)
    .then(response => response.json())
    .then(data => {
        console.log('Données reçues:', data); // ← AJOUTE ÇA
        console.log('Type:', typeof data, 'Longueur:', data.length); // ← ET ÇA
        
        allCriteria = data;
        renderTable(data);
        setupSearch();
    })
    .catch(error => {
        console.error('Erreur:', error);
        document.getElementById('table-body').innerHTML = 
            '<tr><td colspan="4" class="text-danger">Erreur de chargement</td></tr>';
    });


// Fonction pour afficher le tableau
function renderTable(data) {
    const tbody = document.getElementById('table-body');
    tbody.innerHTML = ''; // Vide le tableau
    
    data.forEach(critere => {
        const row = tbody.insertRow();
        row.innerHTML = `
            <td>${critere.Critere_ID}</td>
            <td>${critere.Critere_description}</td>
            <td>${critere.Critere_statut}</td>
            <td>${critere.Critere_points || '-'}</td>
        `;
    });
}

// Fonction de recherche dynamique
function setupSearch() {
    const searchInput = document.getElementById('recherche'); // ← Ton ID réel
    const searchType = document.getElementById('type');
    
    function filterTable() {
        const query = searchInput.value.trim().toLowerCase();
        const type = searchType.value;
        
        if (!query) {
            renderTable(allCriteria); // Si vide, affiche tout
            return;
        }
        
        const filtered = allCriteria.filter(critere => {
            switch(type) {
                case "1": // ID
                    return String(critere.Critere_ID).toLowerCase().includes(query);
                case "2": // Description
                    return String(critere.Critere_description).toLowerCase().includes(query);
                case "3": // Status
                    return String(critere.Critere_statut).toLowerCase().includes(query);
                case "4": // Points
                    return String(critere.Critere_points || '').toLowerCase().includes(query);
                default:
                    return false;
            }
        });
        
        renderTable(filtered); // Réaffiche avec les résultats filtrés
    }
    
    searchInput.addEventListener('input', filterTable);
    searchType.addEventListener('change', filterTable);
}