async function createUser() 
{
    const zoneError = document.getElementById('zone-error');
    zoneError.innerHTML = '';

    var nomValue = document.getElementById("nom").value;
    var prenomValue = document.getElementById("prenom").value;
    var emailValue = document.getElementById("email").value;
    var passwordValue = document.getElementById("password").value;
    var confirmPasswordValue = document.getElementById("confirmPassword").value;
    var phoneValue = document.getElementById("phone").value;
    var addressValue = document.getElementById("address").value;
    var additionalAddressValue = document.getElementById("additionalAddress").value;
    var postalCodeValue = document.getElementById("postalCode").value;
    var cityValue = document.getElementById("city").value;
    var companyValue = document.getElementById("company").value;
    var civiliteHomme = document.getElementById("civilite_homme");
    var civiliteFemme = document.getElementById("civilite_femme");

    var civiliteValue = "";
    if (civiliteHomme && civiliteHomme.checked) {
        civiliteValue = "Monsieur";
    } else if (civiliteFemme && civiliteFemme.checked) {
        civiliteValue = "Madame";
    }

    var adresseNum = "";
    var adresseNom = addressValue;
    var m = addressValue.trim().match(/^\s*(\d+)\s*(.*)$/);
    if (m) {
        adresseNum = m[1];
        adresseNom = m[2];
    }

    if (additionalAddressValue === "")
    {
        additionalAddressValue = null;
    }

    if (passwordValue !== confirmPasswordValue) {
        zoneError.innerHTML = `
            <div class="alert alert-danger">
                Les mots de passe ne correspondent pas
            </div>
        `;
        return;
    }

    try 
    {
        const url = 'http://172.20.33.6/checkmystars/models/crud/creer.php';
        const data = 
        {
            Nom: nomValue,
            Prenom: prenomValue,
            Civilite: civiliteValue,
            Telephone: phoneValue,
            Email: emailValue,
            AdresseNum: adresseNum,
            AdresseNom: adresseNom,
            Complement: additionalAddressValue,
            CodePostal: postalCodeValue,
            Ville: cityValue,
            Pays: "France",
            Societe: companyValue,
            MotPasse: passwordValue

        };

        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });

        if (!response.ok) {
            throw new Error(`Erreur HTTP: ${response.status}`);
        }

        // Essayer de lire la réponse comme texte d'abord
        const responseText = await response.text();
        console.log('Réponse brute:', responseText);
        
        // Parser en JSON
        const result = JSON.parse(responseText);
        console.log('Réponse JSON:', result);
        
        if (result.success) {
            zoneError.innerHTML = `
                <div class="alert alert-success">
                    ${result.message}
                </div>
            `;
        } else {
            zoneError.innerHTML = `
                <div class="alert alert-danger">
                    ${result.message}
                </div>
            `;
        }
        
    } catch (error) {
        console.error('Erreur complète:', error);
        zoneError.innerHTML = `
            <div class="alert alert-danger">
                Erreur: ${error.message}<br>
                Vérifiez la console pour plus de détails.
            </div>
        `;
    }
}

function checkLastName()
{
    let regex = /^[a-z]+$/i;
    regex.test(nomValue)
}

