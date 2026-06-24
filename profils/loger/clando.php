<?php
session_start();

if (empty($_SESSION['username']) && empty($_SESSION['mdp'])) {
    header('Location: /campuscoud.com/');
    exit();
}

include('../../traitement/fonction.php');
connexionBD();
include('../../traitement/requete.php');

verif_type_mdp_2($_SESSION['username']);

/* ==========================
   GESTION DES MESSAGES
========================== */
$_SESSION['erreurValider'] = $_GET['erreurValider'] ?? '';
$_SESSION['successValider'] = $_GET['successValider'] ?? '';
$_SESSION['erreurNonTrouver'] = $_GET['erreurNonTrouver'] ?? '';
$_SESSION['erreurForclo'] = $_GET['erreurForclo'] ?? '';

/* ==========================
   RECUP DATA (JSON FIX)
========================== */
$data = null;

if (isset($_GET['data'])) {
    $data = $_GET['data'] ?? [];
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>COUD: CLANDO</title>
    <link rel="stylesheet" href="../../assets/css/main.css">
    <!-- script================================================== -->
    <script src="../../assets/js/modernizr.js"></script>
    <script src="../../assets/js/pace.min.js"></script>
    <link rel="stylesheet" href="../../assets/css/styles.css">
    <link rel="stylesheet" href="../../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>

    <?php include('../../head.php'); ?>

    <div class="container mt-4 mb-4">

        <h3 class="text-center mb-4">CLANDOTER UN ETUDIANT</h3>

        <!-- ================= ALERT ================= -->
        <div class="row justify-content-center 4">
            <div class="col-md-6">

                <?php if (!empty($_SESSION['erreurValider'])): ?>
                <div class="alert alert-warning"><?= $_SESSION['erreurValider'] ?></div>

                <?php elseif (!empty($_SESSION['successValider'])): ?>
                <div class="alert alert-success"><?= $_SESSION['successValider'] ?></div>

                <?php elseif (!empty($_SESSION['erreurNonTrouver'])): ?>
                <div class="alert alert-danger"><?= $_SESSION['erreurNonTrouver'] ?></div>

                <?php elseif (!empty($_SESSION['erreurForclo'])): ?>
                <div class="alert alert-dark"><?= $_SESSION['erreurForclo'] ?></div>
                <?php endif; ?>

            </div>
        </div>

        <!-- ================= RECHERCHE ================= -->
        <form action="requestClando.php" method="POST" class="row justify-content-center mb-4">
            <div class="col-md-6">
                <input name="numEtudiant" id="numEtudiant" type="text" class="form-control"
                    placeholder="NUMERO CARTE ATTRIBUTAIRE" required>
            </div>

            <div class="col-md-2">
                <button class="btn btn-primary btn-lg w-100">Rechercher</button>
            </div>
        </form>

        <!-- ================= RESULT ================= -->
        <?php if ($data): ?>

        <form action="requestClando.php" method="POST">

            <?php
    //  récupérer le premier paiement
    $paiements = getAllSituation($data['num_etu']);
    $firstPaiement = mysqli_fetch_assoc($paiements);
    ?>

            <!--  HIDDEN IMPORTANT -->
            <input type="hidden" name="id_paie" value="<?= $firstPaiement['id_paie'] ?? '' ?>">
            <input type="hidden" name="num_etu_source" value="<?= $data['num_etu'] ?>">

            <!-- ================= INFOS ETUDIANT ================= -->
            <div class="row justify-content-center">
                <div class="col-md-4 mb-2">
                    <input class="form-control" value="<?= $data['prenoms'] ?>" disabled>
                </div>

                <div class="col-md-4 mb-2">
                    <input class="form-control" value="<?= $data['nom'] ?>" disabled>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-md-4 mb-2">
                    <input class="form-control" value="<?= $data['niveauFormation'] ?>" disabled>
                </div>

                <div class="col-md-4 mb-2">
                    <input class="form-control" value="Pavillon: <?= $data['pavillon'] ?>" disabled>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-md-4 mb-2">
                    <input class="form-control" value="Lit: <?= $data['lit'] ?>" disabled>
                </div>

                <div class="col-md-4 mb-2">
                    <input class="form-control" value="Validé le : <?= dateFromat($data['dateTime_val']) ?>" disabled>
                </div>
            </div>

            <!-- ================= PAIEMENT (1 SEUL) ================= -->
            <?php if ($firstPaiement): ?>
            <div class="row justify-content-center mt-3">
                <div class="col-md-8">
                    <table class="table table-bordered">
                        <tr class="table-primary">
                            <th>Quittance</th>
                            <th>Date</th>
                            <th>Libellé</th>
                        </tr>
                        <tr>
                            <td><?= $firstPaiement['id_paie'] ?></td>
                            <td><?= dateFromat($firstPaiement['dateTime_paie']) ?></td>
                            <td><?= $firstPaiement['libelle'] ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            <?php endif; ?>

            <!-- ================= CONDITION LOGER ================= -->
            <?php if ($data['etat_id_paie'] == 'Migré'): ?>

            <div class="row justify-content-center mt-3">
                <div class="col-md-4">
                    <input required type="text" name="num_etu_clando" class="form-control"
                        placeholder="NUMERO CARTE DU CLANDO">
                </div>
            </div>

            <div class="text-center mt-3">
                <button type="button" class="btn btn-lg btn-success" data-bs-toggle="modal" data-bs-target="#modal">
                    LOGER
                </button>
            </div>

            <?php else: ?>

            <div class="text-center mt-3">
                <a class="btn btn-secondary" href="clando.php">RETOUR</a>
            </div>

            <?php endif; ?>

            <!-- ================= MODAL ================= -->
            <div class="modal fade" id="modal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title">Confirmation</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            Confirmer le clando ?
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-primary">Confirmer</button>
                        </div>

                    </div>
                </div>
            </div>

        </form>

        <?php endif; ?>
        <br><br>

    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="../../assets/bootstrap/js/bootstrap.bundle.min.js"></script>

</body>

</html>