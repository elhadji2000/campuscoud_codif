<?php session_start(); ?>
<html lang="fr">
<?php
include ('head.html');
include ('../../traitement/fonction.php');

$link = connexionBD();

if (!isset($_SESSION['sag'])) {
    header('location: ../../');
    exit();
}

include ('../../activite.php');

// ===============================
// 1. Récupération numéro étudiant
// ===============================
$numero = null;
if (isset($_GET['etu'])) {
    $numero = trim($_GET['etu']);
} elseif (isset($_POST['etu'])) {
    $numero = trim($_POST['etu']);
}

// ===============================
// Variables
// ===============================
$titulaire = null;
$suppleant = null;
$messageErreur = null;
$rangTitulaire = null;

if (!empty($numero)) {
    // ===============================
    // 2. Récupérer étudiant
    // ===============================
    $etudiant = studentConnect($numero);

    if (empty($etudiant) || !is_array($etudiant)) {
        $messageErreur = 'Étudiant introuvable';
    } else {
        // ===============================
        // 3. Récupérer quota
        // ===============================
        $quotaRow = getQuotaClasse($etudiant['niveauFormation'], $etudiant['sexe']);
        $quota = intval($quotaRow['COUNT(*)']);

        // ===============================
        // 4. Statut étudiant
        // ===============================
        $dataStatutStudent = getOnestudentStatus(
            $quota,
            $etudiant['niveauFormation'],
            $etudiant['sexe'],
            $numero
        );

        // ===============================
        // 5. Vérification ATTRIBUTAIRE
        // ===============================
        if (
            empty($dataStatutStudent) ||
            !isset($dataStatutStudent['statut']) ||
            strtoupper($dataStatutStudent['statut']) !== 'ATTRIBUTAIRE'
        ) {
            $messageErreur = "Cet étudiant n'est pas un titulaire (non attributaire)";
        } else {
            // ===============================
            // 6. Titulaire + rang
            // ===============================
            $rangTitulaire = intval($dataStatutStudent['rang']);
            $titulaire = $etudiant;

            // ===============================
            // 7. Suppléant
            // ===============================
            $suppleant = getOneSuppleantByTitulaire(
                $quota,
                $etudiant['niveauFormation'],
                $etudiant['sexe'],
                $rangTitulaire
            );
        }
    }

    $statutSuppleant = null;

    if (!empty($suppleant)) {
        $dataStatutSupp = getOnestudentStatus(
            $quota,
            $suppleant['niveauFormation'],
            $suppleant['sexe'],
            $suppleant['num_etu']
        );

        $statutSuppleant = $dataStatutSupp['statut'] ?? 'N/A';
    }
}
?>

<style>
table tr td,
tr {
    font-size: 11px;
}
</style>

<body>

    <section id="homedesigne" class="s-homedesigne">
        <p class="lead">Recherche Titulaire & Suppléant</p>
    </section>

    <section>
        <center>

            <!-- ===============================
     FORMULAIRE
================================ -->
            <form method="GET" class="row g-3" style="display:flex; justify-content:center; margin-top:20px;">
                <div class="col-auto">
                    <input type="text" name="etu" class="form-control" placeholder="Numéro étudiant"
                        value="<?= htmlspecialchars($numero ?? '') ?>">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-success">Rechercher</button>
                </div>
            </form>

            <!-- ===============================
     MESSAGE ERREUR
================================ -->
            <?php if (!empty($messageErreur)): ?>
            <div class="alert alert-danger" style="margin-top:20px; width:50%;">
                <?= htmlspecialchars($messageErreur); ?>
            </div>
            <?php endif; ?>

            <!-- ===============================
     TABLEAU RESULTAT
================================ -->
            <div class="table-responsive container" style="margin-top:20px;">
                <table border="1" class="table table-bordered">

                    <thead>
                        <tr style="background-color:#f2f2f2;">
                            <th>Type</th>
                            <th>Num étudiant</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Niveau</th>
                            <th>statut</th>
                            <th>Sexe</th>
                            <th>Rang</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php if (empty($numero)): ?>
                        <tr>
                            <td colspan="8">Veuillez entrer un numéro étudiant</td>
                        </tr>

                        <?php elseif (!empty($titulaire)): ?>

                        <tr style="background-color:#d1e7dd;">
                            <td><b>Titulaire</b></td>
                            <td><?= htmlspecialchars($titulaire['num_etu']); ?></td>
                            <td><?= htmlspecialchars($titulaire['nom']); ?></td>
                            <td><?= htmlspecialchars($titulaire['prenoms']); ?></td>
                            <td><?= htmlspecialchars($titulaire['niveauFormation']); ?></td>
                            <td><?= htmlspecialchars($titulaire['sexe']); ?></td>
                            <td><?= htmlspecialchars($dataStatutStudent['statut']); ?></td> <!-- ✅ -->
                            <td><?= htmlspecialchars($rangTitulaire); ?></td>
                        </tr>

                        <!-- SUPPLEANT -->
                        <?php if (!empty($suppleant)): ?>
                        <tr style="background-color:#fff3cd;">
                            <td><b>Suppléant</b></td>
                            <td><?= htmlspecialchars($suppleant['num_etu']); ?></td>
                            <td><?= htmlspecialchars($suppleant['nom']); ?></td>
                            <td><?= htmlspecialchars($suppleant['prenoms']); ?></td>
                            <td><?= htmlspecialchars($suppleant['niveauFormation']); ?></td>
                            <td><?= htmlspecialchars($suppleant['sexe']); ?></td>
                            <td><?= htmlspecialchars($statutSuppleant); ?></td> <!-- ✅ -->
                            <td><?= htmlspecialchars($suppleant['rang']); ?></td>
                        </tr>
                        <?php else: ?>
                        <tr>
                            <td colspan="8">Aucun suppléant trouvé</td>
                        </tr>
                        <?php endif; ?>

                        <?php endif; ?>

                    </tbody>

                </table>
            </div>

        </center>
    </section>

    <center>
        <a href="javascript:history.back()">Retour</a>
    </center>

</body>

</html>