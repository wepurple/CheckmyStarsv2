async function createUser() 
{
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

    const errorAlert = document.getElementById('errorAlert');
    const errorMessage = document.getElementById('errorMessage');
    const submitBtn = document.querySelector('button.center-btn');

    function showError(msg) {
        if (errorAlert && errorMessage) {
            errorMessage.textContent = msg;
            errorAlert.classList.remove('d-none');
        } else {
            alert(msg);
        }
    }
    function hideError() {
        if (errorAlert && errorMessage) {
            errorMessage.textContent = '';
            errorAlert.classList.add('d-none');
        }
    }

    function validateForm() {
        const nameRegex = /^[A-Za-zÀ-ÖØ-öø-ÿ \-']+$/;
        if (!nameRegex.test(nomValue) || nomValue.trim().length < 2) {
            showError('Nom invalide.');
            return false;
        }
        if (!nameRegex.test(prenomValue) || prenomValue.trim().length < 2) {
            showError('Prénom invalide.');
            return false;
        }
        if (!civiliteValue) {
            showError('Veuillez sélectionner la civilité.');
            return false;
        }
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(emailValue)) {
            showError('Adresse email invalide.');
            return false;
        }
        const phoneRegex = /^[0-9]{10}$/;
        if (!phoneRegex.test(phoneValue)) {
            showError('Numéro de téléphone invalide (10 chiffres).');
            return false;
        }
        if (!adresseNum) {
            showError('Veuillez saisir un numéro de rue au début de l\'adresse.');
            return false;
        }
        const postalRegex = /^[0-9]{5}$/;
        if (!postalRegex.test(postalCodeValue)) {
            showError('Code postal invalide (5 chiffres).');
            return false;
        }
        if (passwordValue.length < 8) {
            showError('Le mot de passe doit contenir au moins 8 caractères.');
            return false;
        }
        if (passwordValue !== confirmPasswordValue) {
            showError('Les mots de passe ne correspondent pas.');
            return false;
        }
        if (companyValue.trim() === '') {
            showError('Société requise.');
            return false;
        }
        return true;
    }

    try 
    {
        hideError();
        if (!validateForm()) return;

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

        // disable button while sending
        if (submitBtn) submitBtn.disabled = true;

        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });
        const resText = await response.text();
        let resJson = null;
        try { resJson = JSON.parse(resText); } catch(e) { /* ignore */ }

        if (!response.ok) {
            if (response.status === 400 && resJson && resJson.errors) {
                // show first validation error
                const firstKey = Object.keys(resJson.errors)[0];
                showError(resJson.errors[firstKey]);
            } else if (resJson && resJson.message) {
                showError(resJson.message);
            } else {
                showError('Erreur HTTP: ' + response.status);
            }
            return;
        }

        // success
        if (resJson && resJson.message) {
            alert(resJson.message);
            // optionally redirect to login
            window.location.href = 'index.php';
        } else {
            alert('Inscription réussie.');
            window.location.href = 'index.php';
        }

    }
    catch (error) 
    {

        if (error && error.message) showError(error.message);
    }
    finally {
        if (submitBtn) submitBtn.disabled = false;
    }
}


