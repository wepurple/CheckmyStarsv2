async function getInfos() {
    const url = "models/crud/infoDossier.php"
    const response = await fetch(url, {
        method : "GET",
        headers : {
            'Content-Type' : "application/json"
        }
    })

    const result = JSON.parse(await response.text())["utilisateur"]
    return result
}

async function viderTab() {//vide le tablo
    tab = document.getElementById("tabloBody")
    child = tab.lastElementChild
    while (child){
        tab.removeChild(child)
        child = tab.lastElementChild
    }
}

function remplirTab(liste) {//remplit le tablo
    tab = document.getElementById("tabloBody")
    if (liste.length>0){
        viderTab()
        for (let i = 0; i < liste.length; i++){
            e=tab.lastElementChild
            tab.appendChild(document.createElement('th'))
            e.lastElementChild.scope = "row"
            e.lastElementChild.textContent = liste[i]["Utilisateur_ID"]
        }
    }
}

document.addEventListener("DOMContentLoaded", async function() {//quand la page est chargée
    const list = await getInfos()
    console.log(list)
    remplirTab(list)
    //remplissage du tablo

});