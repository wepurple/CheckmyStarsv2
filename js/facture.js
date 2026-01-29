/* =============================================
   GESTION DES FACTURES ET DEVIS - JAVASCRIPT
   ============================================= */

// Configuration des types de documents
const DOC_CONFIG = {
  FACTURE: {
    suffix: "",
    containerId: "lignes-container",
    sectionId: "section-facture",
  },
  DEVIS: {
    suffix: "_devis",
    containerId: "lignes-container-devis",
    buttonId: "btn-devis",
    sectionId: "section-devis",
  },
};

let selectedDocType = "DEVIS";
let previewTimeout;
let currentDevisNumero = null; // Numéro de devis réservé

// ========== FLAGS DE VERROUILLAGE ==========
let isInvoiceMode = false; // true si on affiche une facture
let isLocked = false; // true si le document est verrouillé (facture existante)
let currentDocumentId = null; // ID du document actuellement chargé

// ========== INITIALISATION ==========

document.addEventListener("DOMContentLoaded", function () {
  // Ajouter une première ligne vide pour chaque type
  addLigne("FACTURE");
  addLigne("DEVIS");

  // Ajouter les écouteurs pour les formulaires
  addEventListenersToForm("FACTURE");
  addEventListenersToForm("DEVIS");

  // Initialiser les boutons de basculement
  bindToggleButtons();

  // Charger la liste des devis et factures existants
  fetchDevisList();
  fetchFacturesList();

  // Charger la liste des entreprises
  loadEntreprisesList();

  // Afficher la section Devis par défaut et générer un numéro unique
  setDocType("DEVIS");
  initNewDevis();
});

// ========== GESTION DES LIGNES ==========

function addLigne(type = selectedDocType) {
  const config = DOC_CONFIG[type];
  if (!config) return;

  const id = `${type}-${Date.now()}`;
  const container = document.getElementById(config.containerId);
  if (!container) return;

  const ligneHTML = `
    <div class="ligne-item" id="ligne-${id}" data-doc-type="${type}">
      <div class="row">
        <div class="col-lg-6 col-md-12 mb-2">
          <label class="form-label">Description</label>
          <input type="text" class="form-control" placeholder="Description de la prestation"
                 data-id="${id}" data-doc="${type}" data-field="description" value="">
        </div>
        <div class="col-lg-2 col-md-6 col-sm-6 mb-2">
          <label class="form-label">Quantité</label>
          <input type="number" class="form-control" placeholder="1" min="0" step="0.01"
                 data-id="${id}" data-doc="${type}" data-field="quantite" value="1">
        </div>
        <div class="col-lg-2 col-md-6 col-sm-6 mb-2">
          <label class="form-label">Prix Unitaire</label>
          <input type="number" class="form-control" placeholder="0.00" min="0" step="0.01"
                 data-id="${id}" data-doc="${type}" data-field="prix_unitaire" value="0">
        </div>
        <div class="col-lg-1 col-md-6 col-sm-6 mb-2">
          <label class="form-label">TVA %</label>
          <select class="form-control" data-id="${id}" data-doc="${type}" data-field="tva_taux">
            <option value="0">0%</option>
            <option value="5.5">5.5%</option>
            <option value="20" selected>20%</option>
          </select>
        </div>
        <div class="col-lg-1 col-md-6 col-sm-6 d-flex align-items-end mb-2">
          <button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="removeLigne('${id}', '${type}')" title="Supprimer">
            <i class="fa-solid fa-trash"></i>
          </button>
        </div>
      </div>
    </div>`;

  container.insertAdjacentHTML("beforeend", ligneHTML);
  addEventListenersToLigne(id, type);
}

function removeLigne(id, type = selectedDocType) {
  const element = document.getElementById(`ligne-${id}`);
  if (element) {
    element.remove();
    updateTotals(type);
    updatePreview(type);
  }
}

function addEventListenersToLigne(id, type) {
  const inputs = document.querySelectorAll(
    `[data-id="${id}"][data-doc="${type}"]`,
  );
  inputs.forEach((input) => {
    input.addEventListener("change", () => {
      updateTotals(type);
      debouncedPreview(type);
    });
    input.addEventListener("keyup", () => {
      updateTotals(type);
      debouncedPreview(type);
    });
  });
}

function addEventListenersToForm(type) {
  const suffix = DOC_CONFIG[type]?.suffix ?? "";

  // Champs entreprise
  const entrepriseFields = [
    `entreprise_nom${suffix}`,
    `entreprise_siret${suffix}`,
    `entreprise_adresse${suffix}`,
    `entreprise_cp${suffix}`,
    `entreprise_ville${suffix}`,
    `entreprise_tel${suffix}`,
    `entreprise_tva${suffix}`,
  ];

  entrepriseFields.forEach((field) => {
    const el = document.getElementById(field);
    if (el) {
      el.addEventListener("change", () => debouncedPreview(type));
      el.addEventListener("keyup", () => debouncedPreview(type));
    }
  });

  // Champs document (facture/devis)
  const docFields =
    type === "FACTURE"
      ? ["facture_numero", "facture_date"]
      : ["devis_numero", "devis_date"];

  docFields.forEach((field) => {
    const el = document.getElementById(field);
    if (el) {
      el.addEventListener("change", () => debouncedPreview(type));
    }
  });

  // Champs client
  const clientFields = [
    `client_nom${suffix}`,
    `client_adresse${suffix}`,
    `client_cp${suffix}`,
    `client_ville${suffix}`,
  ];

  clientFields.forEach((field) => {
    const el = document.getElementById(field);
    if (el) {
      el.addEventListener("change", () => debouncedPreview(type));
      el.addEventListener("keyup", () => debouncedPreview(type));
    }
  });
}

// ========== CALCUL DES TOTAUX ==========

