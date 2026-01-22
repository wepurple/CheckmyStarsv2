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

async function remplirTab() {//remplit le tablo
    tab = document.getElementById("tabloBody")
}

document.addEventListener("DOMContentLoaded", async function() {//quand la page est chargée
    const list = await getInfos()
    console.log(list)

    //remplissage du tablo

});