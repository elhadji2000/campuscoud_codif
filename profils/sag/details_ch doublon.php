<?php session_start(); ?>
<html lang="fr">
<?php 
include('head.html');	 
include ('../../traitement/fonction.php');

$link = connexionBD();

if (!isset($_SESSION['sag'])) {
    header("location: ../../");
    exit();
}

include('../../activite.php'); 

// Récupération du numéro de chambre
if (isset($_GET['ch'])) {
    $numch = trim($_GET['ch']);
} elseif (isset($_POST['ch'])) {
    $numch = trim($_POST['ch']);
} else {
    $numch = null;
}

$lits = getLitParChambre($link, $numch);
?>

<body>
    <section id="homedesigne" class="s-homedesigne">
        <p class="lead">Espace S.A.G: Bienvenue!</p>
    </section>

    <section id="styles" class="s-styles">
        <center>
            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center;">
                <label for="search" style="font-weight: bold; font-size: 16px; color: #333;">
                    🔍 Rechercher une chambre :
                </label>
            </div>

            <form method="GET" action="details_ch" class="row g-3"
                style="display: flex; justify-content: center; margin-top: 20px;">
                <div class="col-auto">
                    <input type="text" name="ch" class="form-control" placeholder="EX: 35A0" style="padding:3px;">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-success mb-3"
                        style="background-color: #676eebff;">Rechercher</button>
                </div>
            </form>

            <div class="table-responsive container" style="margin-top:20px; width:70%;text-align:center;">
                <table border="1" class="table table-bordered">
                    <thead>
                        <tr style="background-color:#f2f2f2;">
                            <th>#</th>
                            <th>ID</th>
                            <th>Lit</th>
                            <th>NiveauFormation</th>
                            <th>Choix</th>
                            <th>Valider</th>
                            <th>Payer</th>
                            <th>Loger</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($numch)) : ?>
                        <tr>
                            <td colspan="4" style="text-align:center;">
                                🔸 Veuillez entrer un numéro de chambre pour lancer la recherche.
                            </td>
                        </tr>
                        <?php elseif (!empty($lits)) : ?>
                        <?php $i = 1; ?>
                        <?php foreach ($lits as $lit) : ?>
                        <tr>
                            <td style="text-align:center;"><?= $i++; ?></td>
                            <td><?= htmlspecialchars($lit['id_lit']); ?></td>
                            <td><?= htmlspecialchars($lit['lit']); ?></td>
                            <td style="color:<?= !empty($lit['niveauFormation']) ? 'black' : 'gray'; ?>">
                                <?= !empty($lit['niveauFormation']) ? $lit['niveauFormation'] : 'vide'; ?>
                            </td>
                            <td style="color:<?= !empty($lit['prenom']) ? 'black' : 'gray'; ?>">
                                <?= !empty($lit['prenoms']) ? $lit['prenoms'].' '.$lit['nom'].' ( '.$lit['num_etu'].' )' : 'vide'; ?>
                            </td>
                            <td>
                                <?= htmlspecialchars(!empty($lit['dateTime_val']) 
                                    ? date('d/m/Y', strtotime($lit['dateTime_val'])) 
                                    : 'NoN'); ?>
                            </td>
                            <td>
                                <?= htmlspecialchars(!empty($lit['dateTime_paie']) 
                                    ? date('d/m/Y', strtotime($lit['dateTime_paie'])) 
                                    : 'NoN'); ?>
                            </td>
                            <td>
                                <?= htmlspecialchars(!empty($lit['dateTime_loger']) 
                                    ? date('d/m/Y', strtotime($lit['dateTime_loger'])) 
                                    : 'NoN'); ?>
                            </td>

                        </tr>
                        <?php endforeach; ?>
                        <?php else : ?>
                        <tr>
                            <td colspan="4" style="text-align:center;">
                                ❌ Aucun lit trouvé pour la chambre <?= htmlspecialchars($numch); ?>.
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </center>
    </section>

    <center>
        <a href="javascript:history.back()" id="retour">Retour</a><br><br>
    </center>
</body>

</html>