function updateTotals(type = selectedDocType) {
  const config = DOC_CONFIG[type];
  if (!config) return;

  let totalHT = 0;
  let totalTVA = 0;
  let totalTTC = 0;

  const container = document.getElementById(config.containerId);
  if (!container) return;

  const lignes = container.querySelectorAll(`[data-doc-type="${type}"]`);

  lignes.forEach((ligne) => {
    const description = ligne.querySelector('[data-field="description"]').value;
    const quantite =
      parseFloat(ligne.querySelector('[data-field="quantite"]').value) || 0;
    const prixUnitaire =
      parseFloat(ligne.querySelector('[data-field="prix_unitaire"]').value) ||
      0;
    const tvaTaux =
      parseFloat(ligne.querySelector('[data-field="tva_taux"]').value) || 0;

    const montantHT = quantite * prixUnitaire;
    const montantTVA = montantHT * (tvaTaux / 100);
    const montantTTC = montantHT + montantTVA;

    if (description) {
      totalHT += montantHT;
      totalTVA += montantTVA;
      totalTTC += montantTTC;
    }
  });

  const totalHtEl = document.getElementById("total_ht");
  const totalTvaEl = document.getElementById("total_tva");
  const totalTtcEl = document.getElementById("total_ttc");

  if (totalHtEl) totalHtEl.textContent = totalHT.toFixed(2) + " €";
  if (totalTvaEl) totalTvaEl.textContent = totalTVA.toFixed(2) + " €";
  if (totalTtcEl) totalTtcEl.textContent = totalTTC.toFixed(2) + " €";
}

// ========== PRÉVISUALISATION ==========

function debouncedPreview(type = selectedDocType) {
  clearTimeout(previewTimeout);
  previewTimeout = setTimeout(() => {
    updatePreview(type);
  }, 300);
}

function updatePreview(type = selectedDocType) {
  const previewDiv = document.getElementById("pdf-preview");
  const config = DOC_CONFIG[type];
  if (!previewDiv || !config) return;

  const suffix = config.suffix;

  // Récupérer les données entreprise
  const entreprise = {
    nom: getValue(`entreprise_nom${suffix}`),
    siret: getValue(`entreprise_siret${suffix}`),
    adresse: getValue(`entreprise_adresse${suffix}`),
    cp: getValue(`entreprise_cp${suffix}`),
    ville: getValue(`entreprise_ville${suffix}`),
    tel: getValue(`entreprise_tel${suffix}`),
    tva: getValue(`entreprise_tva${suffix}`),
  };

  // Récupérer les données document
  const documentInfo =
    type === "FACTURE"
      ? { numero: getValue("facture_numero"), date: getValue("facture_date") }
      : { numero: getValue("devis_numero"), date: getValue("devis_date") };

  // Récupérer les données client
  const client = {
    nom: getValue(`client_nom${suffix}`),
    adresse: getValue(`client_adresse${suffix}`),
    cp: getValue(`client_cp${suffix}`),
    ville: getValue(`client_ville${suffix}`),
  };

  const lignes = collectLines(type);

  // Calculer les totaux
  let totalHT = 0;
  let totalTVA = 0;
  let totalTTC = 0;
  lignes.forEach((ligne) => {
    totalHT += ligne.montant_ht;
    totalTVA += ligne.montant_tva;
    totalTTC += ligne.montant_ttc;
  });

  // Générer le HTML de prévisualisation
  const htmlPreview = generateHTMLPreview(
    {
      entreprise,
      documentInfo,
      client,
      lignes,
      totaux: { ht: totalHT, tva: totalTVA, ttc: totalTTC },
    },
    type,
  );

  previewDiv.innerHTML = htmlPreview;
  previewTimeout = null;
}

function generateHTMLPreview(data, docType) {
  const { entreprise, documentInfo, client, lignes, totaux } = data;
  const dateFormatted = documentInfo.date
    ? new Date(documentInfo.date).toLocaleDateString("fr-FR")
    : "";

  let lignesHTML = "";
  lignes.forEach((ligne) => {
    lignesHTML += `
      <tr>
        <td>${escapeHtml(ligne.description)}</td>
        <td class="text-right">${ligne.quantite.toFixed(2)}</td>
        <td class="text-right">${ligne.prix_unitaire.toFixed(2)} €</td>
        <td class="text-right">${ligne.montant_ht.toFixed(2)} €</td>
        <td class="text-right">${ligne.tva_taux.toFixed(1)}%</td>
        <td class="text-right">${ligne.montant_tva.toFixed(2)} €</td>
        <td class="text-right"><strong>${ligne.montant_ttc.toFixed(
          2,
        )} €</strong></td>
      </tr>`;
  });

  if (lignesHTML === "") {
    lignesHTML =
      '<tr><td colspan="7" style="text-align: center; color: #999; padding: 30px;">Aucune prestation ajoutée</td></tr>';
  }

  const docTitle = docType === "DEVIS" ? "DEVIS" : "FACTURE";

  return `
    <div class="pdf-preview-html">
      <div class="pdf-header">
        <div class="company-info">
          <h2>${escapeHtml(entreprise.nom)}</h2>
          <p><strong>${escapeHtml(entreprise.adresse)}</strong></p>
          <p>${escapeHtml(entreprise.cp)} ${escapeHtml(entreprise.ville)}</p>
          <p>Tél: ${escapeHtml(entreprise.tel)}</p>
          <p>SIRET: ${escapeHtml(entreprise.siret)}</p>
          <p>N° TVA: ${escapeHtml(entreprise.tva)}</p>
        </div>
        <div class="invoice-details">
          <h4>${docTitle}</h4>
          <p><strong>${escapeHtml(documentInfo.numero)}</strong></p>
          <p>Date: <strong>${dateFormatted}</strong></p>
        </div>
      </div>

      <div class="addresses">
        <div class="address-block">
          <h5>Adressé à:</h5>
          <p><strong>${escapeHtml(client.nom)}</strong></p>
          <p>${escapeHtml(client.adresse)}</p>
          <p>${escapeHtml(client.cp)} ${escapeHtml(client.ville)}</p>
        </div>
        <div class="address-block">
          <h5>Informations de facturation:</h5>
          <p>Même adresse</p>
        </div>
      </div>

      <table class="invoice-table">
        <thead>
          <tr>
            <th>Description</th>
            <th style="width: 80px;">Quantité</th>
            <th style="width: 100px;">Prix Unit.</th>
            <th style="width: 100px;">Montant HT</th>
            <th style="width: 60px;">TVA</th>
            <th style="width: 100px;">Montant TVA</th>
            <th style="width: 100px;">Total TTC</th>
          </tr>
        </thead>
        <tbody>
          ${lignesHTML}
        </tbody>
      </table>

      <div class="invoice-summary">
        <div class="summary-table">
          <tr>
            <td><strong>Total HT:</strong></td>
            <td style="text-align: right;"><strong>${totaux.ht.toFixed(
              2,
            )} €</strong></td>
          </tr>
          <tr>
            <td><strong>Total TVA:</strong></td>
            <td style="text-align: right;"><strong>${totaux.tva.toFixed(
              2,
            )} €</strong></td>
          </tr>
          <tr class="total-row">
            <td><strong>TOTAL TTC:</strong></td>
            <td style="text-align: right;"><strong>${totaux.ttc.toFixed(
              2,
            )} €</strong></td>
          </tr>
        </div>
      </div>

      <div class="invoice-notes">
        <strong>Conditions de paiement:</strong> Net 30 jours<br>
        IBAN : XXXX XXXX XXXX XXXX XXXX XX<br>
        BIC : XXXXXXXXXXX
      </div>
    </div>`;
}

