async function getInfos() {
    const url = "models/crud/myInfo.php"
    const response = await fetch(url, {
        method : "GET",
        headers : {
            'Content-Type' : "application/json"
        }
    })

    const result = JSON.parse(await response.text())["utilisateur"][0]
    return result
}

async function updateInfos(){
    const list = await getInfos()
    console.log(list)
    const ids = [
        "nom",
        "prenom",
        "civilite",
        "mail",
        "tel",
        "societe",
        "numRue",
        "nomRue",
        "complement",
        "codePost",
        "ville",
        "pays"
    ] 
    const elements = [
        "Utilisateur_Nom",
        "Utilisateur_Prenom",
        "Utilisateur_Civilite",
        "Utilisateur_Mail",
        "Utilisateur_Telephone",
        "Utilisateur_Societe",
        "AdressePostale_NumeroRue",
        "AdressePostale_NomRue",
        "AdressePostale_Complement",
        "AdressePostale_CodePostal",
        "AdressePostale_Ville",
        "AdressePostale_Pays"
    ]

    for (i = 0 ; i<ids.length ; i++){        
        document.getElementById(ids[i]).textContent = list[elements[i]]
    }
}



document.addEventListener("DOMContentLoaded", async function() {//quand la page est chargée
    updateInfos()
});