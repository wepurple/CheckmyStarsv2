async function getInfos() {
    const url = "models/crud/afficherUtilisateur.php?IdPersonne="+id
    const response = await fetch(url, {
        method : "GET",
        headers : {
            'Content-Type' : "application/json"
        }
    })

    const result = JSON.parse(await response.text())["utilisateur"][0]
    //console.log(result)
}

document.addEventListener("DOMContentLoaded", function() {//quand la page est chargée
    
});