// ========== EXPORT PDF ==========

function downloadPDF(type = selectedDocType) {
  const loading = document.getElementById("loadingOverlay");
  loading.classList.add("active");

  const config = DOC_CONFIG[type];
  const suffix = config?.suffix ?? "";

  const entreprise = {
    nom: getValue(`entreprise_nom${suffix}`),
    siret: getValue(`entreprise_siret${suffix}`),
    adresse: getValue(`entreprise_adresse${suffix}`),
    cp: getValue(`entreprise_cp${suffix}`),
    ville: getValue(`entreprise_ville${suffix}`),
    tel: getValue(`entreprise_tel${suffix}`),
    tva: getValue(`entreprise_tva${suffix}`),
  };

  const documentInfo =
    type === "FACTURE"
      ? { numero: getValue("facture_numero"), date: getValue("facture_date") }
      : { numero: getValue("devis_numero"), date: getValue("devis_date") };

  const client = {
    nom: getValue(`client_nom${suffix}`),
    adresse: getValue(`client_adresse${suffix}`),
    cp: getValue(`client_cp${suffix}`),
    ville: getValue(`client_ville${suffix}`),
  };

  const lignesRaw = collectLines(type);
  const lignes = lignesRaw.map((ligne) => ({
    description: ligne.description,
    quantite: ligne.quantite.toString(),
    prix_unitaire: ligne.prix_unitaire.toString(),
    tva_taux: ligne.tva_taux.toString(),
    montant_ht: ligne.montant_ht.toFixed(2),
    montant_tva: ligne.montant_tva.toFixed(2),
    montant_ttc: ligne.montant_ttc.toFixed(2),
  }));

  if (lignes.length === 0) {
    alert("Veuillez ajouter au moins une prestation avant de générer le PDF");
    loading.classList.remove("active");
    return;
  }

  // Endpoint selon le type de document
  const endpoint =
    type === "FACTURE" ? "generate_facture_pdf.php" : "generate_devis_pdf.php";

  fetch(endpoint, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      entreprise,
      [type === "FACTURE" ? "facture" : "devis"]: documentInfo,
      client,
      lignes,
      type: type,
    }),
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error("Erreur lors de la génération du PDF");
      }
      return response.blob();
    })
    .then((blob) => {
      const url = window.URL.createObjectURL(blob);
      const link = document.createElement("a");
      link.href = url;
      const prefix = type === "FACTURE" ? "facture" : "devis";
      link.download = `${prefix}_${documentInfo.numero.replace(/\//g, "-")}_${
        documentInfo.date
      }.pdf`;
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
      window.URL.revokeObjectURL(url);

      loading.classList.remove("active");
    })
    .catch((error) => {
      console.error("Erreur:", error);
      alert("Erreur lors de la génération du PDF: " + error.message);
      loading.classList.remove("active");
    });
}

// ========== SAUVEGARDE EN BDD ==========

function saveDevis(type = selectedDocType) {
  // === SÉCURITÉ : Bloquer si mode facture verrouillée ===
  if (isInvoiceMode && isLocked) {
    alert("❌ Impossible de modifier une facture existante.");
    return;
  }

  const loading = document.getElementById("loadingOverlay");
  if (loading) loading.classList.add("active");

  const config = DOC_CONFIG[type];
  const suffix = config?.suffix ?? "";

  // Validation préalable
  const errors = [];

  const documentInfo =
    type === "FACTURE"
      ? { numero: getValue("facture_numero"), date: getValue("facture_date") }
      : { numero: getValue("devis_numero"), date: getValue("devis_date") };

  if (!documentInfo.numero || documentInfo.numero === "Génération...") {
    errors.push("Numéro de document manquant");
  }

  if (!documentInfo.date) {
    errors.push("Date du document manquante");
  }

  const entrepriseSelect = document.getElementById("entreprise_select_devis");
  const entrepriseId =
    entrepriseSelect && entrepriseSelect.value
      ? parseInt(entrepriseSelect.value)
      : null;

  if (!entrepriseId) {
    errors.push("Sélectionnez une entreprise");
  }

  const lignes = collectLines(type);
  if (lignes.length === 0) {
    errors.push("Ajoutez au moins une prestation");
  }

  if (errors.length > 0) {
    alert("❌ Erreurs:\n• " + errors.join("\n• "));
    if (loading) loading.classList.remove("active");
    return;
  }

  // Calcul et validation du total
  let totalTTC = 0;
  lignes.forEach((l) => (totalTTC += l.montant_ttc));

  if (totalTTC <= 0) {
    alert("❌ Le montant total doit être > 0");
    if (loading) loading.classList.remove("active");
    return;
  }

  const client = {
    nom: getValue(`client_nom${suffix}`),
    adresse: getValue(`client_adresse${suffix}`),
    cp: getValue(`client_cp${suffix}`),
    ville: getValue(`client_ville${suffix}`),
    utilisateur_id: getClientUserId(), // Récupérer ID utilisateur
  };

  fetch("api/devis_api.php?action=save_devis", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      type,
      documentInfo,
      client,
      lignes,
      totalTTC,
      entrepriseId,
    }),
  })
    .then((r) => r.json())
    .then((res) => {
      if (!res.success) throw new Error(res.message || "Échec sauvegarde");
      alert(`✅ ${type} sauvegardé : ${documentInfo.numero}`);
      fetchDevisList(); // Rafraîchir la liste déroulante
      // Réinitialiser le numéro réservé
      currentDevisNumero = null;
    })
    .catch((err) => {
      console.error(err);
      alert("❌ Erreur sauvegarde: " + err.message);
    })
    .finally(() => {
      if (loading) loading.classList.remove("active");
    });
}

// Charger un devis sélectionné
function loadDevisFromDropdown() {
  const select = document.getElementById("devisSelector");
  if (!select || !select.value) {
    alert("Sélectionnez un devis à charger.");
    return;
  }

  const devisId = select.value;
  const loading = document.getElementById("loadingOverlay");
  if (loading) loading.classList.add("active");

  fetch(`api/devis_api.php?action=get_devis&id=${encodeURIComponent(devisId)}`)
    .then((r) => r.json())
    .then((data) => {
      if (!data.success)
        throw new Error(data.message || "Chargement devis impossible");
      hydrateFormFromDevis(data.devis);
    })
    .catch((err) => {
      console.error(err);
      alert("❌ Erreur chargement devis: " + err.message);
    })
    .finally(() => {
      if (loading) loading.classList.remove("active");
    });
}

