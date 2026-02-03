<?php
session_start();
if (empty($_SESSION['username']) && empty($_SESSION['mdp'])) {
    header('Location: https://campuscoud.com/');
    exit();
}
$annee = $_SESSION["annee"];
include('../../traitement/fonction.php');
verif_type_mdp_2($_SESSION['username']);

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
            <strong class="text-primary">ACTION DE REMPLACEMENT </strong>
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
            <div class="row">

                <!-- TITULAIRE -->
                <div class="col-md-6">
                    <div class="p-3 border rounded bg-light">
                        <h5 class="text-primary mb-3">ÉTUDIANT TITULAIRE</h5>
                        <!-- OCCUPANT 1 -->
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
                    </div>
                </div>

                <!-- SUPPLÉANT -->
                <div class="col-md-6">
                    <div class="p-3 border rounded bg-light">
                        <h5 class="text-success mb-3">ÉTUDIANT A PERMUTER</h5>
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
                    </div>
                </div>

            </div>
            <div class="text-center mt-4">
                <button id="btnAttribuer" class="btn btn-success btn-lg" disabled>
                    PERMUTER
                </button>

            </div>
        </form>
    </div>

    <script>
    let titulaireValide = false;
    let suppleantValide = false;
    let sexeTitulaire = null;

    function rechercher(type) {
        const numero = document.getElementById("num_" + type).value.trim();
        const zone = document.getElementById("info_" + type);

        if (!numero) {
            alert("Veuillez saisir un numéro étudiant");
            return;
        }

        zone.innerHTML = "<em class='text-muted'>Recherche en cours...</em>";
        document.getElementById("btnAttribuer").disabled = true;

        fetch("rechercher_etudiant.php?num=" + encodeURIComponent(numero) + "&type=" + type)

            .then(res => res.json())
            .then(data => {
                if (!data.success) {
                    erreur(zone, data.message ?? "Étudiant introuvable");
                    setValide(type, false);
                    return;
                }


                /* ========= RÈGLES TITULAIRE ========= */
                if (type === "titulaire") {

                   /*  if (!data.estAffecte) {
                        erreur(zone, "Le titulaire doit obligatoirement être logé");
                        setValide("titulaire", false);
                        return;
                    } */

                    sexeTitulaire = data.sexe;
                }

                /* ========= RÈGLES SUPPLÉANT ========= */
                if (type === "suppleant") {

                    if (data.estAffecte) {
                        erreur(zone, "Le suppléant ne doit pas être logé");
                        setValide("suppleant", false);
                        return;
                    }

                    if (sexeTitulaire && data.sexe !== sexeTitulaire) {
                        erreur(zone, "Le suppléant doit être du même sexe que le titulaire");
                        setValide("suppleant", false);
                        return;
                    }
                }

                /* ========= RÈGLES COMMUNES ========= */
                 if (data.etat_inscription !== "Inscrit(e)") {
                    erreur(zone, "Étudiant non inscrit");
                    setValide(type, false);
                    return;
                } 

                if (data.payant !== "Régime Non Payant") {
                    erreur(zone, "Étudiant en régime payant");
                    setValide(type, false);
                    return;
                }

                /*  const anneeSession = "<?= $annee ?>";
                 if (data.annee !== anneeSession) {
                     erreur(zone, "Année académique incorrecte");
                     setValide(type, false);
                     return;
                 } */

                /* ========= AFFICHAGE ========= */
                zone.innerHTML = `
                <strong>${data.prenom} ${data.nom}</strong><br>
                Faculté : ${data.faculte}<br>
                Département : ${data.departement}<br>
                Sexe : ${data.sexe}<br>
                Téléphone : ${data.telephone}
            `;

                setValide(type, true);
            })
            .catch(() => {
                erreur(zone, "Erreur serveur");
                setValide(type, false);
            });
    }

    /* ========= HELPERS ========= */

    function setValide(type, etat) {
        if (type === "titulaire") titulaireValide = etat;
        if (type === "suppleant") suppleantValide = etat;
        updateBouton();
    }

    function updateBouton() {
        document.getElementById("btnAttribuer").disabled = !(titulaireValide && suppleantValide);
    }

    function erreur(zone, message) {
        zone.innerHTML = `<div class="alert alert-danger">${message}</div>`;
    }
    </script>




    <script src="../../assets/js/jquery-3.2.1.min.js"></script>
    <script src="../../assets/js/plugins.js"></script>
    <script src="../../assets/js/main.js"></script>

</body>

</html>