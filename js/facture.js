/* =============================================
   GESTION DES FACTURES ET DEVIS - JAVASCRIPT
   ============================================= */

// Configuration des types de documents
const DOC_CONFIG = {
  FACTURE: {
    suffix: "",
    containerId: "lignes-container",
    buttonId: "btn-facture",
    sectionId: "section-facture",
  },
  DEVIS: {
    suffix: "_devis",
    containerId: "lignes-container-devis",
    buttonId: "btn-devis",
    sectionId: "section-devis",
  },
};

let selectedDocType = "FACTURE";
let previewTimeout;

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

  // Afficher la section Facture par défaut
  setDocType("FACTURE");
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
          <button type="button" class="btn-remove-ligne w-100" onclick="removeLigne('${id}', '${type}')" title="Supprimer">
            <i class="bi bi-trash"></i>
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
    `[data-id="${id}"][data-doc="${type}"]`
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
    type
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
          2
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
              2
            )} €</strong></td>
          </tr>
          <tr>
            <td><strong>Total TVA:</strong></td>
            <td style="text-align: right;"><strong>${totaux.tva.toFixed(
              2
            )} €</strong></td>
          </tr>
          <tr class="total-row">
            <td><strong>TOTAL TTC:</strong></td>
            <td style="text-align: right;"><strong>${totaux.ttc.toFixed(
              2
            )} €</strong></td>
          </tr>
        </div>
      </div>

      <div class="invoice-notes">
        <strong>Conditions de paiement:</strong> Net 30 jours<br>
        Merci de votre confiance!
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

// ========== TOGGLE FACTURE / DEVIS ==========

function bindToggleButtons() {
  const btnFacture = document.getElementById(DOC_CONFIG.FACTURE.buttonId);
  const btnDevis = document.getElementById(DOC_CONFIG.DEVIS.buttonId);

  if (btnFacture)
    btnFacture.addEventListener("click", () => setDocType("FACTURE"));
  if (btnDevis) btnDevis.addEventListener("click", () => setDocType("DEVIS"));
}

function setDocType(type) {
  if (!DOC_CONFIG[type]) return;
  selectedDocType = type;

  // Mettre à jour les boutons
  const btnFacture = document.getElementById(DOC_CONFIG.FACTURE.buttonId);
  const btnDevis = document.getElementById(DOC_CONFIG.DEVIS.buttonId);
  if (btnFacture && btnDevis) {
    if (type === "FACTURE") {
      btnFacture.classList.add("btn-primary");
      btnFacture.classList.remove("btn-outline-primary");
      btnDevis.classList.add("btn-outline-primary");
      btnDevis.classList.remove("btn-primary");
    } else {
      btnDevis.classList.add("btn-primary");
      btnDevis.classList.remove("btn-outline-primary");
      btnFacture.classList.add("btn-outline-primary");
      btnFacture.classList.remove("btn-primary");
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

// Auto-refresh toutes les 5 secondes si inactif
setInterval(() => {
  if (!previewTimeout) {
    updatePreview(selectedDocType);
  }
}, 5000);
