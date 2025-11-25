<?php
session_start();
if (empty($_SESSION['username']) && empty($_SESSION['mdp'])) {
    header('Location: https://campuscoud.com/');
    exit();
}

$fac = $_SESSION['fac']; 
$lit = $_GET['lit']??"null";

include('../../traitement/fonction.php');
verif_type_mdp_2($_SESSION['username']);

$faculter = $fac . "/SOCIALE";
$lits = getLitByPcs($faculter); 

// Vérifier que le lit appartient bien au quota
if (!in_array($lit, $lits)) {

    echo "
    <script>
        alert('Erreur : Ce lit n\\'appartient pas à votre quota sociale.');
        window.location.href = 'index';
    </script>
    ";
    exit();
}


// Vérifier lit individuel ou non
$individuel = isLitIndividuel($lit);
$sexe = getSexeLit($lit) ;
$occupants = getOccupantsByLit($lit);
$nbOccupants = count($occupants);

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COUD: CODIFICATION</title>

    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="../../assets/css/styles.css">
    <link rel="stylesheet" href="../../assets/bootstrap/css/bootstrap.css">
</head>

<body>
    <?php include('../../head.php'); ?>
    <br>

    <div class="container">
        <h3 class="text-center mb-4">
            Attribution du lit <strong class="text-danger"><?= htmlspecialchars($lit) ?></strong>
        </h3>

        <?php

            if (isset($_SESSION['message'])) {
                $type = $_SESSION['message_type']; 
                echo "<div class='alert alert-$type text-center'>"
                    . $_SESSION['message'] . 
                    "</div>";

                unset($_SESSION['message']);
                unset($_SESSION['message_type']);
            }
        ?>

        <form action="traitement_aff" method="POST">
            <input type="hidden" name="lit" value="<?= htmlspecialchars($lit) ?>">

            <div class="row">

                <!-- TITULAIRE -->
                <div class="<?= $individuel ? 'col-md-12' : 'col-md-6' ?>">
                    <div class="p-3 border rounded bg-light">
                        <h5 class="text-primary mb-3">Étudiant Titulaire</h5>

                        <?php if ($nbOccupants >= 1): ?>
                        <!-- OCCUPANT 1 -->
                        <label>Numéro carte étudiant</label>
                        <div class="input-group mb-3">
                            <input type="text" name="num_titulaire" id="num_titulaire" class="form-control"
                                value="<?= htmlspecialchars($occupants[0]['num_etu']) ?>" readonly>
                            <?php if (!isAffectationValidee($occupants[0]['id_aff'])): ?>
                            <button type="button" class="btn btn-danger"
                                onclick="supprimer(<?= $occupants[0]['id_aff'] ?>, 'titulaire')">
                                Supprimer
                            </button>
                            <?php else: ?>
                            <button type="button" class="btn btn-secondary" disabled>
                                ✔ Validée
                            </button>
                            <?php endif; ?>
                        </div>
                        <div class="p-3 bg-white border rounded">
                            <h3><?= htmlspecialchars($occupants[0]['prenoms'] . " " . $occupants[0]['nom']) ?></h3></br>
                            <h3>Numéro : <?= htmlspecialchars($occupants[0]['num_etu']) ?></h3></br>
                            <h3>Sexe : <?= htmlspecialchars($occupants[0]['sexe']) ?></h3></br>
                            <h3>Faculté : <?= htmlspecialchars($occupants[0]['faculte']) ?></h3></br>
                            <h3>Département : <?= htmlspecialchars($occupants[0]['departement']) ?></h3></br>
                            <h3>Téléphone : <?= htmlspecialchars($occupants[0]['telephone']) ?></h3></br>
                        </div>

                        <?php else: ?>
                        <!-- Recherche titulaire -->
                        <label>Numéro carte étudiant</label>
                        <div class="input-group mb-3">
                            <input type="text" name="num_titulaire" id="num_titulaire" class="form-control"
                                placeholder="Saisir numéro étudiant..." required>
                            <button type="button" class="btn btn-secondary"
                                onclick="rechercher('titulaire')">Rechercher</button>
                        </div>

                        <div id="info_titulaire" class="p-3 bg-white border rounded" style="min-height:120px;">
                            <em class="text-muted">Aucune information pour le moment…</em>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>



                <!-- SUPPLÉANT -->
                <?php if (!$individuel): ?>
                <div class="col-md-6">
                    <div class="p-3 border rounded bg-light">
                        <h5 class="text-success mb-3">Étudiant Suppléant</h5>

                        <?php if ($nbOccupants == 2): ?>
                        <!-- Occupant 2 -->
                        <label>Numéro carte étudiant</label>
                        <div class="input-group mb-3">
                            <input type="text" name="num_suppleant" id="num_suppleant" class="form-control"
                                value="<?= htmlspecialchars($occupants[1]['num_etu']) ?>" readonly>
                            <?php if (!isAffectationValidee($occupants[1]['id_aff'])): ?>
                            <button type="button" class="btn btn-danger"
                                onclick="supprimer(<?= $occupants[1]['id_aff'] ?>, 'suppleant')">
                                Supprimer
                            </button>
                            <?php else: ?>
                            <button type="button" class="btn btn-secondary" disabled>
                                ✔ Validée
                            </button>
                            <?php endif; ?>
                        </div>
                        <div class="p-3 bg-white border rounded">
                            <h3><?= htmlspecialchars($occupants[1]['prenoms'] . " " . $occupants[1]['nom']) ?></h3></br>
                            <h3>Numéro : <?= htmlspecialchars($occupants[1]['num_etu']) ?></h3></br>
                            <h3>Sexe : <?= htmlspecialchars($occupants[1]['sexe']) ?></h3></br>
                            <h3>Faculté : <?= htmlspecialchars($occupants[1]['faculte']) ?></h3></br>
                            <h3>Département : <?= htmlspecialchars($occupants[1]['departement']) ?></h3></br>
                            <h3>Téléphone : <?= htmlspecialchars($occupants[1]['telephone']) ?></h3></br>
                        </div>

                        <?php elseif ($nbOccupants == 1): ?>
                        <!-- Recherche suppléant -->
                        <label>Numéro carte étudiant</label>
                        <div class="input-group mb-3">
                            <input type="text" name="num_suppleant" id="num_suppleant" class="form-control"
                                placeholder="Saisir numéro étudiant...">
                            <button type="button" class="btn btn-secondary"
                                onclick="rechercher('suppleant')">Rechercher</button>
                        </div>

                        <div id="info_suppleant" class="p-3 bg-white border rounded" style="min-height:120px;">
                            <em class="text-muted">Aucune information pour le moment…</em>
                        </div>

                        <?php else: ?>
                        <!-- Lit vide -->
                        <label>Numéro carte étudiant</label>
                        <div class="input-group mb-3">
                            <input type="text" name="num_suppleant" id="num_suppleant" class="form-control"
                                placeholder="Saisir numéro étudiant...">
                            <button type="button" class="btn btn-secondary"
                                onclick="rechercher('suppleant')">Rechercher</button>
                        </div>

                        <div id="info_suppleant" class="p-3 bg-white border rounded" style="min-height:120px;">
                            <em class="text-muted">Aucune information pour le moment…</em>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>


            </div>

            <?php if (($individuel && $nbOccupants == 0) || (!$individuel && $nbOccupants < 2)): ?>
            <div class="text-center mt-4">
                <button class="btn btn-success btn-lg">Enregistrer l’attribution</button>
            </div>
            <?php endif; ?>


        </form>
    </div>
    <script>
    const sexeLit = "<?= addslashes(getSexeLit($lit)) ?>";
    </script>
    <script>
    // SCRIPT AJAX – recherche étudiant via getDonneesEtudiant()

    function rechercher(type) {
        let numero = document.getElementById("num_" + type).value;

        if (numero.trim() === "") {
            alert("Veuillez saisir un numéro étudiant !");
            return;
        }
        // zone d'affichage (titulaire ou suppléant)
        const zoneInfo = document.getElementById("info_" + type);
        zoneInfo.innerHTML = "<em>Recherche en cours...</em>";

        fetch("rechercher_etudiant.php?num=" + numero)
            .then(response => response.json())
            .then(data => {
                let zone = document.getElementById("info_" + type);

                if (!data.success) {
                    zone.innerHTML =
                        "<span class='text-danger'>Étudiant introuvable dans la base UCAD.</span>";
                    return;
                }

                // Vérifier si déjà affecté
                if (data.estAffecte === true) {
                    zone.innerHTML = `
                        <div class='alert alert-danger'>
                            Cet étudiant est déjà affecté à un lit !<br>
                            Impossible de l'attribuer à nouveau.
                        </div>
                    `;
                    return;
                }


                //  Vérifier si sexe lit == sexe étudiant
                /* if (data.sexe && data.sexe !== sexeLit) {
                    zoneInfo.innerHTML =
                        "<div class='alert alert-danger'>" +
                        "Erreur : Ce lit est réservé au sexe <b>" + sexeLit +
                        "</b>, mais l’étudiant recherché est <b>" + data.sexe + "</b>." +
                        "</div>";
                    return;
                } */

                // Affichage automatique
                zone.innerHTML = `
                <h3>Nom : ${data.nom} </h3><br>
                <h3> Prénom : ${data.prenom} </h3><br>
                <h3> Faculté : ${data.faculte} </h3><br>
                <h3> Département : ${data.departement} </h3><br>
                <h3> Sexe : ${data.sexe} </h3><br>
                <h3> Téléphone : ${data.telephone} </h3><br>
            `;
            })
            .catch(() => {
                alert("Erreur lors de la communication avec le serveur.");
            });
    }
    </script>
    <script>
    function supprimer(id_aff, type) {
        if (confirm("Voulez-vous vraiment supprimer cette affectation ?")) {
            window.location.href = "supprimer_affectation.php?id_aff=" + id_aff + "&type=" + type + "&lit=<?= $lit ?>";
        }
    }
    </script>


    <script src="../../assets/js/jquery-3.2.1.min.js"></script>
    <script src="../../assets/js/plugins.js"></script>
    <script src="../../assets/js/main.js"></script>

</body>

</html>