<?php
session_start();

// Récupération de l'ID en toute sécurité
$dossier_id = isset($_GET['id']) ? intval($_GET['id']) : null;

if ($dossier_id) {
    // Préparation de la requête pour récupérer les infos du dossier
    $stmt = $pdo->prepare("SELECT * FROM dossiers WHERE Dossier_ID = ?");
    $stmt->execute([$dossier_id]);
    $dossier = $stmt->fetch();
}
?>


<!DOCTYPE html>
<html lang="fr" data-bs-theme="dark">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Gestion dossier - CheckMyStars</title>

        <link rel="stylesheet" href="bootstrap 5.3/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <script src="bootstrap 5.3/js/bootstrap.js"></script>
        <script src="js/search_inspecteurs.js"></script>
        <link rel="icon" type="image/x-icon" href="pictures/logosm.png">
    </head>

    <body class="bg-secondary">
    <?php require("./includes/navbar.php"); ?>

    <div class="container-fluid p-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalUpdate">
                <i class="fas fa-edit"></i> Modifier le dossier
            </button>

            <a href="detail_client.php?id=<?php echo $dossier['Client_ID'] ?? ''; ?>" class="btn btn-light">
                <i class="fas fa-arrow-left"></i> Retour au tableau de bord
            </a>

            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalDelete">
                <i class="fas fa-trash"></i> Supprimer
            </button>
        </div>

        <section class="information bg-dark text-white p-4 rounded shadow">
            <?php if ($dossier): ?>
                <h3>Dossier n° <?php echo htmlspecialchars($dossier['Dossier_Numero']); ?></h3>
                <hr>
                <p><strong>Statut :</strong> <?php echo htmlspecialchars($dossier['Dossier_Statut']); ?></p>
                <?php else: ?>
                <div class="alert alert-warning">Dossier introuvable.</div>
            <?php endif; ?>
        </section>
    </div>



    

    <div class="modal fade" id="modalDelete" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger">Confirmer la suppression</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir supprimer définitivement le dossier <strong><?php echo htmlspecialchars($dossier['Dossier_Numero'] ?? ''); ?></strong> ?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <a href="delete_dossier.php?id=<?php echo $dossier_id; ?>" class="btn btn-danger">Supprimer</a>
                </div>
            </div>
        </div>
    </div>

</body>
</html>