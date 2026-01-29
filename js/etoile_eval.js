document.addEventListener('DOMContentLoaded', function() {
    let liste = document.getElementById('selectEtoiles');

    let allData = [];

    liste.addEventListener('change', function() {
        
        let etoiles = this.value 
        
        fetch(`models/crud/getCriteriaByEtoile.php?star=${etoiles}`)
            .then(response => {
                console.log('Reponse fetch; ', response);
                return response.json();
            })
            .then(data => {
                console.log("Données recues: ", data);
                allData = data;
                displayData(data);
            })
            .catch(error => console.log("Erreur fetch : ", error));

        function displayData(data) {
            const tbody = document.getElementById('table-body');

            if(!tbody) {
                console.error("Erreur: table-body introuvable");
                return;
            }

            tbody.innerHTML = '';
        
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
                    <td>${critere.Critere_statut}</td>
                    <td>${critere.Critere_points}</td>
                    <td>
                        <label for"nombre"Choisir un nombre : </label>
                        <input type="number" id="nombre" name="nombre" min="0" max=${critere.Critere_points}>
                    </td>
                `;
            });
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        console.log(etoiles);

    return etoiles;

    });

});