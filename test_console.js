<!-- Test console JavaScript pour facture.js -->
<!-- Coller dans la console JS (F12) pour tester -->

// ========== TEST 1: Vérifier que facture.js est chargé ==========
console.log('✅ facture.js chargé:', typeof DOC_CONFIG !== 'undefined');
console.log('DOC_CONFIG:', DOC_CONFIG);

// ========== TEST 2: Vérifier les fonctions existent ==========
console.log('✅ loadEntreprisesList exists:', typeof loadEntreprisesList === 'function');
console.log('✅ loadEntrepriseData exists:', typeof loadEntrepriseData === 'function');
console.log('✅ loadClientData exists:', typeof loadClientData === 'function');
console.log('✅ fetchNewDevisNumber exists:', typeof fetchNewDevisNumber === 'function');

// ========== TEST 3: Tester l'API directement ==========
fetch("api/devis_api.php?action=new_devis_number")
  .then(r => {
    console.log('HTTP Status:', r.status);
    console.log('Content-Type:', r.headers.get('content-type'));
    return r.text();
  })
  .then(text => {
    console.log('Raw response:', text);
    try {
      const data = JSON.parse(text);
      console.log('✅ Parsed JSON:', data);
    } catch(e) {
      console.error('❌ Not valid JSON:', e.message);
      console.log('Response preview:', text.substring(0, 200));
    }
  })
  .catch(err => console.error('❌ Fetch error:', err));

// ========== TEST 4: Vérifier les éléments DOM ==========
console.log('✅ #devis_numero element:', document.getElementById('devis_numero') !== null);
console.log('✅ #entreprise_select_devis element:', document.getElementById('entreprise_select_devis') !== null);
console.log('✅ #client_nom_devis element:', document.getElementById('client_nom_devis') !== null);

// ========== TEST 5: Appeler directement la fonction ==========
console.log('Calling fetchNewDevisNumber()...');
fetchNewDevisNumber();
// Attendre 2-3 secondes et vérifier le champ #devis_numero
