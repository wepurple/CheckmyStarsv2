let i = 0;
let allData = [];
let etoiles = null;


document.addEventListener('DOMContentLoaded', function() {
    let liste = document.getElementById('selectEtoiles');

    liste.addEventListener('change', function() {
        
        etoiles = this.value 
        
        fetchData();

    return etoiles;

    });
});

function fetchData(){
    fetch(`models/crud/getCriteriaByEtoile.php?star=${etoiles}`)
        .then(response => {
            console.log('Reponse fetch; ', response);
            return response.json();
        })
        .then(data => {
            console.log("Données recues: ", data);
            allData = data;
            displayData(data);
            setupFilters();
        })
        .catch(error => console.log("Erreur fetch : ", error));
    }

function displayData(data) {
    const tbody = document.getElementById('table-body');

    if(!tbody) {
        console.error("Erreur: table-body introuvable");
        return;
    }

    tbody.innerHTML = '';
    i = 0;
    data.forEach(critere => {
        const row = tbody.insertRow();
        row.innerHTML = `
            <td>${critere.Critere_ID}</td>
            <td>
                <div class="d-flex align-items-center gap-2">
                    <span class="flex-grow-1">
                    ${critere.Critere_description}
                    </span>
                </div>
            </td>
            <td id="statut-${i}">${critere.Critere_statut}</td>
            <td id="points-${i}">${critere.Critere_points}</td>
            <td>
                <input type="checkbox" id="checkbox-${i}" name="checkbox">
            </td>
            <td id="textarea-${i}"><textarea></textarea></td>
        `;
        
        i = i + 1;
    });
}

console.log(etoiles);

function setupFilters() {
    const searchBar = document.getElementById('searchBar');
    const filterType = document.getElementById('filterType');
    
    if (!searchBar) {
        console.error('ERREUR: searchBar introuvable !');
        return;
    }
    
    if (!filterType) {
        console.error('ERREUR: filterType introuvable !');
        return;
    }
    
    // Événements
    searchBar.addEventListener('input', function() {
        filterData();
    });
    
    filterType.addEventListener('change', function() {
        filterData();
    });
}

// Fonction de filtrage
function filterData() {
    const searchBar = document.getElementById('searchBar');
    const filterType = document.getElementById('filterType');
    const searchTerm = searchBar.value.toLowerCase().trim();
    
    if (!searchTerm) {
        displayData(allData);
        return;
    }
    
    const filteredData = allData.filter(critere => {
        switch(filterType.value) {
            case 'id':
                return critere.Critere_ID.toString().includes(searchTerm);
            case 'description':
                return critere.Critere_description.toLowerCase().includes(searchTerm);
            case 'status':
                return critere.Critere_statut.toLowerCase().includes(searchTerm);
            case 'points':
                const points = critere.Critere_points !== null && critere.Critere_points !== undefined 
                    ? critere.Critere_points.toString() 
                    : '';
                return points.includes(searchTerm);
            case 'all':
            default:
                const pointsAll = critere.Critere_points !== null && critere.Critere_points !== undefined 
                    ? critere.Critere_points.toString() 
                    : '';
                return critere.Critere_ID.toString().includes(searchTerm) ||
                    critere.Critere_description.toLowerCase().includes(searchTerm) ||
                    critere.Critere_statut.toLowerCase().includes(searchTerm) ||
                    pointsAll.includes(searchTerm);
        }
    });
    
    displayData(filteredData);
}

// Affiche le nombre de résultats
function updateResultCount(count) {
    let countElement = document.getElementById('result-count');
    if (!countElement) {
        countElement = document.createElement('div');
        countElement.id = 'result-count';
        countElement.style.marginTop = '10px';
        countElement.style.fontWeight = 'bold';
        countElement.style.color = '#ff0000';
        const container = document.querySelector('.search-filter-container');
        if (container) {
            container.appendChild(countElement);
        }
    }
    countElement.textContent = `${count} résultat(s)`;
}

let points_X = 0;
let points_O = 0;
let points_NA = 0;
let points_ONC = 0;

let total = 0;

function pointsTotal() {
    points_X = 0;
    points_O = 0;
    points_NA = 0;
    points_XONC = 0;
    points_X_Max = 0;
    points_O_Max = 0;
    points_NA_Max = 0;
    points_XONC_Max = 0;
    invalide = true;
    result = "";

    document.getElementById('checkAll').addEventListener('change', function() {
        const isChecked = this.checked;
    });

    for (let j =0; j < i; j++) {

        const checkbox = document.getElementById(`checkbox-${j}`);
        checkbox.checked = isChecked;
        const status = document.getElementById(`statut-${j}`);
        const points = Number(document.getElementById(`points-${j}`).textContent);
        console.log('Checkbox ', j, ': ', checkbox.checked);
        console.log('statut ', j, ': ', status.textContent);
        console.log('points ', j, ': ', points);
        points_X_Max += 0;
        points_O_Max += 0;
        points_NA_Max += 0;
        points_XONC_Max += 0;
        if (checkbox.checked) {
            if (status.textContent === "O") {
                points_O += points;
            }
            else if (status.textContent === "X") {
                points_X += points;
            }
            else if (status.textContent === "NA") {
                points_NA += points;
            }
            else if (status.textContent === "X ONC") {
                points_XONC += points;
            }
        }
        if (!checkbox.checked) {
            if (status.textContent === "X") {
                invalide = false;
            }
        }
    }
    if (invalide) {
        result = "Évaluation valide.";
    } else {
        result = "Évaluation invalide : un critère obligatoire n'a pas été coché.";
    }

    console.log('Points O :', points_O);
    console.log('Points X :', points_X);
    console.log('Points NA :', points_NA);
    console.log('Points ONC :', points_XONC);
    console.log(result);
    console.log('Points O :', points_O_Max);
    console.log('Points X :', points_X_Max);
    console.log('Points NA :', points_NA_Max);
    console.log('Points ONC :', points_XONC_Max);
}





    