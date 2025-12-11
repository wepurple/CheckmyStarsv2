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
        const url = 'models/crud/creer.php';
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

        // Essayer de lire la réponse comme texte d'abord
        const responseText = await response.text();
        console.log('Réponse brute:', responseText);
        
        // Parser en JSON
        const result = JSON.parse(responseText);
        console.log('Réponse JSON:', result);
        console.log('Status code:', response.status);
        
        if (response.ok || response.status === 201) {
            zoneError.innerHTML = `
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>${result.message}
                </div>
            `;
            // Réinitialiser le formulaire
            document.getElementById('registerForm').reset();
            // Redirection après 2 secondes
            setTimeout(() => {
                window.location.href = 'index.php';
            }, 2000);
        } else {
            zoneError.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>${result.message}
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

function validateForm() {
    const zoneError = document.getElementById('zone-error');
    zoneError.innerHTML = '';

    let nomValue = document.getElementById("nom").value;
    let prenomValue = document.getElementById("prenom").value;
    let emailValue = document.getElementById("email").value;
    let passwordValue = document.getElementById("password").value;
    let confirmPasswordValue = document.getElementById("confirmPassword").value;
    let phoneValue = document.getElementById("phone").value;
    let postalCodeValue = document.getElementById("postalCode").value;

    let errors = [];

    // Valider le nom et prénom (lettres uniquement)
    if (!/^[a-z\s]+$/i.test(nomValue.trim())) {
        errors.push("Le nom ne doit contenir que des lettres");
    }
    if (!/^[a-z\s]+$/i.test(prenomValue.trim())) {
        errors.push("Le prénom ne doit contenir que des lettres");
    }

    // Valider l'email
    if (!/^\S+@\S+\.\S+$/.test(emailValue)) {
        errors.push("Veuillez entrer une adresse email valide");
    }

    // Valider le mot de passe
    if (passwordValue.length < 6) {
        errors.push("Le mot de passe doit contenir au moins 6 caractères");
    }

    // Vérifier la confirmation du mot de passe
    if (passwordValue !== confirmPasswordValue) {
        errors.push("Les mots de passe ne correspondent pas");
    }

    // Valider le téléphone
    if (!/^[0-9]{10}$/.test(phoneValue.replace(/\s/g, ''))) {
        errors.push("Le téléphone doit contenir 10 chiffres");
    }

    // Valider le code postal
    if (!/^[0-9]{5}$/.test(postalCodeValue)) {
        errors.push("Le code postal doit contenir 5 chiffres");
    }

    if (errors.length > 0) {
        let errorHTML = '<div class="alert alert-danger"><i class="fas fa-times-circle me-2"></i><ul class="mb-0">';
        errors.forEach(error => {
            errorHTML += `<li>${error}</li>`;
        });
        errorHTML += '</ul></div>';
        zoneError.innerHTML = errorHTML;
        return false;
    }

    return true;
}

