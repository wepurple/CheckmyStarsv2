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
    //console.log(list)

    for (let i = 0 ; i<ids.length ; i++){        
        document.getElementById(ids[i]).value = list[elements[i]]
    }
}

function edit(){
    for (let j = 0 ; j<ids.length ; j++){
        document.getElementById(ids[j]).removeAttribute("disabled")
    }
    document.getElementById("validerButton").removeAttribute("disabled")
    document.getElementById("cancelButton").removeAttribute("disabled")
    document.getElementById("editButton").setAttribute("disabled", "")
}

function cancel(){
    for (let j = 0 ; j<ids.length ; j++){
        document.getElementById(ids[j]).setAttribute("disabled", "")
        document.getElementById(ids[j]).classList.remove('is-invalid')
    }
    document.getElementById("validerButton").setAttribute("disabled", "")
    document.getElementById("cancelButton").setAttribute("disabled", "")
    document.getElementById("editButton").removeAttribute("disabled")
    updateInfos()
}

function valider(){
    let verif = true
    for (let k = 0 ; k<ids.length ; k++){
        console.log(document.getElementById(ids[k]).value == "")
        if(document.getElementById(ids[k]).value == ""){
            verif = false
            document.getElementById(ids[k]).classList.add('is-invalid')
        }else{
            document.getElementById(ids[k]).classList.remove('is-invalid')
        }
    }
    if(verif){
        console.log("ok")
        
    }
}

document.addEventListener("DOMContentLoaded", async function() {//quand la page est chargée
    updateInfos()
});