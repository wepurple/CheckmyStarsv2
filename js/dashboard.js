let seeModal = null;
let editModal = null;
let deleteUserId = null;
let confirmModal = null;
let societeModal = null;

const REGEX = {
  nom: /^[A-Za-zÀ-ÖØ-öø-ÿ'’ -]{2,50}$/,
  prenom: /^[A-Za-zÀ-ÖØ-öø-ÿ'’ -]{2,50}$/,

  email: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
  telFR: /^(?:(?:\+33)\s?|0)[1-9](?:[\s.-]?\d{2}){4}$/,

  password: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,64}$/,

  numRue: /^(?:\d{1,5})(?:\s?(?:bis|ter|quater|[A-Za-z]))?$/i,
  nomRue: /^[0-9A-Za-zÀ-ÖØ-öø-ÿ'’().,\-\/\s]{2,100}$/,
  complement: /^[0-9A-Za-zÀ-ÖØ-öø-ÿ'’().,\-\/\s]{0,100}$/,
  codePostal: /^\d{5}$/,
  ville: /^[0-9A-Za-zÀ-ÖØ-öø-ÿ'’\-\/\s]{2,80}$/,
  pays: /^[A-Za-zÀ-ÖØ-öø-ÿ'’\-\/\s]{2,60}$/,

  civiliteValue: /^[1-3]$/,
  roleId: /^[0-3]$/,
  societeId: /^\d+$/
};

async function getInfos() {
  const url = "../checkmystars/models/Read/infoDossier.php";
  const response = await fetch(url, {
    method: "GET",
    headers: {
      "Content-Type": "application/json",
    },
  });

  const result = JSON.parse(await response.text())["utilisateur"];
  return result;
}

async function viderTab() {
  //vide le tablo
  tab = document.getElementById("tabloBody");
  child = tab.lastElementChild;
  while (child) {
    tab.removeChild(child);
    child = tab.lastElementChild;
  }
}

function remplirTab(liste) {
  //remplit le tablo
  tab = document.getElementById("tabloBody");

  if (liste.length > 0) {
    viderTab();
    for (let i = 0; i < liste.length; i++) {
      const tr = document.createElement("tr");

      tr.addEventListener("click", function () {
        window.location.href =
          "../checkmystars/gestion/details/detail_client.php?id=" + liste[i]["Utilisateur_ID"];
      });
      tab.appendChild(tr);
      e = tab.lastElementChild;
      e.appendChild(document.createElement("td"));
      e.lastElementChild.textContent = liste[i]["Utilisateur_ID"];

      e.appendChild(document.createElement("td"));
      e.lastElementChild.textContent = liste[i]["Utilisateur_Nom"];

      e.appendChild(document.createElement("td"));
      e.lastElementChild.textContent = liste[i]["Societe_Nom"];

      e.appendChild(document.createElement("td"));
      e.lastElementChild.textContent = liste[i]["Utilisateur_Telephone"];

      e.appendChild(document.createElement("td"));
      e.lastElementChild.textContent = liste[i]["Utilisateur_Mail"];

      e.appendChild(document.createElement("td"));
      e.lastElementChild.textContent = liste[i]["Nombre_Dossiers"];

      let statusGlobal = liste[i]["Status_Global"];
      let statusText = statusGlobal == 1 ? "Terminé" : "En cours";
      let statusClass =
        statusGlobal == 1 ? "badge bg-success" : "badge bg-warning text-dark";

      let tdStatus = document.createElement("td");
      let spanStatus = document.createElement("span");

      spanStatus.className = statusClass;
      spanStatus.textContent = statusText;

      tdStatus.appendChild(spanStatus);
      e.appendChild(tdStatus);
    }
  }
}

async function addUser() {
  try {
    const v = {
      nom: document.getElementById('leNom').value.trim(),
      prenom: document.getElementById('lePrenom').value.trim(),
      civiliteValue: document.getElementById('leGenre').value,
      email: document.getElementById('leMail').value.trim(),
      societe_id: document.getElementById('laSociete').value,
      role_id: document.getElementById('leRole').value,
      telephone: document.getElementById('leTel').value.trim(),
      num_rue: document.getElementById('leNumRue').value.trim(),
      nom_rue: document.getElementById('laAdresse').value.trim(),
      complement: document.getElementById('leComplement').value.trim(),
      code_postal: document.getElementById('leCode').value.trim(),
      ville: document.getElementById('laVille').value.trim(),
      pays: document.getElementById('lePays').value.trim(),
      password: document.getElementById('leMdp').value
    };

    if (!checkRequired('leNom', v.nom, "Nom obligatoire")) return;
    if (!checkRequired('lePrenom', v.prenom, "Prénom obligatoire")) return;
    if (!checkRequired('leMail', v.email, "Email obligatoire")) return;
    if (!checkRequired('laSociete', v.societe_id, "Société obligatoire")) return;
    if (!checkRequired('leRole', v.role_id, "Rôle obligatoire")) return;
    if (!checkRequired('leTel', v.telephone, "Téléphone obligatoire")) return;
    if (!checkRequired('leMdp', v.password, "Mot de passe obligatoire")) return;

    if (!checkRegex('leNom', v.nom, REGEX.nom, "Nom invalide")) return;
    if (!checkRegex('lePrenom', v.prenom, REGEX.prenom, "Prénom invalide")) return;
    if (!checkRegex('leMail', v.email, REGEX.email, "Email invalide")) return;
    const emailExists = await checkEmailExists(v.email);
    if (emailExists) {
      markField('leMail', false);
      showToast("Cet email est déjà utilisé", "error");
      document.getElementById('leMail').focus();
      return;
    }
    if (!checkRegex('leTel', v.telephone, REGEX.telFR, "Téléphone invalide (ex: 06 12 34 56 78 ou +33 6 12 34 56 78)")) return;

    if (!checkRegex('leGenre', v.civiliteValue, REGEX.civiliteValue, "Civilité invalide")) return;
    if (!checkRegex('leRole', String(v.role_id), REGEX.roleId, "Rôle invalide")) return;
    if (!checkRegex('laSociete', String(v.societe_id), REGEX.societeId, "Société invalide")) return;

    if (!checkRegex('leMdp', v.password, REGEX.password, "Mot de passe trop faible (min 8, maj/min/chiffre/spécial)")) return;

    if (!checkRequired('laAdresseComplete', v.num_rue, "Numéro de rue obligatoire")) return;
    if (!checkRequired('laAdresseComplete', v.nom_rue, "Nom de rue obligatoire")) return;
    if (!checkRequired('laAdresseComplete', v.code_postal, "Code postal obligatoire")) return;
    if (!checkRequired('laAdresseComplete', v.ville, "Ville obligatoire")) return;
    if (!checkRequired('laAdresseComplete', v.pays, "Pays obligatoire")) return;

    if (!checkRegex('laAdresseComplete', v.num_rue, REGEX.numRue, "Numéro de rue invalide (ex: 12, 12 bis, 12B)")) return;
    if (!checkRegex('laAdresseComplete', v.nom_rue, REGEX.nomRue, "Adresse invalide")) return;
    if (v.complement !== "" && !checkRegex('leComplement', v.complement, REGEX.complement, "Complément invalide")) return;
    if (!checkRegex('laAdresseComplete', v.code_postal, REGEX.codePostal, "Code postal invalide (5 chiffres)")) return;
    if (!checkRegex('laAdresseComplete', v.ville, REGEX.ville, "Ville invalide")) return;
    if (!checkRegex('laAdresseComplete', v.pays, REGEX.pays, "Pays invalide")) return;

    const civilite = v.civiliteValue === "1" ? "Monsieur" : v.civiliteValue === "2" ? "Madame" : "Iel";

    const data = {
      nom: v.nom,
      prenom: v.prenom,
      civilite,
      email: v.email,
      password: v.password,
      societe_id: v.societe_id === "" ? null : v.societe_id,
      role_id: parseInt(v.role_id, 10),
      telephone: v.telephone,
      num_rue: v.num_rue,
      nom_rue: v.nom_rue,
      complement: v.complement,
      code_postal: v.code_postal,
      ville: v.ville,
      pays: v.pays
    };

    const response = await fetch("../checkmystars/models/Create/users.php", {
      method: "POST",
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify(data)
    });

    const result = await response.json();
    if (result.success) {
      const addModalElement = document.getElementById('addModal');
      const addModal = bootstrap.Modal.getInstance(addModalElement);
      if (addModal) addModal.hide();
      clearValidationClasses('addForm');
      document.getElementById('addForm').reset();
      const list = await getInfos();
      console.log(list);
      remplirTab(list);
      showToast("Utilisateur créé avec succès !", "success");
    } else {
      showToast("Erreur lors de la création : " + result.error, "error");
    }
  } catch (error) {
    console.error("Erreur:", error);
    showToast("Une erreur s'est produite : " + error.message, "error");
  }
}

function showToast(message, type = 'success') {
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

function setupAdresseAutocomplete({ adresseCompleteId, numRueId, adresseId, codeId, villeId, paysId }) {
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

function markField(id, ok) {
  const el = document.getElementById(id);
  if (!el) return;
  el.classList.toggle('is-invalid', !ok);
  el.classList.toggle('is-valid', ok);
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

function addressBlockTouched(v) {
  return [v.num_rue, v.nom_rue, v.complement, v.code_postal, v.ville, v.pays].some(x => (x || "").trim() !== "");
}

async function checkEmailExists(email, excludeUserId = null) {
  try {
    const url = `../checkmystars/models/Read/checkEmail.php?email=${encodeURIComponent(email)}${excludeUserId ? `&excludeId=${excludeUserId}` : ''}`;
    const response = await fetch(url, {
      method: "GET",
      headers: { 'Content-Type': "application/json" }
    });
    const result = await response.json();
    return result.exists;
  } catch (error) {
    console.error("Erreur vérification email:", error);
    return false;
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

function resetModalForm(modalId, formId) {
  const modalElement = document.getElementById(modalId);
  if (!modalElement) return;
  
  modalElement.addEventListener('hidden.bs.modal', () => {
    clearValidationClasses(formId);
    const form = document.getElementById(formId);
    if (form) form.reset();
  });
  
  modalElement.addEventListener('show.bs.modal', () => {
    clearValidationClasses(formId);
  });
}

function openSocieteModal() {
    const addModalEl = document.getElementById('addModal');
    if (addModalEl) {
        const addModalInstance = bootstrap.Modal.getInstance(addModalEl);
        if (addModalInstance) {
            addModalInstance.hide();
        }
    }

    const modalEl = document.getElementById('addSocieteModal');
    if (!societeModal) {
        societeModal = new bootstrap.Modal(modalEl);
    }
    societeModal.show();
    document.getElementById('addSocieteForm').reset();
}

async function submitSociete() {
  const v = {
    nom: document.getElementById('societeNom').value.trim(),
    mail: document.getElementById('societeMail').value.trim(),
    tel: document.getElementById('societeTel').value.trim(),
    num_rue: document.getElementById('societeNumRue').value.trim(),
    nom_rue: document.getElementById('societeNomRue').value.trim(),
    complement: document.getElementById('societeComplement').value.trim(),
    code_postal: document.getElementById('societeCodePostal').value.trim(),
    ville: document.getElementById('societeVille').value.trim(),
    pays: document.getElementById('societePays').value.trim()
  };

  if (!checkRequired('societeNom', v.nom, "Nom société obligatoire")) return;
  if (!checkRegex('societeNom', v.nom, REGEX.nomRue, "Nom société invalide")) return;

  if (!v.num_rue || !v.nom_rue || !v.code_postal || !v.ville) {
    showToast("Adresse incomplète - utilisez l'autocomplétion", "warning");
    return;
  }

  const payload = {
    num_rue: v.num_rue,
    nom_rue: v.nom_rue,
    complement: v.complement || null,
    code_postal: v.code_postal,
    ville: v.ville,
    pays: v.pays,
    societe_nom: v.nom,
    societe_mail: v.mail || null,
    societe_telephone: v.tel || null
  };

  try {
    const response = await fetch('models/Create/company.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json;charset=utf-8' },
      body: JSON.stringify(payload)
    });

    const data = await response.json();

  if (data.success) {
    societeModal.hide();

    await refreshSocietes();

    const select = document.getElementById('laSociete');
    if (data.new_societe_id) {
        select.value = data.new_societe_id;
    } else if (data.new_user_id) {
        select.value = data.new_user_id;
    }

    const addModalEl = document.getElementById('addModal');
    const addModal = new bootstrap.Modal(addModalEl);
    addModal.show();
    
    showToast(`Société "${v.nom}" créée !`, "success");
    } else {
      showToast(data.error || "Erreur création société", "error");
    }
  } catch (error) {
    console.error(error);
    showToast("Erreur réseau", "error");
  }
}

async function refreshSocietes() {
    try {
        const response = await fetch('models/Read/companies.php');
        const companies = await response.json();
        
        const addSelect = document.getElementById('laSociete');
        if (addSelect) {
            const currentValue = addSelect.value;
            addSelect.innerHTML = '<option value="">Sélectionner...</option><option value="new_company">Créer une nouvelle entreprise</option>';
            
            companies.forEach(company => {
                const option = new Option(company.Societe_Nom, company.Societe_ID);
                addSelect.appendChild(option);
            });
            
            addSelect.value = currentValue;
        }
        
        const editSelect = document.getElementById('editLaSociete');
        if (editSelect) {
            const currentEditValue = editSelect.value;
            editSelect.innerHTML = '';
            companies.forEach(company => {
                const option = new Option(company.Societe_Nom, company.Societe_ID);
                editSelect.appendChild(option);
            });
            editSelect.value = currentEditValue;
        }
        
    } catch (error) {
        console.error('Erreur refresh sociétés:', error);
    }
}

function addCancel() {
    clearValidationClasses('addForm');
    document.getElementById('addForm').reset();
    const addModalElement = document.getElementById('addModal');
    const addModal = bootstrap.Modal.getInstance(addModalElement);
    if (addModal) {
        addModal.hide();
    }
}

function openSocieteModal() {
    const addModalEl = document.getElementById('addModal');
    if (addModalEl) {
        const addModalInstance = bootstrap.Modal.getInstance(addModalEl);
        if (addModalInstance) {
            addModalInstance.hide();
        }
    }

    const modalEl = document.getElementById('addSocieteModal');
    if (!societeModal) {
        societeModal = new bootstrap.Modal(modalEl);
    }
    societeModal.show();
    document.getElementById('addSocieteForm').reset();
}

async function submitSociete() {
  const v = {
    nom: document.getElementById('societeNom').value.trim(),
    mail: document.getElementById('societeMail').value.trim(),
    tel: document.getElementById('societeTel').value.trim(),
    num_rue: document.getElementById('societeNumRue').value.trim(),
    nom_rue: document.getElementById('societeNomRue').value.trim(),
    complement: document.getElementById('societeComplement').value.trim(),
    code_postal: document.getElementById('societeCodePostal').value.trim(),
    ville: document.getElementById('societeVille').value.trim(),
    pays: document.getElementById('societePays').value.trim()
  };

  if (!checkRequired('societeNom', v.nom, "Nom société obligatoire")) return;
  if (!checkRegex('societeNom', v.nom, REGEX.nomRue, "Nom société invalide")) return;

  if (!v.num_rue || !v.nom_rue || !v.code_postal || !v.ville) {
    showToast("Adresse incomplète - utilisez l'autocomplétion", "warning");
    return;
  }

  const payload = {
    num_rue: v.num_rue,
    nom_rue: v.nom_rue,
    complement: v.complement || null,
    code_postal: v.code_postal,
    ville: v.ville,
    pays: v.pays,
    societe_nom: v.nom,
    societe_mail: v.mail || null,
    societe_telephone: v.tel || null
  };

  try {
    const response = await fetch('models/Create/company.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json;charset=utf-8' },
      body: JSON.stringify(payload)
    });

    const data = await response.json();

  if (data.success) {
    societeModal.hide();

    await refreshSocietes();

    const select = document.getElementById('laSociete');
    if (data.new_societe_id) {
        select.value = data.new_societe_id;
    } else if (data.new_user_id) {
        select.value = data.new_user_id;
    }

    const addModalEl = document.getElementById('addModal');
    const addModal = new bootstrap.Modal(addModalEl);
    addModal.show();
    
    showToast(`Société "${v.nom}" créée !`, "success");
    } else {
      showToast(data.error || "Erreur création société", "error");
    }
  } catch (error) {
    console.error(error);
    showToast("Erreur réseau", "error");
  }
}

async function refreshSocietes() {
    try {
        const response = await fetch('models/Read/companies.php');
        const companies = await response.json();
        
        const addSelect = document.getElementById('laSociete');
        if (addSelect) {
            const currentValue = addSelect.value;
            addSelect.innerHTML = '<option value="">Sélectionner...</option><option value="new_company">Créer une nouvelle entreprise</option>';
            
            companies.forEach(company => {
                const option = new Option(company.Societe_Nom, company.Societe_ID);
                addSelect.appendChild(option);
            });
            
            addSelect.value = currentValue;
        }
        
        const editSelect = document.getElementById('editLaSociete');
        if (editSelect) {
            const currentEditValue = editSelect.value;
            editSelect.innerHTML = '';
            companies.forEach(company => {
                const option = new Option(company.Societe_Nom, company.Societe_ID);
                editSelect.appendChild(option);
            });
            editSelect.value = currentEditValue;
        }
        
    } catch (error) {
        console.error('Erreur refresh sociétés:', error);
    }
}

document.addEventListener("DOMContentLoaded", async function () {
  //quand la page est chargée
  const list = await getInfos();
  console.log(list);
  remplirTab(list);
  //remplissage du tablo

  setupAdresseAutocomplete({
    adresseCompleteId: "laAdresseComplete",
    numRueId: "leNumRue",
    adresseId: "laAdresse",
    codeId: "leCode",
    villeId: "laVille",
    paysId: "lePays"
  });

  setupAdresseAutocomplete({
    adresseCompleteId: "editLaAdresseComplete",
    numRueId: "editLeNumRue",
    adresseId: "editLaAdresse",
    codeId: "editLeCode",
    villeId: "editLaVille",
    paysId: "editLePays"
  });

  setupAdresseAutocomplete({
    adresseCompleteId: "societeAdresseComplete",
    numRueId: "societeNumRue",
    adresseId: "societeNomRue",
    codeId: "societeCodePostal",
    villeId: "societeVille",
    paysId: "societePays"
  });

  document.getElementById('laSociete').addEventListener('change', function () {
    if (this.value === 'new_company') {
        this.value = '';
        openSocieteModal();
    }
  });
});
