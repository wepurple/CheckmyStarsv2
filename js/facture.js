/* =============================================
   GESTION DES FACTURES - JAVASCRIPT
   ============================================= */

// Données globales
let lignesFacture = [];
let previewTimeout;

// Initialisation
document.addEventListener("DOMContentLoaded", function () {
  // Ajouter une première ligne vide
  addLigne();

  // Ajouter des écouteurs pour mise à jour en temps réel
  addEventListenersToForm();
});

// ========== GESTION DES LIGNES ==========

function addLigne() {
  const id = Date.now(); // ID unique basé sur le timestamp

  const container = document.getElementById("lignes-container");

  const ligneHTML = `
        <div class="ligne-item" id="ligne-${id}">
            <div class="row">
                <div class="col-lg-6 col-md-12 mb-2">
                    <label class="form-label">Description</label>
                    <input type="text" class="form-control" placeholder="Description de la prestation" 
                           data-id="${id}" data-field="description" value="">
                </div>
                <div class="col-lg-2 col-md-6 col-sm-6 mb-2">
                    <label class="form-label">Quantité</label>
                    <input type="number" class="form-control" placeholder="1" min="0" step="0.01"
                           data-id="${id}" data-field="quantite" value="1">
                </div>
                <div class="col-lg-2 col-md-6 col-sm-6 mb-2">
                    <label class="form-label">Prix Unitaire</label>
                    <input type="number" class="form-control" placeholder="0.00" min="0" step="0.01"
                           data-id="${id}" data-field="prix_unitaire" value="0">
                </div>
                <div class="col-lg-1 col-md-6 col-sm-6 mb-2">
                    <label class="form-label">TVA %</label>
                    <select class="form-control" data-id="${id}" data-field="tva_taux">
                        <option value="0">0%</option>
                        <option value="5.5">5.5%</option>
                        <option value="20" selected>20%</option>
                    </select>
                </div>
                <div class="col-lg-1 col-md-6 col-sm-6 d-flex align-items-end mb-2">
                    <button type="button" class="btn-remove-ligne w-100" onclick="removeLigne('${id}')" title="Supprimer">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `;

  container.insertAdjacentHTML("beforeend", ligneHTML);

  // Ajouter les écouteurs aux champs de la nouvelle ligne
  addEventListenersToLigne(id);
}

function removeLigne(id) {
  const element = document.getElementById(`ligne-${id}`);
  if (element) {
    element.remove();
    updateTotals();
    updatePreview();
  }
}

function addEventListenersToLigne(id) {
  const inputs = document.querySelectorAll(`[data-id="${id}"]`);

  inputs.forEach((input) => {
    input.addEventListener("change", function () {
      updateTotals();
      debouncedPreview();
    });
    input.addEventListener("keyup", function () {
      updateTotals();
      debouncedPreview();
    });
  });
}

function addEventListenersToForm() {
  // Formulaire entreprise
  const entrepriseFields = [
    "entreprise_nom",
    "entreprise_siret",
    "entreprise_adresse",
    "entreprise_cp",
    "entreprise_ville",
    "entreprise_tel",
    "entreprise_tva",
  ];

  entrepriseFields.forEach((field) => {
    const el = document.getElementById(field);
    if (el) {
      el.addEventListener("change", debouncedPreview);
      el.addEventListener("keyup", debouncedPreview);
    }
  });

  // Formulaire facture
  const factureFields = ["facture_numero", "facture_date"];

  factureFields.forEach((field) => {
    const el = document.getElementById(field);
    if (el) {
      el.addEventListener("change", debouncedPreview);
    }
  });

  // Formulaire client
  const clientFields = [
    "client_nom",
    "client_adresse",
    "client_cp",
    "client_ville",
  ];

  clientFields.forEach((field) => {
    const el = document.getElementById(field);
    if (el) {
      el.addEventListener("change", debouncedPreview);
      el.addEventListener("keyup", debouncedPreview);
    }
  });
}

// ========== CALCUL DES TOTAUX ==========

function updateTotals() {
  let totalHT = 0;
  let totalTVA = 0;
  let totalTTC = 0;

  const lignes = document.querySelectorAll(".ligne-item");

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

    totalHT += montantHT;
    totalTVA += montantTVA;
    totalTTC += montantTTC;
  });

  // Mettre à jour l'affichage
  document.getElementById("total_ht").textContent = totalHT.toFixed(2) + " €";
  document.getElementById("total_tva").textContent = totalTVA.toFixed(2) + " €";
  document.getElementById("total_ttc").textContent = totalTTC.toFixed(2) + " €";
}

// ========== PRÉVISUALISATION ==========

function debouncedPreview() {
  clearTimeout(previewTimeout);
  previewTimeout = setTimeout(() => {
    updatePreview();
  }, 300); // Attendre 300ms après la dernière saisie
}

