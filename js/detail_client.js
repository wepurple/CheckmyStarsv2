const currentUrl = window.location.search;
var query = new URLSearchParams(currentUrl);
var clientId = query.get('id');

var idUser;

const REGEX = {
    nomBien: /(.|\s)*\S(.|\s)*/,

    email: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
    telFR: /^(?:(?:\+33)\s?|0)[1-9](?:[\s.-]?\d{2}){4}$/,

    numRue: /^(?:\d{1,5})(?:\s?(?:bis|ter|quater|[A-Za-z]))?$/i,
    nomRue: /^[0-9A-Za-zÀ-ÖØ-öø-ÿ'’().,\-\/\s]{2,100}$/,
    complement: /^[0-9A-Za-zÀ-ÖØ-öø-ÿ'’().,\-\/\s]{0,100}$/,
    codePostal: /^\d{5}$/,
    ville: /^[0-9A-Za-zÀ-ÖØ-öø-ÿ'’\-\/\s]{2,80}$/,
    pays: /^[A-Za-zÀ-ÖØ-öø-ÿ'’\-\/\s]{2,60}$/,
};


async function preFillClientInfo()
{
    var clientLastName = document.getElementById("leNom");
    var clientFirstName = document.getElementById("lePrenom");
    var clientCompany = document.getElementById("laSociete");
    var clientMail = document.getElementById("leMail");

    var clientInformation = await getUserById(clientId);

    clientLastName.value = clientInformation.Utilisateur_Nom;
    clientFirstName.value = clientInformation.Utilisateur_Prenom;
    clientCompany.value = clientInformation.Societe_Nom;
    clientMail.value = clientInformation.Utilisateur_Mail;

    console.log(clientInformation);
}

async function submitPreFillClientInfo() 
{
    try
    {
      var nameProperty = document.getElementById("leNomBien").value.trim();
      var phoneProperty = document.getElementById("leTelBien").value.trim();
      var typeProperty = document.getElementById("typeBien").value.trim();
      var currentStar = document.getElementById("etoileActuel").value.trim();
      var targetStar = document.getElementById("etoileCible").value.trim();

      var streetNumber = document.getElementById('leNumRue').value.trim();
      var streetName = document.getElementById('laAdresse').value.trim();
      var complement = document.getElementById('leComplement').value.trim();
      var postcode = document.getElementById('leCode').value.trim();
      var city = document.getElementById('laVille').value.trim();
      var country = document.getElementById('lePays').value.trim();

      var orderingParty = document.getElementById('donneurOrdre').value.trim(); // Donneur d'ordre

      if (orderingParty == 0)
      {
        orderingParty = null;
      }

      if (!checkRegex('leNomBien', nameProperty, REGEX.nomBien, "Nom du bien invalide")) return;

      if (!checkRegex('leTelBien', phoneProperty, REGEX.telFR, "Téléphone invalide (ex: 06 12 34 56 78 ou +33 6 12 34 56 78)")) return;

      if (!checkRequired('laAdresseComplete', streetNumber, "Numéro de rue obligatoire")) return;
      if (!checkRequired('laAdresseComplete', streetName, "Nom de rue obligatoire")) return;
      if (!checkRequired('laAdresseComplete', postcode, "Code postal obligatoire")) return;
      if (!checkRequired('laAdresseComplete', city, "Ville obligatoire")) return;
      if (!checkRequired('laAdresseComplete', country, "Pays obligatoire")) return;

      if (!checkRegex('laAdresseComplete', streetNumber, REGEX.numRue, "Numéro de rue invalide (ex: 12, 12 bis, 12B)")) return;
      if (!checkRegex('laAdresseComplete', streetName, REGEX.nomRue, "Adresse invalide")) return;
      if (complement !== "" && !checkRegex('leComplement', complement, REGEX.complement, "Complément invalide")) return;
      if (!checkRegex('laAdresseComplete', postcode, REGEX.codePostal, "Code postal invalide (5 chiffres)")) return;
      if (!checkRegex('laAdresseComplete', city, REGEX.ville, "Ville invalide")) return;
      if (!checkRegex('laAdresseComplete', country, REGEX.pays, "Pays invalide")) return;

      const data =
      {
        NumRue: streetNumber,
        NomRue: streetName,
        Comp: complement,
        CP: postcode,
        Ville: city,
        Pays: country,

        BiensNom: nameProperty,
        BiensTel: phoneProperty,
        BiensEtoiles: currentStar,
        BiensDonneurID: orderingParty,
        BiensType: typeProperty,
        BiensUser: clientId,
        EtoileDossier: targetStar,
        InspecteurID: idUser,
      };

      console.log(data)

      const response = await fetch("../../models/Create/folder.php", {
        method: "POST",
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(data)
      });

      const result = await response.json();
      if (result.success) {
        const addModalElement = document.getElementById('exampleModal');
        const addModal = bootstrap.Modal.getInstance(addModalElement);
        if (addModal) addModal.hide();
        clearValidationClasses('addForm');
        document.getElementById('addForm').reset();
        showToast("Dossier créé avec succès !", "success");
      } else {
        showToast("Erreur lors de la création : " + result.error, "error");
      }
    }
    catch(error)
    {
        console.error("Erreur:", error);
        showToast("Une erreur s'est produite : " + error.message, "error");
    }
}

function clearValidationClasses(formId) {
  const form = document.getElementById(formId);
  if (!form) return;
  
  const inputs = form.querySelectorAll('.is-valid, .is-invalid');
  inputs.forEach(input => {
    input.classList.remove('is-valid', 'is-invalid');
  });
}

async function getUserById(id) {
    const url = "../../models/Read/users.php?IdPersonne=" + id;
    const response = await fetch(url, {
        method: "GET",
        headers: {
            'Content-Type': "application/json"
        }
    });
    const result = await response.json();

    return result;
}

function showToast(message, type = 'success') 
{
  const typeConfig = {
    success: { bg: 'bg-success', icon: '<i class="fa-solid fa-check"></i>', title: 'Succès' },
    error: { bg: 'bg-danger', icon: '<i class="fa-solid fa-bug"></i>', title: 'Erreur' },
    warning: { bg: 'bg-warning', icon: '<i class="fa-solid fa-triangle-exclamation"></i>', title: 'Attention' },
    info: { bg: 'bg-info', icon: '<i class="fa-solid fa-info"></i>', title: 'Information' }
  };
  
  const config = typeConfig[type] || typeConfig['info'];
  
  // Créer l'élément toast
  const toastHTML = `
    <div class="toast align-items-center text-white ${config.bg} border-0" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="d-flex">
        <div class="toast-body">
          <strong>${config.icon}</strong> ${message}
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    </div>
  `;
  
  // Ajouter le toast au conteneur
  const container = document.querySelector('.toast-container');
  container.insertAdjacentHTML('beforeend', toastHTML);
  
  // Initialiser et afficher le toast
  const toastElement = container.lastElementChild;
  const toast = new bootstrap.Toast(toastElement, {
    autohide: true,
    delay: type === 'error' ? 5000 : 3000
  });
  
  toast.show();
  
  // Supprimer le toast du DOM après fermeture
  toastElement.addEventListener('hidden.bs.toast', () => {
    toastElement.remove();
  });
}

function setupAdresseAutocomplete({ adresseCompleteId, numRueId, adresseId, codeId, villeId, paysId }) 
{
  const adresseCompleteInput = document.getElementById(adresseCompleteId);
  const numRueInput = document.getElementById(numRueId);
  const adresseInput = document.getElementById(adresseId);
  const codeInput = document.getElementById(codeId);
  const villeInput = document.getElementById(villeId);
  const paysInput = document.getElementById(paysId);

  if (!adresseCompleteInput) return;

  let lastFeatures = [];
  let abortController = null;

  // Créer la div de suggestions
  const suggestionsDiv = document.createElement('div');
  suggestionsDiv.className = 'autocomplete-suggestions';
  suggestionsDiv.style.cssText = `
    position: absolute;
    z-index: 9999;
    background: #2b3035;
    border: 1px solid #495057;
    border-radius: 4px;
    max-height: 300px;
    overflow-y: auto;
    display: none;
    box-shadow: 0 4px 6px rgba(0,0,0,0.3);
  `;
  adresseCompleteInput.parentElement.style.position = 'relative';
  adresseCompleteInput.parentElement.appendChild(suggestionsDiv);

  adresseCompleteInput.addEventListener("input", async () => {
    const q = adresseCompleteInput.value.trim();
    if (q.length < 3) {
      suggestionsDiv.style.display = 'none';
      suggestionsDiv.innerHTML = '';
      lastFeatures = [];
      return;
    }

    if (abortController) abortController.abort();
    abortController = new AbortController();

    const url = `https://api-adresse.data.gouv.fr/search/?q=${encodeURIComponent(q)}&limit=8`;

    try {
      const resp = await fetch(url, { signal: abortController.signal });
      if (!resp.ok) return;

      const data = await resp.json();
      lastFeatures = data.features || [];

      if (lastFeatures.length === 0) {
        suggestionsDiv.style.display = 'none';
        return;
      }

      suggestionsDiv.innerHTML = lastFeatures
        .map((f, idx) => {
          const p = f.properties || {};
          return `
            <div class="suggestion-item" data-idx="${idx}" style="
              padding: 12px 14px;
              cursor: pointer;
              border-bottom: 1px solid #3a3f44;
              transition: background 0.15s;
            ">
              <div style="display: flex; align-items: start; gap: 10px;">
                <i class="fas fa-map-marker-alt" style="color: #0d6efd; margin-top: 3px;"></i>
                <div style="flex: 1;">
                  <div style="color: #dee2e6; font-size: 14px; font-weight: 500;">
                    ${p.name || p.label || ''}
                  </div>
                  <div style="color: #adb5bd; font-size: 12px; margin-top: 2px;">
                    ${p.city || ''} · ${p.postcode || ''} · ${p.context || ''}
                  </div>
                </div>
              </div>
            </div>
          `;
        })
        .join('');

      suggestionsDiv.style.display = 'block';
      suggestionsDiv.style.width = adresseCompleteInput.offsetWidth + 'px';

      suggestionsDiv.querySelectorAll('.suggestion-item').forEach(item => {
        item.addEventListener('mouseenter', () => {
          item.style.backgroundColor = '#495057';
        });
        item.addEventListener('mouseleave', () => {
          item.style.backgroundColor = 'transparent';
        });
        item.addEventListener('click', () => {
          const idx = parseInt(item.dataset.idx);
          selectAddress(lastFeatures[idx]);
        });
      });

    } catch (e) {

    }
  });

  function selectAddress(feature) {
    if (!feature) return;
    const p = feature.properties || {};

    adresseCompleteInput.value = p.label || '';

    if (numRueInput) numRueInput.value = p.housenumber || '';
    if (adresseInput) adresseInput.value = p.street || p.name || '';
    if (codeInput) codeInput.value = p.postcode || '';
    if (villeInput) villeInput.value = p.city || '';
    if (paysInput) paysInput.value = 'France';

    suggestionsDiv.style.display = 'none';
    suggestionsDiv.innerHTML = '';

    adresseCompleteInput.classList.add('is-valid');
    setTimeout(() => adresseCompleteInput.classList.remove('is-valid'), 2000);
  }

  document.addEventListener('click', (e) => {
    if (!adresseCompleteInput.contains(e.target) && !suggestionsDiv.contains(e.target)) {
      suggestionsDiv.style.display = 'none';
    }
  });

  let selectedIndex = -1;
  adresseCompleteInput.addEventListener('keydown', (e) => {
    const items = suggestionsDiv.querySelectorAll('.suggestion-item');
    if (items.length === 0) return;

    if (e.key === 'ArrowDown') {
      e.preventDefault();
      selectedIndex = Math.min(selectedIndex + 1, items.length - 1);
      updateSelection(items);
    } else if (e.key === 'ArrowUp') {
      e.preventDefault();
      selectedIndex = Math.max(selectedIndex - 1, 0);
      updateSelection(items);
    } else if (e.key === 'Enter' && selectedIndex >= 0) {
      e.preventDefault();
      const idx = parseInt(items[selectedIndex].dataset.idx);
      selectAddress(lastFeatures[idx]);
      selectedIndex = -1;
    } else if (e.key === 'Escape') {
      suggestionsDiv.style.display = 'none';
      selectedIndex = -1;
    }
  });

  function updateSelection(items) {
    items.forEach((item, i) => {
      item.style.backgroundColor = i === selectedIndex ? '#495057' : 'transparent';
    });
  }
}

function markField(id, ok) 
{
  const el = document.getElementById(id);
  if (!el) return;
  el.classList.toggle('is-invalid', !ok);
  el.classList.toggle('is-valid', ok);
}

function checkRegex(id, value, regex, msg) 
{
  const ok = regex.test(value);
  markField(id, ok);
  if (!ok) {
    showToast(msg, "warning");
    const el = document.getElementById(id);
    if (el) el.focus();
  }
  return ok;
}

function checkRequired(id, value, msg) 
{
  const ok = value !== "";
  markField(id, ok);
  if (!ok) {
    showToast(msg, "warning");
    const el = document.getElementById(id);
    if (el) el.focus();
  }
  return ok;
}

function checkRegex(id, value, regex, msg) {
  const ok = regex.test(value);
  markField(id, ok);
  if (!ok) {
    showToast(msg, "warning");
    const el = document.getElementById(id);
    if (el) el.focus();
  }
  return ok;
}

function checkRequired(id, value, msg) {
  const ok = value !== "";
  markField(id, ok);
  if (!ok) {
    showToast(msg, "warning");
    const el = document.getElementById(id);
    if (el) el.focus();
  }
  return ok;
}

function addressBlockTouched(v) 
{
  return [v.num_rue, v.nom_rue, v.complement, v.code_postal, v.ville, v.pays].some(x => (x || "").trim() !== "");
}

document.addEventListener("DOMContentLoaded", () => {
    setupAdresseAutocomplete({
        adresseCompleteId: "laAdresseComplete",
        numRueId: "leNumRue",
        adresseId: "laAdresse",
        codeId: "leCode",
        villeId: "laVille",
        paysId: "lePays"
    });

    preFillClientInfo();
});