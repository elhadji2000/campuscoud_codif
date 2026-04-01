<?php 
session_start();
include('../../traitement/fonction.php');
if(! isset( $_SESSION['chef_securite'] ) ){
    header("location: ../..");
}	

verif_type_mdp_2($_SESSION['username']); 

$etudiants = [];

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = trim($_GET['search']);
    $etudiants = rechercherEtudiants($connexion, $search);
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COUD: CODIFICATION</title>

    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="../../assets/css/styles.css">
    <link rel="stylesheet" href="../../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/css/base.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
    select.fac {
        width: 250px;
        height: 50px;
        font-size: 16px;
        padding: 5px;
        border-radius: 5px;
    }

    table td {}

    #floatingBtn:hover {
        background-color: #0056b3;
        transform: scale(1.1);
    }

    #sendBtn {
        border-radius: 50%;
        width: 45px;
        height: 45px;
    }

    .table th {
        font-size: 15px;
        letter-spacing: 0.5px;
    }

    .table td {
        vertical-align: middle;
        text-align: center;
    }

    .badge {
        font-size: 14px;
        padding: 6px 10px;
    }
    </style>
</head>
<?php include('../../head.php'); ?>

<body>
    <div class="container-fluid" style="font-size:16px;">
        <center>
            <br>
            <div class="container" style="max-width:600px;">
                <form method="get" action="">
                    <div class="input-group mb-3">
                        <input type="text" name="search" class="form-control"
                            placeholder="Rechercher par nom, prénom, numéro carte ou téléphone"
                            value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" required>

                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i> Rechercher
                        </button>
                    </div>
                </form>
            </div>


            <br>
            <h2>Résultats de la recherche des étudiants</h2>
        </center>

        <br><br>

        <table class="table table-striped table-hover table-bordered text-center">
            <thead class="">
                <tr>
                    <th>#</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Numéro Carte</th>
                    <th>Téléphone</th>
                    <th>Sexe</th>
                    <th>Date Naissance</th>
                    <th>Pavillon</th>
                    <th>Lit</th>
                    <th>Statut</th>
                </tr>
            </thead>

            <tbody>
                <?php if (!empty($etudiants)): ?>

                <?php $i = 1; foreach ($etudiants as $etu): ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><?= htmlspecialchars($etu['nom']) ?></td>
                    <td><?= htmlspecialchars($etu['prenoms']) ?></td>
                    <td><?= htmlspecialchars($etu['num_etu']) ?></td>
                    <td><?= htmlspecialchars($etu['telephone']) ?></td>
                    <td><?= htmlspecialchars($etu['sexe']) ?></td>
                    <td><?= htmlspecialchars($etu['dateNaissance']) ?></td>
                    <td><?= htmlspecialchars($etu['pavillon'] ?? "NULL") ?></td>
                    <td><?= htmlspecialchars($etu['lit'] ?? "NULL") ?></td>
                    <td><?= htmlspecialchars($etu['statut'] ??"NULL") ?></td>
                </tr>
                <?php endforeach; ?>

                <?php else: ?>
                <tr>
                    <td colspan="10" class="text-danger text-center">
                        Aucun étudiant trouvé.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>


        <div class="text-center my-5">
            <button class="btn btn-success" onclick="window.history.back()">Retour</button>
        </div>
    </div>


    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


</body>

</html>