function hydrateFormFromDevis(devis) {
  if (!devis) return;

  // Type de document enregistré
  const type = devis.Devis_Document === "FACTURE" ? "FACTURE" : "DEVIS";

  // Réinitialiser les flags - un devis est modifiable (sauf si verrouillé)
  const devisVerrouille =
    devis.Devis_Verrouille === 1 ||
    devis.Devis_Verrouille === "1" ||
    devis.locked;

  if (type === "DEVIS" && !devisVerrouille) {
    // Mode édition pour les devis non verrouillés
    isInvoiceMode = false;
    isLocked = false;
    currentDocumentId = devis.Devis_ID;
    setDocType(type);
    setReadOnlyMode(false, type);
    showLockedBadge(false);
  } else {
    // Mode lecture seule pour devis verrouillé ou facture
    isInvoiceMode = type === "FACTURE";
    isLocked = true;
    currentDocumentId = devis.Devis_ID;
    setDocType(type);
    setReadOnlyMode(true, type);
    showLockedBadge(true, devis.Devis_Numero);
  }

  const suffix = DOC_CONFIG[type]?.suffix ?? "";

  // Numéro et date
  const numeroField = type === "FACTURE" ? "facture_numero" : "devis_numero";
  const dateField = type === "FACTURE" ? "facture_date" : "devis_date";
  const dateVal = devis.Devis_DateEmission
    ? devis.Devis_DateEmission.substring(0, 10)
    : "";
  const numEl = document.getElementById(numeroField);
  const dateEl = document.getElementById(dateField);
  if (numEl) numEl.value = devis.Devis_Numero || "";
  if (dateEl) dateEl.value = dateVal;

  // Entreprise - sélectionner dans le dropdown si disponible
  if (devis.Entreprise_ID || devis.entreprise) {
    const entrepriseId = devis.Entreprise_ID || devis.entreprise?.Entreprise_ID;
    const entrepriseSelect = document.getElementById("entreprise_select_devis");
    if (entrepriseSelect && entrepriseId) {
      entrepriseSelect.value = entrepriseId;
      // Déclencher le chargement des données
      if (typeof loadEntrepriseData === "function") {
        loadEntrepriseData();
      }
    }
  }

  // Client - sélectionner dans le dropdown si Utilisateur_ID disponible
  if (devis.client) {
    const c = devis.client;
    const clientSelect = document.getElementById(`client_nom${suffix}`);

    // Si on a un Utilisateur_ID, sélectionner l'option correspondante
    if (c.Utilisateur_ID && clientSelect) {
      const options = clientSelect.options;
      for (let i = 0; i < options.length; i++) {
        if (options[i].getAttribute("data-id") == c.Utilisateur_ID) {
          clientSelect.selectedIndex = i;
          // Charger les données client
          if (typeof test === "function") {
            test();
          }
          break;
        }
      }
    }

    // Remplir les champs manuellement (fallback ou données legacy)
    const map = {
      [`client_nom${suffix}`]: c.nom || "",
      [`client_adresse${suffix}`]: c.adresse || "",
      [`client_cp${suffix}`]: c.code_postal || c.cp || "",
      [`client_ville${suffix}`]: c.ville || "",
    };
    Object.entries(map).forEach(([id, val]) => {
      const el = document.getElementById(id);
      // Ne pas écraser si c'est un select et qu'on a déjà sélectionné
      if (el && el.tagName !== "SELECT") {
        el.value = val;
      }
    });
  }

  // Lignes
  setLines(type, devis.items || []);

  // Totaux + preview
  updateTotals(type);
  updatePreview(type);
}

function setLines(type, items) {
  const config = DOC_CONFIG[type];
  if (!config) return;
  const container = document.getElementById(config.containerId);
  if (!container) return;

  container.innerHTML = "";
  if (!items || items.length === 0) {
    addLigne(type);
    return;
  }

  items.forEach((it) => {
    addLigne(type);
    const ligne = container.lastElementChild;
    if (!ligne) return;
    const setVal = (sel, val) => {
      const el = ligne.querySelector(sel);
      if (el) el.value = val;
    };
    setVal('[data-field="description"]', it.description || "");
    setVal(
      '[data-field="quantite"]',
      it.quantite || it.quantite === 0 ? it.quantite : "",
    );
    setVal('[data-field="prix_unitaire"]', it.prix_unitaire || "");
    setVal('[data-field="tva_taux"]', it.tva || it.tva_taux || 0);
  });
}

function fetchDevisList() {
  const select = document.getElementById("devisSelector");
  if (!select) return;

  fetch("api/devis_api.php?action=list_devis")
    .then((r) => r.json())
    .then((data) => {
      if (!data.success)
        throw new Error(data.message || "Impossible de lister les devis");
      select.innerHTML =
        '<option value="">-- Sélectionner un devis --</option>';
      data.devis.forEach((d) => {
        const opt = document.createElement("option");
        opt.value = d.Devis_ID;
        opt.textContent = `${d.Devis_Numero} (${d.Devis_Document})`;
        select.appendChild(opt);
      });
    })
    .catch((err) => {
      console.error(err);
      select.innerHTML = '<option value="">Erreur de chargement</option>';
    });
}

/**
 * Initialise un nouveau devis avec un numéro unique pré-généré
 */
function initNewDevis() {
  // Réinitialiser les flags de verrouillage
  resetLockState();

  // Réinitialiser le formulaire
  resetDevisForm();

  // Basculer en mode DEVIS avec édition activée
  setDocType("DEVIS");
  setReadOnlyMode(false, "DEVIS");

  // Masquer le badge verrouillé
  showLockedBadge(false);

  // Générer un nouveau numéro unique
  fetchNewDevisNumber();
}

/**
 * Réinitialiser l'état de verrouillage (retour au mode édition)
 */
function resetLockState() {
  isInvoiceMode = false;
  isLocked = false;
  currentDocumentId = null;
  window.currentFactureId = null;
}

/**
 * Récupère un nouveau numéro de devis unique depuis l'API
 */