function updatePreview() {
  const previewDiv = document.getElementById("pdf-preview");

  // Récupérer les données
  const entreprise = {
    nom: document.getElementById("entreprise_nom").value,
    siret: document.getElementById("entreprise_siret").value,
    adresse: document.getElementById("entreprise_adresse").value,
    cp: document.getElementById("entreprise_cp").value,
    ville: document.getElementById("entreprise_ville").value,
    tel: document.getElementById("entreprise_tel").value,
    tva: document.getElementById("entreprise_tva").value,
  };

  const facture = {
    numero: document.getElementById("facture_numero").value,
    date: document.getElementById("facture_date").value,
  };

  const client = {
    nom: document.getElementById("client_nom").value,
    adresse: document.getElementById("client_adresse").value,
    cp: document.getElementById("client_cp").value,
    ville: document.getElementById("client_ville").value,
  };

  // Récupérer les lignes
  const lignes = [];
  document.querySelectorAll(".ligne-item").forEach((ligne) => {
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

  // Calculer les totaux
  let totalHT = 0,
    totalTVA = 0,
    totalTTC = 0;
  lignes.forEach((ligne) => {
    totalHT += ligne.montant_ht;
    totalTVA += ligne.montant_tva;
    totalTTC += ligne.montant_ttc;
  });

  // Générer le HTML de prévisualisation
  const htmlPreview = generateHTMLPreview({
    entreprise,
    facture,
    client,
    lignes,
    totaux: { ht: totalHT, tva: totalTVA, ttc: totalTTC },
  });

  previewDiv.innerHTML = htmlPreview;
}

function generateHTMLPreview(data) {
  const { entreprise, facture, client, lignes, totaux } = data;
  const dateFormatted = new Date(facture.date).toLocaleDateString("fr-FR");

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
            </tr>
        `;
  });

  if (lignesHTML === "") {
    lignesHTML =
      '<tr><td colspan="7" style="text-align: center; color: #999; padding: 30px;">Aucune prestation ajoutée</td></tr>';
  }

  return `
        <div class="pdf-preview-html">
            <!-- En-tête -->
            <div class="pdf-header">
                <div class="company-info">
                    <h2>${escapeHtml(entreprise.nom)}</h2>
                    <p><strong>${escapeHtml(entreprise.adresse)}</strong></p>
                    <p>${escapeHtml(entreprise.cp)} ${escapeHtml(
    entreprise.ville
  )}</p>
                    <p>Tél: ${escapeHtml(entreprise.tel)}</p>
                    <p>SIRET: ${escapeHtml(entreprise.siret)}</p>
                    <p>N° TVA: ${escapeHtml(entreprise.tva)}</p>
                </div>
                <div class="invoice-details">
                    <h4>FACTURE</h4>
                    <p><strong>${escapeHtml(facture.numero)}</strong></p>
                    <p>Date: <strong>${dateFormatted}</strong></p>
                </div>
            </div>
            
            <!-- Adresses -->
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
            
            <!-- Tableau des prestations -->
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
            
            <!-- Résumé des totaux -->
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
            
            <!-- Notes -->
            <div class="invoice-notes">
                <strong>Conditions de paiement:</strong> Net 30 jours<br>
                Merci de votre confiance!
            </div>
        </div>
    `;
}

// ========== EXPORT PDF ==========

function downloadPDF() {
  const loading = document.getElementById("loadingOverlay");
  loading.classList.add("active");

  // Récupérer les données
  const entreprise = {
    nom: document.getElementById("entreprise_nom").value,
    siret: document.getElementById("entreprise_siret").value,
    adresse: document.getElementById("entreprise_adresse").value,
    cp: document.getElementById("entreprise_cp").value,
    ville: document.getElementById("entreprise_ville").value,
    tel: document.getElementById("entreprise_tel").value,
    tva: document.getElementById("entreprise_tva").value,
  };

  const facture = {
    numero: document.getElementById("facture_numero").value,
    date: document.getElementById("facture_date").value,
  };

  const client = {
    nom: document.getElementById("client_nom").value,
    adresse: document.getElementById("client_adresse").value,
    cp: document.getElementById("client_cp").value,
    ville: document.getElementById("client_ville").value,
  };

  // Récupérer les lignes
  const lignes = [];
  document.querySelectorAll(".ligne-item").forEach((ligne) => {
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
        quantite: quantite.toString(),
        prix_unitaire: prixUnitaire.toString(),
        tva_taux: tvaTaux.toString(),
        montant_ht: montantHT.toFixed(2),
        montant_tva: montantTVA.toFixed(2),
        montant_ttc: montantTTC.toFixed(2),
      });
    }
  });

  if (lignes.length === 0) {
    alert("Veuillez ajouter au moins une prestation avant de générer le PDF");
    loading.classList.remove("active");
    return;
  }

  // Envoyer les données au serveur
  fetch("generate_facture_pdf.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      entreprise,
      facture,
      client,
      lignes,
    }),
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error("Erreur lors de la génération du PDF");
      }
      return response.blob();
    })
    .then((blob) => {
      // Créer un lien de téléchargement
      const url = window.URL.createObjectURL(blob);
      const link = document.createElement("a");
      link.href = url;
      link.download = `facture_${facture.numero.replace(/\//g, "-")}_${
        facture.date
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

// ========== UTILITAIRES ==========

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

// Auto-mettre à jour la prévisualisation toutes les 5 secondes en cas d'inactivité
setInterval(() => {
  if (!previewTimeout) {
    updatePreview();
  }
}, 5000);
