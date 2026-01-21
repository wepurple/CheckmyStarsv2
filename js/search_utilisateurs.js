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

    for (var i = 0; i < users.length; i++)
    {
        console.log(users[i].Utilisateur_Nom);
    }
}