# Documentation - Gestion des Factures

## 📋 Fonctionnalités Principales

### 1. **Interface Modernisée**

- Design épuré et professionnel avec Bootstrap 5.3
- Disposition à deux colonnes :
  - **Gauche** : Formulaire de saisie
  - **Droite** : Prévisualisation en temps réel

### 2. **Prévisualisation en Temps Réel**

- Mise à jour automatique de l'aperçu au fur et à mesure de la saisie
- Debounce de 300ms pour éviter les appels excessifs
- Affichage HTML proche du PDF final

### 3. **Gestion des Prestations**

- Ajout/suppression dynamique de lignes
- Calcul automatique des montants HT, TVA et TTC
- Support de plusieurs taux de TVA (0%, 5.5%, 20%)
- Validation des champs avant génération du PDF

### 4. **Génération de PDF**

- PDF professionnel avec en-tête personnalisé
- Détails entreprise et client
- Tableau détaillé des prestations
- Résumé des totaux en surbrillance
- Conditions de paiement
- Numérotation automatique des factures

---

## 🎯 Utilisation

### Création d'une Facture

1. **Remplir les informations entreprise**

   - Nom, SIRET, adresse, ville, code postal
   - Numéro de téléphone, numéro TVA

2. **Remplir les informations de la facture**

   - Numéro de facture (ex: FACT-2026-001)
   - Date de facturation

3. **Saisir les informations du client**

   - Raison sociale
   - Adresse complète

4. **Ajouter les prestations**

   - Cliquer sur "Ajouter une prestation"
   - Remplir pour chaque ligne :
     - Description de la prestation
     - Quantité
     - Prix unitaire HT
     - Taux de TVA applicable

5. **Vérifier l'aperçu**

   - L'aperçu se met à jour automatiquement
   - Vérifier les montants et totaux

6. **Télécharger le PDF**
   - Cliquer sur "Télécharger PDF"
   - Le fichier est téléchargé automatiquement

---

## 🛠️ Architecture Technique

### Fichiers Principaux

#### [bootstrap 5.3/css/facture.css](bootstrap%205.3/css/facture.css)

- Styles complets pour l'interface
- Design responsive (desktop, tablette, mobile)
- Animations et transitions fluides
- Mise en page flexible

#### [js/facture.js](js/facture.js)

- Gestion dynamique des lignes
- Calcul des totaux en temps réel
- Prévisualisation HTML
- Envoi des données au serveur pour PDF
- Gestion du chargement et des erreurs

#### [generate_facture_pdf.php](generate_facture_pdf.php)

- Réception et validation des données JSON
- Génération du PDF avec TCPDF
- Mise en page professionnelle
- Téléchargement sécurisé

#### [facture.php](facture.php)

- Page principale
- Formulaires HTML
- Structure de la double colonne
- Intégration Bootstrap

---

## 📱 Responsive Design

L'interface s'adapte automatiquement à la taille de l'écran :

- **Desktop (>1200px)** : Formulaire et aperçu côte à côte
- **Tablette (768-1199px)** : Aperçu encore visible mais réduit
- **Mobile (<768px)** : Interface en colonne unique

---

## ⚙️ Configuration

### Couleurs Principale

- Couleur primaire : `#2563eb` (Bleu)
- Couleur secondaire : `#1e40af` (Bleu foncé)
- Couleur succès : `#10b981` (Vert)
- Couleur danger : `#ef4444` (Rouge)

### Taux de TVA

Les taux de TVA disponibles dans les prestations :

- 0% (Exonération)
- 5.5% (Réduit)
- 20% (Normal)

Pour modifier ces valeurs, éditez les `<option>` dans le sélect TVA du JavaScript.

---

## 🔒 Sécurité

- Validation des données côté client
- Filtrage et échappement des données côté serveur
- Gestion des erreurs appropriée
- CORS et header Content-Type corrects

---

## 📊 Format du PDF Généré

Le PDF contient les sections suivantes :

1. **En-tête**

   - Logo/Nom entreprise
   - Informations entreprise
   - Numéro et date de facture

2. **Adresses**

   - Informations entreprise
   - Adresse du client

3. **Corps**

   - Tableau détaillé des prestations
   - Colonne : Description, Quantité, P.U., Montant HT, TVA, Montant TVA

4. **Résumé**

   - Total HT
   - Total TVA
   - **Total TTC** (mise en évidence)

5. **Pied**
   - Conditions de paiement
   - Informations légales

---

## 🐛 Dépannage

### Le PDF ne se génère pas

- Vérifier que TCPDF est installé (dans `vendor/`)
- Vérifier les permissions du répertoire
- Vérifier la console du navigateur pour les erreurs

### La prévisualisation ne s'affiche pas

- Vérifier la connexion réseau
- Vérifier les fichiers CSS/JS sont chargés
- Consulter la console du navigateur

### Montants incorrects

- Vérifier que les champs quantité et prix sont numériques
- Vérifier que le taux TVA est correct
- Vérifier la formule de calcul en console

---

## 📝 Notes

- Les données du formulaire ne sont pas sauvegardées en base de données
- Chaque génération de PDF est indépendante
- L'interface supporte plusieurs devises (euros par défaut)

---

**Version** : 1.0  
**Date de création** : 15 janvier 2026  
**Dernière mise à jour** : 15 janvier 2026
