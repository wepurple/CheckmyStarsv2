async function valider() {//clic sur le bouton de validation

    const password = document.getElementById('password').value
    const confirm = document.getElementById('confirm').value
    const pwRegex = /^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{12,}$/

    //si les mots de passe sont différents
    if(password != confirm){
        document.getElementById('toastText').textContent = "Les mots de passe doivent être identiques"
        leToast.show()
    }else if(!pwRegex.test(password)){
        document.getElementById('toastText').textContent = "Mot de passe trop faible"
        leToast.show()
    }else{        
        //envoi de la requete de changement de mdp
        const url = 'models/Update/updateFirstPassword.php'
        const data = {
            new: password,
        }
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        const responseText = await response.text();
        const state = await response.status
        const test = await JSON.parse(responseText)

                if (await state == 200){
                    document.location.href="/Checkmystars/CheckMyStars/dashboard";
                }else{
                    document.getElementById('toastText').textContent = test["response"]
                    leToast.show()
                }
    }
}

document.addEventListener("DOMContentLoaded", function() {//quand la page est chargée
    leToast = new bootstrap.Toast(this.getElementById('toast'))
})