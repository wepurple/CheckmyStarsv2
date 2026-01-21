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
                <td>${user.Nom || ''}</td>
                <td>${user.Prenom || ''}</td>
                <td>${user.Email || ''}</td>
                <td>Role</td>
                <td>${user.Societe || ''}</td>
                <td class="text-end">
                    <button class="btn btn-sm btn-warning">Edit</button>
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
document.addEventListener('DOMContentLoaded', loadTable);
