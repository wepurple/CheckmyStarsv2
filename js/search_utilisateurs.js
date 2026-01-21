async function getAllusers()
{
    const url = "models/Read/users.php";
    const response = await fetch(url, {
        method : "GET",
        headers : {
            'Content-Type' : "application/json"
        }
    });

    const result = await response.json();
    console.log("Données reçues:", result);

    return result;
}

function getUserById(id)
{
    
}

async function loadTable()
{
    try {
        var users = await getAllusers()
        var tab = document.getElementById("table-body");
        tab.innerHTML = "";

        console.log("Nombre d'utilisateurs:", users.length);

        for (var i = 0; i < users.length; i++)
        {
            var user = users[i];
            console.log("Utilisateur", i, ":", user);
            
            var tr = document.createElement("tr");
            
            tr.innerHTML = `
                <td>${user.Utilisateur_ID || user.IdPersonne || ''}</td>
                <td>${user.Utilisateur_Nom || ''}</td>
                <td>${user.Utilisateur_Prenom || ''}</td>
                <td>${user.Utilisateur_Mail || ''}</td>
                <td>TODO</td>
                <td>${user.Societe_Nom || ''}</td>
                <td class="text-end">
                    <button class="btn btn-secondary btn-sm me-2"> <i class="fa-solid fa-eye"></i> </button>
                    <button class="btn btn-sm btn-warning me-2">Edit</button>
                    <button class="btn btn-sm btn-danger">Delete</button>
                </td>
            `;
            tab.appendChild(tr);
        }
    } catch (error) {
        console.error("Erreur:", error);
    }
}

// Charger le tableau au démarrage de la page
document.addEventListener("DOMContentLoaded", function() {
    loadTable
    seeModal = new bootstrap.Modal(document.getElementById('seeModal'))
});


