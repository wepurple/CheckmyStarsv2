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
    console.log(result);

    return result;
}

function getUserById(id)
{
    
}

async function loadTable()
{
    var users = await getAllusers()
    var tab = document.getElementById("table-body");
    tab.innerHTML = ""; // Vider le tableau

    for (var i = 0; i < users.length; i++)
    {
        var user = users[i];
        
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
}
