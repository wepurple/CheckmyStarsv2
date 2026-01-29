<!DOCTYPE html>
<html>
<head>
    <title>Test API Devis</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .test-box { background: white; padding: 20px; margin: 10px 0; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        button { padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 3px; cursor: pointer; }
        button:hover { background: #0056b3; }
        .error { color: red; font-weight: bold; }
        .success { color: green; font-weight: bold; }
        pre { background: #f8f8f8; padding: 10px; border-left: 3px solid #007bff; overflow-x: auto; }
        #output { white-space: pre-wrap; }
    </style>
</head>
<body>
    <h1>🧪 Test API Devis</h1>
    
    <div class="test-box">
        <h2>Test 1: Vérifier table document_counters</h2>
        <button onclick="testTableExists()">Tester</button>
        <div id="test1-output"></div>
    </div>

    <div class="test-box">
        <h2>Test 2: Générer un numéro de devis</h2>
        <button onclick="testNewDevisNumber()">Générer numéro</button>
        <div id="test2-output"></div>
    </div>

    <div class="test-box">
        <h2>Test 3: Lister les entreprises</h2>
        <button onclick="testListEntreprises()">Lister</button>
        <div id="test3-output"></div>
    </div>

    <script>
        async function testTableExists() {
            const output = document.getElementById('test1-output');
            output.innerHTML = 'Test en cours...';
            
            try {
                const response = await fetch('api/devis_api.php?action=new_devis_number');
                const text = await response.text();
                
                output.innerHTML = '<pre>';
                output.innerHTML += `Status: ${response.status}\n`;
                output.innerHTML += `Headers: Content-Type = ${response.headers.get('content-type')}\n\n`;
                output.innerHTML += 'Réponse serveur:\n';
                output.innerHTML += text;
                output.innerHTML += '</pre>';
                
                // Essayer de parser
                try {
                    const data = JSON.parse(text);
                    if (data.success) {
                        output.innerHTML += `<p class="success">✅ Table exists et fonctionnelle!</p>`;
                        output.innerHTML += `<p>Numéro généré: <strong>${data.numero}</strong></p>`;
                    } else {
                        output.innerHTML += `<p class="error">❌ Erreur: ${data.message}</p>`;
                    }
                } catch (e) {
                    output.innerHTML += `<p class="error">❌ Réponse non-JSON (probablement une erreur PHP)</p>`;
                }
            } catch (err) {
                output.innerHTML = `<p class="error">❌ Erreur: ${err.message}</p>`;
            }
        }

        async function testNewDevisNumber() {
            const output = document.getElementById('test2-output');
            output.innerHTML = 'Génération en cours...';
            
            try {
                const response = await fetch('api/devis_api.php?action=new_devis_number');
                const data = await response.json();
                
                if (data.success) {
                    output.innerHTML = `<p class="success">✅ Succès!</p>`;
                    output.innerHTML += `<p>Numéro: <strong>${data.numero}</strong></p>`;
                    output.innerHTML += `<pre>${JSON.stringify(data, null, 2)}</pre>`;
                } else {
                    output.innerHTML = `<p class="error">❌ Erreur: ${data.message}</p>`;
                    output.innerHTML += `<pre>${JSON.stringify(data, null, 2)}</pre>`;
                }
            } catch (err) {
                output.innerHTML = `<p class="error">❌ Erreur: ${err.message}</p>`;
            }
        }

        async function testListEntreprises() {
            const output = document.getElementById('test3-output');
            output.innerHTML = 'Chargement...';
            
            try {
                const response = await fetch('api/devis_api.php?action=list_entreprises');
                const data = await response.json();
                
                if (data.success) {
                    output.innerHTML = `<p class="success">✅ ${data.entreprises.length} entreprises trouvées</p>`;
                    output.innerHTML += `<pre>${JSON.stringify(data, null, 2)}</pre>`;
                } else {
                    output.innerHTML = `<p class="error">❌ Erreur: ${data.message}</p>`;
                }
            } catch (err) {
                output.innerHTML = `<p class="error">❌ Erreur: ${err.message}</p>`;
            }
        }
    </script>
</body>
</html>