function fetchNewDevisNumber() {
  const input = document.getElementById("devis_numero");
  if (!input) return;

  // Marquer comme en cours de chargement
  input.value = "Génération...";
  input.readOnly = true;

  fetch("api/devis_api.php?action=new_devis_number")
    .then((r) => {
      if (!r.ok) {
        throw new Error(`HTTP ${r.status}: ${r.statusText}`);
      }
      return r.text(); // Lire comme texte d'abord
    })
    .then((text) => {
      // Essayer de parser comme JSON
      try {
        const data = JSON.parse(text);
        if (!data.success)
          throw new Error(data.message || "Erreur génération numéro");
        currentDevisNumero = data.numero;
        input.value = data.numero;
        input.readOnly = true; // Garder readonly pour éviter modification
        // Réinitialiser le dropdown
        const select = document.getElementById("devisSelector");
        if (select) select.value = "";
        // Mettre à jour la prévisualisation
        updatePreview("DEVIS");
      } catch (parseErr) {
        // Si ce n'est pas du JSON, c'est probablement une erreur PHP
        console.error("Réponse serveur (pas du JSON):", text);
        throw new Error(
          "Erreur serveur (réponse invalide). Vérifiez la console pour les détails.",
        );
      }
    })
    .catch((err) => {
      console.error("Erreur fetchNewDevisNumber:", err);
      input.value = "";
      input.readOnly = false; // En cas d'erreur, permettre saisie manuelle
      alert("❌ Impossible de générer un numéro de devis:\n" + err.message);
    });
}

/**
 * Réinitialise le formulaire devis
 */
function resetDevisForm() {
  // Champs document
  document.getElementById("devis_date").value = new Date()
    .toISOString()
    .split("T")[0];

  // Champs client
  const clientFields = [
    "client_nom_devis",
    "client_adresse_devis",
    "client_cp_devis",
    "client_ville_devis",
  ];
  clientFields.forEach((id) => {
    const el = document.getElementById(id);
    if (el) el.value = "";
  });

  // Vider les lignes et en ajouter une nouvelle
  const container = document.getElementById("lignes-container-devis");
  if (container) {
    container.innerHTML = "";
    addLigne("DEVIS");
  }

  // Désactiver le mode readonly
  setReadOnlyMode(false, "DEVIS");

  // Réinitialiser les totaux
  updateTotals("DEVIS");
}

// Expose to inline onclick handler
window.saveDevis = saveDevis;
window.loadDevisFromDropdown = loadDevisFromDropdown;
window.initNewDevis = initNewDevis;
window.updatePreview = updatePreview;
window.downloadPDF = downloadPDF;
window.addLigne = addLigne;
window.handlePreview = handlePreview;
window.resetLockState = resetLockState;

// Exposer les flags de verrouillage (lecture seule)
Object.defineProperty(window, "isInvoiceMode", { get: () => isInvoiceMode });
Object.defineProperty(window, "isLocked", { get: () => isLocked });

// ========== CONVERSION DEVIS → FACTURE ==========

/**
 * Convertir le devis actuellement chargé en facture
 */
function convertDevisToFacture() {
  const select = document.getElementById("devisSelector");
  const devisId = select ? select.value : null;

  if (!devisId) {
    alert("Veuillez d'abord sélectionner et charger un devis à convertir.");
    return;
  }

  if (!confirm("Convertir ce devis en facture ? Le devis sera verrouillé.")) {
    return;
  }

  const loading = document.getElementById("loadingOverlay");
  if (loading) loading.classList.add("active");

  fetch(
    `api/devis_api.php?action=convert&devis_id=${encodeURIComponent(devisId)}`,
    {
      method: "POST",
      headers: { "Content-Type": "application/json" },
    },
  )
    .then((r) => r.json())
    .then((res) => {
      if (!res.success) throw new Error(res.message || "Échec conversion");

      if (res.already_exists) {
        alert(`ℹ️ Une facture existe déjà : ${res.numeroFacture}`);
      } else {
        alert(
          `✅ Facture créée !\nNuméro: ${res.numeroFacture}\nTotal: ${res.total?.toFixed(2) || 0} €`,
        );
      }

      // Recharger les listes
      fetchDevisList();
      fetchFacturesList();

      // Afficher la facture créée
      loadFacture(res.factureId);
    })
    .catch((err) => {
      console.error(err);
      alert("❌ Erreur conversion: " + err.message);
    })
    .finally(() => {
      if (loading) loading.classList.remove("active");
    });
}

/**
 * Charger une facture (mode READ-ONLY)
 */
function loadFacture(factureId) {
  if (!factureId) {
    const select = document.getElementById("factureSelector");
    factureId = select ? select.value : null;
  }

  if (!factureId) {
    alert("Sélectionnez une facture à charger.");
    return;
  }

  const loading = document.getElementById("loadingOverlay");
  if (loading) loading.classList.add("active");

  fetch(
    `api/devis_api.php?action=get_facture&id=${encodeURIComponent(factureId)}`,
  )
    .then((r) => r.json())
    .then((data) => {
      if (!data.success)
        throw new Error(data.message || "Chargement impossible");
      hydrateFormFromFacture(data.facture);
    })
    .catch((err) => {
      console.error(err);
      alert("❌ Erreur chargement facture: " + err.message);
    })
    .finally(() => {
      if (loading) loading.classList.remove("active");
    });
}

/**
 * Remplir le formulaire avec une facture (mode lecture seule)
 */
function hydrateFormFromFacture(facture) {
  if (!facture) return;

  // Définir les flags de verrouillage
  isInvoiceMode = true;
  isLocked = true;
  currentDocumentId = facture.Facture_ID;

  // Basculer en mode FACTURE
  setDocType("FACTURE");

  // Activer le mode lecture seule (masque les sections d'édition)
  setReadOnlyMode(true, "FACTURE");

  // Remplir les champs
  const numEl = document.getElementById("facture_numero");
  const dateEl = document.getElementById("facture_date");

  if (numEl) {
    numEl.value = facture.Facture_Numero || "";
    numEl.setAttribute("readonly", true);
  }

  if (dateEl) {
    const dateVal = facture.Facture_DateCreation
      ? facture.Facture_DateCreation.substring(0, 10)
      : "";
    dateEl.value = dateVal;
    dateEl.setAttribute("readonly", true);
  }

  // Client
  if (facture.client) {
    const c = facture.client;
    ["nom", "adresse", "email", "telephone"].forEach((field) => {
      const el = document.getElementById(`client_${field}`);
      if (el) {
        el.value = c[field] || "";
        el.setAttribute("readonly", true);
      }
    });
  }

  // Lignes (lecture seule)
  setLinesReadOnly("FACTURE", facture.items || []);

  // Afficher badge "Facture verrouillée"
  showLockedBadge(true, facture.Facture_Numero);

  // Mettre à jour l'aperçu
  updateTotals("FACTURE");
  updatePreview("FACTURE");

  // Stocker l'ID de la facture chargée
  window.currentFactureId = facture.Facture_ID;
}

