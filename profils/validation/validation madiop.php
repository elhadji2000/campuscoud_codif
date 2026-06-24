<?php session_start();
if (empty($_SESSION['username']) && empty($_SESSION['mdp'])) {
    header('Location: /campuscoud.com/');
    exit();
}

include('../../traitement/fonction.php');
connexionBD();
include('../../traitement/requete.php');

verif_type_mdp_2($_SESSION['username']);

$countIn = 0;
$messages = [
    'erreurValider' => $_GET['erreurValider'] ?? null,
    'successValider' => $_GET['successValider'] ?? null,
    'erreurNonTrouver' => $_GET['erreurNonTrouver'] ?? null,
    'erreurForclo' => $_GET['erreurForclo'] ?? null,
];
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COUD: CODIFICATION</title>
    <!-- CSS================================================== -->
    <link rel="stylesheet" href="../../assets/css/main.css">
    <!-- script================================================== -->
    <script src="../../assets/js/modernizr.js"></script>
    <script src="../../assets/js/pace.min.js"></script>
    <link rel="stylesheet" href="../../assets/css/styles.css">
    <link rel="stylesheet" href="../../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/bootstrap/js/bootstrap.min.js">
    <link rel="stylesheet" href="../../assets/bootstrap/js/bootstrap.bundle.min.js">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <?php include('../../head.php'); ?>
    <div class="container">
        <div class="row">
            <div class="text-center">
                <h1>VALIDATION PAR PRESENCE PHYSIQUE</h1><br>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-6">

                <?php foreach ($messages as $key => $msg): ?>
                <?php if ($msg): ?>
                <div class="alert 
                    <?= str_contains($key, 'success') ? 'alert-success' : 'alert-danger' ?>">
                    <?= htmlspecialchars($msg) ?>
                </div>
                <?php endif; ?>
                <?php endforeach; ?>

            </div>
        </div>
        <div class="container" style="width:50%;">
            <form action="requestValidation" method="POST">
                <div class="row align-items-center justify-content-center">
                    <div class="col-md-6">

                        <input id="numEtudiant" name="numEtudiant" type="text" class="form-control text-uppercase"
                            placeholder="NUMERO CARTE ETUDIANT" required>
                    </div>
                    <div class="col-md-6">

                        <button type="submit" width="100px" class="btn btn-lg btn-primary">
                            <i class="fa fa-search"></i> Rechercher
                        </button>

                    </div>
                </div>
            </form>
        </div>
    </div><br>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">

                <?php
                if (isset($_GET['data'])) {
                    $data = $_GET['data'];

                    $tableau_data_etudiant = getAllSituation($data['num_etu']);

                    if ($data['departement'] == 'F.L.S.H/M1ASS') {
                        echo "<div class='alert alert-warning text-center'>
                                Codification momentanément suspendue pour votre formation !
                            </div>";
                        exit();
                    }

                    $rowClass = "row justify-content-center text-dark";
                ?>

                <?php if (isset($_GET['statut'])): ?>
                <form action="requestValidation" method="POST">

                    <!-- INFOS -->
                    <div class="<?= $rowClass ?>">
                        <div class="col-md-4 mb-2">
                            <input type="text" class="form-control" value="Prénom : <?= $data['prenoms'] ?>" disabled>
                            <input type="hidden" name="id_etu" value="<?= $data['id_etu'] ?>">
                        </div>

                        <div class="col-md-4 mb-2">
                            <input class="form-control" value="Nom : <?= $data['nom'] ?>" disabled>

                            <?php if (isset($_GET['idLit'])): ?>
                            <input type="hidden" name="idLit" value="<?= $_GET['idLit'] ?>">
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="<?= $rowClass ?>">
                        <div class="col-md-4 mb-2">
                            <input class="form-control" value="Faculté : <?= $data['etablissement'] ?>" disabled>
                        </div>
                        <div class="col-md-4 mb-2">
                            <input class="form-control" value="Niveau : <?= $data['niveauFormation'] ?>" disabled>
                        </div>
                    </div>

                    <div class="<?= $rowClass ?>">
                        <div class="col-md-4 mb-2">
                            <input class="form-control" value="Moyenne : <?= $data['moyenne'] ?>" disabled>
                        </div>
                        <div class="col-md-4 mb-2">
                            <input class="form-control" value="Statut : <?= $_GET['statut'] ?>" disabled>
                        </div>
                    </div>

                    <!-- SUPPLEANT -->
                    <?php if ($_GET['statut'] == 'Suppleant(e)'):

                        $sexe = studentConnect($data['num_etu'])['sexe'];
                        $quota = getQuotaClasse($data['niveauFormation'], $sexe)['COUNT(*)'];
                        $rang = getOnestudentStatus($quota, $data['niveauFormation'], $sexe, $data['num_etu'])['rang'];

                        $titulaire = getOneTitulaireBySuppleant($quota, $data['niveauFormation'], $sexe, $rang);
                        $litData = getOneLitByStudent($titulaire['num_etu'])->fetch_assoc();
                    ?>

                    <div class="<?= $rowClass ?>">
                        <div class="col-md-4 mb-3">
                            <input class="form-control" value="Pavillon : <?= $litData['pavillon'] ?>" disabled>
                        </div>
                        <div class="col-md-4 mb-3">
                            <input class="form-control" value="Lit : <?= $litData['lit'] ?>" disabled>
                        </div>
                    </div>

                    <?php endif; ?>

                    <!-- VALIDATION -->
                    <?php if ($_GET['statut'] != 'Forclos(e)'): ?>

                    <div class="text-center mt-3">
                        <button class="btn btn-success px-4" type="button" data-toggle="modal"
                            data-target="#confirmationModal">
                            <i class="fa fa-check"></i> VALIDER
                        </button>
                    </div>

                    <?php else: ?>

                    <?php
                        $type = ($data['type'] == 'auto') ? 'Automatique' : $data['type'];
                        $motif = ($data['type'] == 'auto') ? 'Retard' : $data['motif_manuel'];
                    ?>

                    <div class="<?= $rowClass ?>">
                        <div class="col-md-4 mb-3">
                            <input class="form-control" value="Type : <?= $type ?>" disabled>
                        </div>
                        <div class="col-md-4 mb-3">
                            <input class="form-control" value="Motif : <?= $motif ?>" disabled>
                        </div>
                    </div>

                    <div class="text-center">
                        <a class="btn btn-secondary" href="/campuscoud.com/profils/validation/validation">RETOUR</a>
                    </div>

                    <?php endif; ?>

                </form>

                <?php else: ?>
                <!-- ================= AUTRE CAS ================= -->

                <form action="requestValidation" method="POST">

                    <div class="<?= $rowClass ?>">
                        <div class="col-md-4 mb-2">
                            <input class="form-control" value="Prénom : <?= $data['prenoms'] ?>" disabled>
                            <input type="hidden" name="valide" value="<?= $data[0] ?>">
                            
                        </div>

                        <div class="col-md-4 mb-2">
                            <input class="form-control" value="Nom : <?= $data['nom'] ?>" disabled>
                        </div>
                    </div>

                    <div class="<?= $rowClass ?>">
                        <div class="col-md-4 mb-2">
                            <input class="form-control" value="Faculté : <?= $data['etablissement'] ?>" disabled>
                        </div>
                        <div class="col-md-4 mb-2">
                            <input class="form-control" value="Niveau : <?= $data['niveauFormation'] ?>" disabled>
                        </div>
                    </div>

                    <div class="<?= $rowClass ?>">
                        <div class="col-md-4 mb-2">
                            <input class="form-control" value="Numéro : <?= $data['num_etu'] ?>" disabled>
                        </div>
                        <div class="col-md-4 mb-2">
                            <input class="form-control" value="Campus : <?= $data['campus'] ?>" disabled>
                        </div>
                    </div>

                    <div class="<?= $rowClass ?>">
                        <div class="col-md-4 mb-2">
                            <input class="form-control" value="Pavillon : <?= $data['pavillon'] ?>" disabled>
                        </div>
                        <div class="col-md-4 mb-2">
                            <input class="form-control" value="Lit : <?= $data['lit'] ?>" disabled>
                        </div>
                    </div>

                    <?php if ($data['migration_status'] == 'Migré'): ?>

                    <div class="text-center mt-3">
                        <input class="form-control text-center"
                            value="Validé le <?= dateFromat($data['dateTime_val']) ?>" disabled>
                        <br>
                        <a class="btn btn-secondary" href="/campuscoud.com/profils/validation/validation">RETOUR</a>
                    </div>

                    <?php else:

                        $date_debut = getAllDelai("depart", info($data['num_etu'])[5]);
                        $date_debut = dateFromat($date_debut['data_limite']);

                        $mois = calcul_nbreMois($date_debut);
                        $prix = getPrixMensuelLit($data['num_etu']);
                        $montant = $mois * $prix;
                    ?>

                    <div class="text-center mt-3">
                        <input class="form-control mb-3 text-center"
                            value="Facture: Caution=5.000F | <?= $mois ?> mois = <?= $montant ?> F" disabled>

                        <input class="form-control mb-3" name="tel_tuteur" placeholder="Renseignez le Nom du Tuteur et son Telephone ..." required>
                        <input type="hidden" name="num_etu" value="<?= $data['num_etu'] ?>">

                        <button class="btn btn-lg btn-success px-4 py-4" data-toggle="modal" data-target="#confirmationModal">
                            <strong class="text-white" ><i class="fa fa-check"></i>  VALIDER </strong>
                        </button>
                    </div>

                    <?php endif; ?>

                </form>

                <?php endif; } ?>

            </div>
        </div>
    </div>
    <script>
    document.getElementById('numEtudiant').addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });
    </script>
    <script src="../../assets/js/jquery-3.2.1.min.js"></script>
    <script src="../../assets/js/plugins.js"></script>
    <script src="../../assets/js/main.js"></script>

    <!-- JavaScript de Bootstrap (assurez-vous d'ajuster le chemin si nécessaire) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
<script src="../../assets/js/script.js"></script>
</body>

</html>