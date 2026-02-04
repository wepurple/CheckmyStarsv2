async function preFillClientInfo()
{
    var clientLastName = document.getElementById("leNom");
    var clientFirstName = document.getElementById("lePrenom");
    var clientCompany = document.getElementById("laSociete");

    const currentUrl = window.location.search;
    var query = new URLSearchParams(currentUrl);
    var clientId = query.get('id');

    var clientInformation = await getUserById(clientId);

    clientLastName.value = clientInformation.Utilisateur_Nom;
    clientFirstName.value = clientInformation.Utilisateur_Prenom;
    clientCompany.value = clientInformation.Societe_Nom;

    console.log(clientInformation);
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

document.addEventListener("DOMContentLoaded", () => {
    preFillClientInfo()
});