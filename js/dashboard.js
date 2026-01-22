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
            const tr = document.createElement("tr");

            tr.addEventListener("click", function () {
                window.location.href = "detail_client.php?id=" + liste[i]["Utilisateur_ID"];
            });
            tab.appendChild(tr)
            e=tab.lastElementChild
            e.appendChild(document.createElement('td'))
            e.lastElementChild.textContent = liste[i]["Utilisateur_ID"]

            e.appendChild(document.createElement('td'))
            e.lastElementChild.textContent = liste[i]["Utilisateur_Nom"]

            e.appendChild(document.createElement('td'))
            e.lastElementChild.textContent = liste[i]["Societe_Nom"]

            e.appendChild(document.createElement('td'))
            e.lastElementChild.textContent = liste[i]["Utilisateur_Telephone"]

            e.appendChild(document.createElement('td'))
            e.lastElementChild.textContent = liste[i]["Utilisateur_Mail"]

            e.appendChild(document.createElement('td'))
            e.lastElementChild.textContent = liste[i]["Nombre_Dossiers"]
            
            let statusGlobal = liste[i]["Status_Global"]
            let statusText = statusGlobal == 1 ? "Terminé" : "En cours"
            let statusClass = statusGlobal == 1
                ? "badge bg-success"
                : "badge bg-warning text-dark";

            let tdStatus = document.createElement("td")
            let spanStatus = document.createElement("span")

            spanStatus.className = statusClass
            spanStatus.textContent = statusText

            tdStatus.appendChild(spanStatus)
            e.appendChild(tdStatus)
        }
    }
}

document.addEventListener("DOMContentLoaded", async function() {//quand la page est chargée
    const list = await getInfos()
    console.log(list)
    remplirTab(list)
    //remplissage du tablo

});