/**
 * Afficher les lignes en mode lecture seule
 */
function setLinesReadOnly(type, items) {
  const config = DOC_CONFIG[type];
  if (!config) return;
  const container = document.getElementById(config.containerId);
  if (!container) return;

  container.innerHTML = "";
  if (!items || items.length === 0) return;

  items.forEach((it) => {
    const id = `${type}-readonly-${Date.now()}-${Math.random().toString(36).substr(2, 5)}`;

    const ligneHTML = `
      <div class="ligne-item ligne-readonly" id="ligne-${id}" data-doc-type="${type}">
        <div class="row">
          <div class="col-lg-6 col-md-12 mb-2">
            <label class="form-label">Description</label>
            <input type="text" class="form-control" value="${escapeHtml(it.description || "")}" 
                   data-field="description" readonly disabled>
          </div>
          <div class="col-lg-2 col-md-6 col-sm-6 mb-2">
            <label class="form-label">Quantité</label>
            <input type="number" class="form-control" value="${it.quantite || 0}" 
                   data-field="quantite" readonly disabled>
          </div>
          <div class="col-lg-2 col-md-6 col-sm-6 mb-2">
            <label class="form-label">Prix Unitaire</label>
            <input type="number" class="form-control" value="${it.prix_unitaire || 0}" 
                   data-field="prix_unitaire" readonly disabled>
          </div>
          <div class="col-lg-1 col-md-6 col-sm-6 mb-2">
            <label class="form-label">TVA %</label>
            <input type="text" class="form-control" value="${it.tva || 20}%" 
                   data-field="tva_taux" readonly disabled>
          </div>
          <div class="col-lg-1 col-md-6 col-sm-6 d-flex align-items-end mb-2">
            <span class="badge bg-secondary w-100 p-2">
              <i class="bi bi-lock"></i>
            </span>
          </div>
        </div>
      </div>`;

    container.insertAdjacentHTML("beforeend", ligneHTML);
  });
}

/**
 * Activer/désactiver le mode lecture seule
 * Si locked=true pour une facture, masque complètement les zones d'édition
 */
function setReadOnlyMode(readonly, type = selectedDocType) {
  const config = DOC_CONFIG[type];
  const suffix = config?.suffix ?? "";

  // Mettre à jour les flags globaux
  isLocked = readonly;
  isInvoiceMode = type === "FACTURE" && readonly;

  // Le champ numéro de devis reste TOUJOURS readonly
  const devisNumeroEl = document.getElementById("devis_numero");
  if (devisNumeroEl) devisNumeroEl.readOnly = true;

  // === AJOUTER/RETIRER LA CLASSE readonly-mode SUR LE WRAPPER ===
  const wrapper = document.getElementById("facture-wrapper");
  const mobileBar = document.getElementById("mobile-action-bar");
  const formulaireSection = document.getElementById("formulaire-section");

  if (wrapper) {
    if (readonly && type === "FACTURE") {
      wrapper.classList.add("readonly-mode");
    } else {
      wrapper.classList.remove("readonly-mode");
    }
  }

  // Masquer explicitement la section formulaire en mode facture
  if (formulaireSection) {
    if (readonly && type === "FACTURE") {
      formulaireSection.style.display = "none";
    } else {
      formulaireSection.style.display = "";
    }
  }

  if (mobileBar) {
    if (readonly && type === "FACTURE") {
      mobileBar.classList.add("readonly-mode");
    } else {
      mobileBar.classList.remove("readonly-mode");
    }
  }

  // === MASQUER/AFFICHER LES SECTIONS D'ÉDITION ===
  // Accordions de formulaire (entreprise, client, etc.)
  const accordionFacture = document.getElementById(
    "formulaireAccordionFacture",
  );
  const accordionDevis = document.getElementById("formulaireAccordionDevis");

  if (type === "FACTURE" && accordionFacture) {
    accordionFacture.style.display = readonly ? "none" : "";
  }
  if (type === "DEVIS" && accordionDevis) {
    accordionDevis.style.display = readonly ? "none" : "";
  }

  // Conteneur des lignes de prestations (masquer en mode facture verrouillée)
  const lignesAccordionFacture = document.querySelector(
    "#section-facture .accordion-item:has(#lignes-container)",
  );
  const lignesAccordionDevis = document.querySelector(
    "#section-devis .accordion-item:has(#lignes-container-devis)",
  );

  // Alternative si :has() n'est pas supporté
  const prestationsFacture = document
    .getElementById("lignes-container")
    ?.closest(".accordion-item");
  const prestationsDevis = document
    .getElementById("lignes-container-devis")
    ?.closest(".accordion-item");

  if (type === "FACTURE" && prestationsFacture) {
    prestationsFacture.style.display = readonly ? "none" : "";
  }
  if (type === "DEVIS" && prestationsDevis) {
    prestationsDevis.style.display = readonly ? "none" : "";
  }

  // Boutons d'action - masquer si verrouillé (desktop)
  const btnAddLineFacture = document.querySelector(
    '#section-facture [onclick*="addLigne"]',
  );
  const btnAddLineDevis = document.querySelector(
    '#section-devis [onclick*="addLigne"]',
  );
  const btnSave = document.getElementById("btn-save-devis");
  const btnConvert = document.getElementById("btn-convert-facture");
  const btnActualiser = document.getElementById("btn-actualiser");

  // Boutons mobile
  const btnSaveMobile = document.getElementById("btn-save-mobile");
  const btnConvertMobile = document.getElementById("btn-convert-mobile");

  if (btnAddLineFacture)
    btnAddLineFacture.style.display =
      type === "FACTURE" && readonly ? "none" : "";
  if (btnAddLineDevis)
    btnAddLineDevis.style.display = type === "DEVIS" && readonly ? "none" : "";
  if (btnSave) btnSave.style.display = readonly ? "none" : "";
  if (btnConvert) btnConvert.style.display = readonly ? "none" : "";
  if (btnActualiser) btnActualiser.style.display = readonly ? "none" : "";

  // Mobile buttons
  if (btnSaveMobile) btnSaveMobile.style.display = readonly ? "none" : "";
  if (btnConvertMobile) btnConvertMobile.style.display = readonly ? "none" : "";

  // Bouton télécharger PDF - toujours visible
  const btnDownload = document.querySelector('[onclick*="downloadPDF"]');
  if (btnDownload) btnDownload.style.display = "";

  // Mettre à jour le titre de l'aperçu
  const previewTitle = document.getElementById("preview-title");
  const previewCard = previewTitle?.closest(".card");
  const previewHeader = previewCard?.querySelector(".card-header");

  if (previewTitle) {
    if (type === "FACTURE" && readonly) {
      // En mode lecture seule, masquer le header de carte (redondant avec le badge)
      if (previewHeader) previewHeader.style.display = "none";
    } else {
      if (previewHeader) previewHeader.style.display = "";
      previewTitle.textContent =
        type === "FACTURE" ? "Aperçu de la Facture" : "Aperçu du Devis";
    }
  }

  // NE PAS AFFICHER DE BADGE ICI - c'est géré par showLockedBadge()
  // Vider le conteneur readonly-badge-container pour éviter les doublons
  const badgeContainer = document.getElementById("readonly-badge-container");
  if (badgeContainer) {
    badgeContainer.innerHTML = "";
  }

  window.isReadOnlyMode = readonly;
}

