async function getAllusers()
{
    const url = "models/Read/users.php";
    const response = await fetch(url, {
        method : "GET",
        headers : {
            'Content-Type' : "application/json"
        }
    });

    const result = JSON.parse(await response.text())["utilisateur"]

    console.log(result);
}

function getUserById(id)
{
    
}