async function getInfos() {
    const url = "models/crud/infoDossier.php"
    const response = await fetch(url, {
        method : "GET",
        headers : {
            'Content-Type' : "application/json"
        }
    })

    const result = JSON.parse(await response.text())["utilisateur"][0]
    return result
}

document.addEventListener("DOMContentLoaded", async function() {//quand la page est chargée
    const list = await getInfos()
    console.log(list)

});