/**
 * Afficher/masquer le badge "Verrouillé"
 */
function showLockedBadge(show, numero = "") {
  const container = document.getElementById("readonly-badge-container");

  if (!container) return;

  if (show) {
    container.innerHTML = `
      <div class="readonly-badge" role="alert">
        <i class="fa-solid fa-lock"></i>
        <span>Facture <strong>${escapeHtml(numero)}</strong> — Mode lecture seule</span>
      </div>
    `;
  } else {
    container.innerHTML = "";
  }
}

/**
 * Réinitialiser pour créer un nouveau document
 */
function resetToNewDocument() {
  // Désactiver le mode lecture seule
  setReadOnlyMode(false);
  showLockedBadge(false);

  // Réinitialiser les champs
  const type = selectedDocType;
  const suffix = DOC_CONFIG[type]?.suffix ?? "";

  // Numéro et date
  const numeroField = type === "FACTURE" ? "facture_numero" : "devis_numero";
  const dateField = type === "FACTURE" ? "facture_date" : "devis_date";

  const numEl = document.getElementById(numeroField);
  const dateEl = document.getElementById(dateField);

  if (numEl) {
    numEl.value = "";
    numEl.removeAttribute("readonly");
  }
  if (dateEl) {
    dateEl.value = new Date().toISOString().substring(0, 10);
    dateEl.removeAttribute("readonly");
  }

  // Client
  ["nom", "adresse", "cp", "ville"].forEach((field) => {
    const el = document.getElementById(`client_${field}${suffix}`);
    if (el) {
      el.value = "";
      el.removeAttribute("readonly");
    }
  });

  // Lignes
  const container = document.getElementById(DOC_CONFIG[type].containerId);
  if (container) container.innerHTML = "";
  addLigne(type);

  // Réinitialiser les sélecteurs
  const devisSelector = document.getElementById("devisSelector");
  const factureSelector = document.getElementById("factureSelector");
  if (devisSelector) devisSelector.value = "";
  if (factureSelector) factureSelector.value = "";

  // Clear stored IDs
  window.currentFactureId = null;
  window.currentDevisId = null;

  updateTotals(type);
  updatePreview(type);
}

/**
 * Télécharger le PDF d'une facture existante (read-only)
 */
function downloadFacturePDF() {
  const factureId = window.currentFactureId;
  if (!factureId) {
    alert("Aucune facture chargée.");
    return;
  }

  const loading = document.getElementById("loadingOverlay");
  if (loading) loading.classList.add("active");

  // Utiliser l'API PDF existante avec les données de la facture chargée
  downloadPDF("FACTURE");
}

/**
 * Charger la liste des factures
 */
function fetchFacturesList() {
  const select = document.getElementById("factureSelector");
  if (!select) return;

  fetch("api/devis_api.php?action=list_factures")
    .then((r) => r.json())
    .then((data) => {
      if (!data.success)
        throw new Error(data.message || "Impossible de lister les factures");
      select.innerHTML =
        '<option value="">-- Sélectionner une facture --</option>';
      data.factures.forEach((f) => {
        const opt = document.createElement("option");
        opt.value = f.Facture_ID;
        const montant = f.Facture_Montant
          ? ` - ${parseFloat(f.Facture_Montant).toFixed(2)}€`
          : "";
        opt.textContent = `${f.Facture_Numero}${montant}`;
        select.appendChild(opt);
      });
    })
    .catch((err) => {
      console.error(err);
      select.innerHTML = '<option value="">Erreur de chargement</option>';
    });
}

// ========== CHARGEMENT ENTREPRISES ET CLIENTS ==========

/**
 * Charger la liste des entreprises dans le select
 */
function loadEntreprisesList() {
  const select = document.getElementById("entreprise_select_devis");
  if (!select) return;

  fetch("api/devis_api.php?action=list_entreprises")
    .then((r) => r.json())
    .then((data) => {
      if (!data.success) throw new Error(data.message);

      // Garder l'option vide par défaut
      const currentValue = select.value;
      select.innerHTML =
        '<option value="">-- Sélectionner une entreprise --</option>';

      data.entreprises.forEach((e) => {
        const opt = document.createElement("option");
        opt.value = e.Entreprise_ID;
        opt.textContent = e.Entreprise_Nom;
        select.appendChild(opt);
      });

      // Restaurer la sélection
      if (currentValue) select.value = currentValue;
    })
    .catch((err) => {
      console.error("Erreur chargement entreprises:", err);
    });
}

/**
 * Charger les données d'une entreprise sélectionnée
 * Appelle: api/devis_api.php?action=get_entreprise&id=...
 */
