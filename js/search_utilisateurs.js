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

function loadTable()
{
    var users = getAllusers()

    for (var i = 0; i < users.lenght; i++)
    {
        console.log(users[1].Utilisateur_Nom)
    }
}