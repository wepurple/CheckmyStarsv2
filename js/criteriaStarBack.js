// Récupère le numéro d'étoile depuis l'URL
const urlParams = new URLSearchParams(window.location.search);
const star = urlParams.get('star') || 1;

const tbody = document.getElementById('table-body');
const searchInput = document.getElementById('recherche');
const searchType = document.getElementById('type');

let allCriteria = []; // Stocke toutes les données

// Fonction pour afficher les lignes
function renderTable(data) {
    tbody.innerHTML = '';
    data.forEach(critere => {
        const row = tbody.insertRow();
        row.innerHTML = `
            <td>${critere.id}</td>
            <td>${critere.description}</td>
            <td>${critere.status}</td>
            <td>${critere.points}</td>
        `;
    });
}

// Fonction de filtrage
function filterTable() {
    const query = searchInput.value.trim().toLowerCase();
    const type = searchType.value;

    if (!query) {
        renderTable(allCriteria);
        return;
    }

    const filtered = allCriteria.filter(critere => {
        switch(type) {
            case "1": // ID
                return String(critere.id).toLowerCase().includes(query);
            case "2": // Description
                return String(critere.description).toLowerCase().includes(query);
            case "3": // Status
                return String(critere.status).toLowerCase().includes(query);
            case "4": // Points
                return String(critere.points).toLowerCase().includes(query);
            default:
                return false;
        }
    });

    renderTable(filtered);
}

// Fetch les données
fetch(`models/crud/getCriteriaByEtoile.php?star=${star}`)
    .then(response => response.json())
    .then(data => {
        allCriteria = data;
        renderTable(allCriteria);
    })
    .catch(err => console.error('Erreur chargement:', err));

// Écoute les changements
searchInput.addEventListener('input', filterTable);
searchType.addEventListener('change', filterTable);