async function loadEntrepriseData() {
  const select = document.getElementById("entreprise_select_devis");
  if (!select || !select.value) {
    console.warn("Aucune entreprise sélectionnée");
    return;
  }

  const entrepriseId = select.value;

  try {
    // Récupérer les données de l'entreprise
    const response = await fetch(
      `api/devis_api.php?action=get_entreprise&id=${encodeURIComponent(entrepriseId)}`,
    );

    if (!response.ok) throw new Error("Erreur chargement entreprise");

    const data = await response.json();
    if (!data.success) throw new Error(data.message);

    const entreprise = data.entreprise;

    // Remplir les champs entreprise
    const suffix = DOC_CONFIG[selectedDocType]?.suffix ?? "";

    [
      ["entreprise_nom", "Entreprise_Nom"],
      ["entreprise_siret", "Entreprise_SIRET"],
      ["entreprise_adresse", "Entreprise_Adresse"],
      ["entreprise_cp", "Entreprise_CodePostal"],
      ["entreprise_ville", "Entreprise_Ville"],
      ["entreprise_tel", "Entreprise_Telephone"],
      ["entreprise_tva", "Entreprise_TVA_Intra"],
    ].forEach(([fieldId, dbField]) => {
      const el = document.getElementById(fieldId + suffix);
      if (el) el.value = entreprise[dbField] || "";
    });

    updatePreview(selectedDocType);
  } catch (error) {
    console.error("Erreur loadEntrepriseData:", error);
    alert("❌ Erreur chargement entreprise: " + error.message);
  }
}

/**
 * Charger les données d'un client sélectionné
 * Appelle: api/devis_api.php?action=get_client_info&id=...
 */
async function loadClientData() {
  const select = document.getElementById("client_nom_devis");
  if (!select || !select.value) {
    console.warn("Aucun client sélectionné");
    return;
  }

  // Récupérer l'ID utilisateur depuis l'attribut data-id, pas le nom
  const utilisateurId =
    select.options[select.selectedIndex]?.getAttribute("data-id");
  if (!utilisateurId) {
    console.warn("ID utilisateur non trouvé");
    return;
  }

  try {
    const response = await fetch(
      `api/devis_api.php?action=get_client_info&id=${encodeURIComponent(utilisateurId)}`,
    );

    if (!response.ok) throw new Error("Erreur chargement client");

    const data = await response.json();
    if (!data.success) throw new Error(data.message);

    const client = data.client;
    const suffix = DOC_CONFIG[selectedDocType]?.suffix ?? "";

    // Remplir les champs client
    [
      ["client_nom", "nom_complet"],
      ["client_adresse", "adresse"],
      ["client_cp", "code_postal"],
      ["client_ville", "ville"],
    ].forEach(([fieldId, dbField]) => {
      const el = document.getElementById(fieldId + suffix);
      if (el) el.value = client[dbField] || "";
    });

    updatePreview(selectedDocType);
  } catch (error) {
    console.error("Erreur loadClientData:", error);
    alert("❌ Erreur chargement client: " + error.message);
  }
}

window.loadEntreprisesList = loadEntreprisesList;
window.loadEntrepriseData = loadEntrepriseData;
window.loadClientData = loadClientData;
window.test = loadClientData; // Compatibilité avec facture.php

// Exposer les nouvelles fonctions
window.convertDevisToFacture = convertDevisToFacture;
window.loadFacture = loadFacture;
window.resetToNewDocument = resetToNewDocument;
window.downloadFacturePDF = downloadFacturePDF;
window.fetchFacturesList = fetchFacturesList;

// ========== TOGGLE FACTURE / DEVIS ==========

function bindToggleButtons() {
  const btnDevis = document.getElementById(DOC_CONFIG.DEVIS.buttonId);
  if (btnDevis) btnDevis.addEventListener("click", () => setDocType("DEVIS"));
}

function setDocType(type) {
  if (!DOC_CONFIG[type]) return;
  selectedDocType = type;

  // Mettre à jour le bouton devis (actif seulement en mode DEVIS)
  const btnDevis = document.getElementById(DOC_CONFIG.DEVIS.buttonId);
  if (btnDevis) {
    if (type === "DEVIS") {
      btnDevis.classList.add("btn-success");
      btnDevis.classList.remove("btn-outline-success");
    } else {
      btnDevis.classList.add("btn-outline-success");
      btnDevis.classList.remove("btn-success");
    }
  }

  // Afficher/masquer les sections
  const sectionFacture = document.getElementById(DOC_CONFIG.FACTURE.sectionId);
  const sectionDevis = document.getElementById(DOC_CONFIG.DEVIS.sectionId);
  if (sectionFacture && sectionDevis) {
    sectionFacture.classList.toggle("d-none", type !== "FACTURE");
    sectionDevis.classList.toggle("d-none", type !== "DEVIS");
  }

  // Mettre à jour le titre de l'aperçu
  const previewTitle = document.getElementById("preview-title");
  if (previewTitle) {
    previewTitle.textContent =
      type === "DEVIS" ? "Aperçu du Devis" : "Aperçu de la Facture";
  }

  // Rafraîchir l'aperçu
  updatePreview(type);
}

function handlePreview(type) {
  setDocType(type);
}

// ========== UTILITAIRES ==========

/**
 * Récupérer l'ID utilisateur du client sélectionné
 */
function getClientUserId() {
  const suffix = DOC_CONFIG[selectedDocType]?.suffix ?? "";
  const clientSelect = document.getElementById(`client_nom${suffix}`);
  if (!clientSelect || clientSelect.selectedIndex <= 0) return null;
  return (
    parseInt(
      clientSelect.options[clientSelect.selectedIndex].getAttribute("data-id"),
    ) || null
  );
}

function collectLines(type = selectedDocType) {
  const config = DOC_CONFIG[type];
  if (!config) return [];

  const container = document.getElementById(config.containerId);
  if (!container) return [];

  const lignes = [];
  container.querySelectorAll(`[data-doc-type="${type}"]`).forEach((ligne) => {
    const description = ligne.querySelector('[data-field="description"]').value;
    const quantite =
      parseFloat(ligne.querySelector('[data-field="quantite"]').value) || 0;
    const prixUnitaire =
      parseFloat(ligne.querySelector('[data-field="prix_unitaire"]').value) ||
      0;
    const tvaTaux =
      parseFloat(ligne.querySelector('[data-field="tva_taux"]').value) || 0;

    if (description && quantite > 0 && prixUnitaire > 0) {
      const montantHT = quantite * prixUnitaire;
      const montantTVA = montantHT * (tvaTaux / 100);
      const montantTTC = montantHT + montantTVA;

      lignes.push({
        description,
        quantite,
        prix_unitaire: prixUnitaire,
        tva_taux: tvaTaux,
        montant_ht: montantHT,
        montant_tva: montantTVA,
        montant_ttc: montantTTC,
      });
    }
  });

  return lignes;
}

function getValue(id) {
  const el = document.getElementById(id);
  return el ? el.value : "";
}

function escapeHtml(text) {
  const map = {
    "&": "&amp;",
    "<": "&lt;",
    ">": "&gt;",
    '"': "&quot;",
    "'": "&#039;",
  };
  return text.replace(/[&<>"']/g, (m) => map[m]);
}
