<?php 
session_start();
if (!isset($_SESSION['lit'])) {
    header('location: ../');
}

require_once ('../../traitement/fonction.php');
include ('head.html');
$link = connexionBD();
include ('../../activite.php');
verif_type_mdp_2($_SESSION['username']);
?>

<html lang="fr">

<section id="homedesigne" class="s-homedesigne">
    <p class="lead">Espace S.A.G : Bienvenue !</p>
</section>

<section id="styles" class="s-styles">

    <div class="row add-bottom" style="margin-top: 10px;">
        
        <!-- FORMULAIRE ALIGNÉ -->
        <div class="col-twelve" style="display:flex; justify-content:center;">
            <form method="GET" style="display:flex; gap:10px; align-items:center;">

                <select name="fac" class="form-control" style="width:260px;">
                    <option value="">-- Choisir une faculté --</option>

                    <?php foreach (getAllEtablissement() as $e): ?>
                        <option value="<?= $e['etablissement']; ?>"
                            <?= (isset($_GET['fac']) && $_GET['fac'] == $e['etablissement']) ? 'selected' : '' ?>>
                            <?= $e['etablissement']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <button type="submit" style="padding:7px 12px;">Rechercher</button>
            </form>
        </div>

        <div class="col-twelve" style="margin:0px;">
            
            <!-- MESSAGE D'ACCUEIL SI AUCUNE FACULTÉ CHOISIE -->
            <?php if (!isset($_GET['fac']) || empty($_GET['fac'])): ?>
                <div style="text-align:center; padding:10px; font-size:18px; color:#444;">
                    Veuillez sélectionner une faculté pour afficher la liste des lits, titulaires et suppléants.
                </div>
            <?php endif; ?>


            <!-- INDICATEUR DE CHARGEMENT -->
            <div id="loading" 
                 style="display:none; text-align:center; padding:20px; font-size:18px; color:blue;">
                <img src="https://i.gifer.com/ZZ5H.gif" width="40"><br>
                Chargement des données...
            </div>

            <div class="table-responsive" id="resultArea">

                <?php
                if (isset($_GET['fac']) && !empty($_GET['fac'])) {

                    $fac = $_GET['fac'];
                    $lits = getLitNonVal($fac);
                ?>

                    <h4 style="margin-bottom:10px;">Résultats pour la faculté : <strong><?= $fac ?></strong></h4>

                    <?php if (count($lits) === 0): ?>
                        <p>Aucun lit trouvé.</p>

                    <?php else: ?>

                    <table border="1" id="resultTable">
                        <thead>
                            <tr>
                                <th>Lit</th>
                                <th>Etudiant</th>
                                <th>N° Étudiant</th>
                            </tr>
                        </thead>

                        <tbody>
                        <?php foreach ($lits as $row): 
                        ?>
                            <tr>
                                <td><?= $row['lit'] ?></td>

                                <td><?= $row['prenoms'] . " " . $row['nom'] ?></td>
                                <td><?= $row['num_etu'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>

                    <?php endif; ?>

                <?php } ?>

            </div>
        </div>

    </div>
</section>

<?php include ('../../foot.html'); ?>

<script>
// Affichage du loader dès qu'on lance la recherche
document.querySelector("form").addEventListener("submit", () => {
    document.getElementById("loading").style.display = "block";
    document.getElementById("resultArea").style.opacity = "0.3";
});

// Recherche dynamique dans tableau
document.addEventListener("DOMContentLoaded", function() {
    const searchInput = document.getElementById("search");
    const rows = document.querySelectorAll("#resultTable tbody tr");

    if (searchInput) {
        searchInput.addEventListener("keyup", function() {
            const value = this.value.toLowerCase().trim();

            rows.forEach(row => {
                const num = row.cells[2]?.textContent.toLowerCase() ?? "";
                row.style.display = num.includes(value) ? "" : "none";
            });
        });
    }
});
</script>

</html>
