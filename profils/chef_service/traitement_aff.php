<?php
session_start();
include('../../traitement/fonction.php');
global $connexion;
$_SESSION["annee"] = str_replace('-', '_', $_SESSION["annee"]);
/* -------------------------------------------------------
   MESSAGE + REDIRECTION
   $dataStatutStudentSearch = getOnestudentStatus($quotaClasseStudentConnecte, $classeStudentSearch, $sexeStudentSearch, $num_etu);
------------------------------------------------------- */
function redirectWithMessage($msg, $type = "danger", $lit = null) {
    $_SESSION['message'] = $msg;
    $_SESSION['message_type'] = $type;

    if ($lit !== null) {
        header("Location: pageLit?lit=" . urlencode($lit));
    } else {
        header("Location: pageLit");
    }

    exit();
}


function error($msg) {
    global $lit2;
    redirectWithMessage($msg, "danger", $lit2);
}


/* -------------------------------------------------------
   VARIABLES
------------------------------------------------------- */
$lit2 = trim($_POST['lit']??"null");
$fac  = $_SESSION['fac']; 
$idLit = (int)getIdByLit($lit2);

$num_titulaire  = trim($_POST['num_titulaire']??"");
$num_suppleant  = isset($_POST['num_suppleant']) ? trim($_POST['num_suppleant']) : "";

$niveauFormation = $fac . "/SOCIALE/" . $lit2;

$occupants = getOccupantsByLit($lit2); 
$nb = count($occupants);

$litIndividuel = isLitIndividuel($lit2);

/* -------------------------------------------------------
   FONCTION : Vérifier si étudiant affecté dans un autre lit
------------------------------------------------------- */
function isAffecteDansAutreLit($idEtu, $litActuel) {
    global $connexion;

    $sql = "
        SELECT 1 
        FROM codif_affectation a
        JOIN codif_lit l ON l.id_lit = a.id_lit
        WHERE a.id_etu = ?
        AND l.lit != ?
        LIMIT 1
    ";

    $stmt = mysqli_prepare($connexion, $sql);
    mysqli_stmt_bind_param($stmt, "is", $idEtu, $litActuel);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    return mysqli_stmt_num_rows($stmt) > 0;
}

/* -------------------------------------------------------
   UPDATE ETUDIANT & QUOTA
------------------------------------------------------- */
function updateEtudiant2($connexion, $idEtu, $niveauFormation, $fac, $moyenne)
{
    $sql = "UPDATE codif_etudiant SET niveauFormation=?, moyenne=?, faculte=?, departement=? WHERE id_etu=?";
    $stmt = mysqli_prepare($connexion, $sql);
    mysqli_stmt_bind_param($stmt, "sssdi", $niveauFormation,$fac,$fac, $moyenne, $idEtu);
    mysqli_stmt_execute($stmt);
}


/* -------------------------------------------------------
   GET OR CREATE STUDENT
------------------------------------------------------- */
function getOrCreateStudentId($connexion, $num_etu, $niveauFormation, $moyenneFixee, $litActuel)
{
    global $fac;

    $id = getIdByNumCarte($num_etu);

    if ($id) {

        // Vérifier si l'étudiant est déjà dans le lit courant
        $occupants = getOccupantsByLit($litActuel);

        foreach ($occupants as $occupant) {
            if ($occupant['id_etu'] == $id) {

                updateEtudiant2(
                    $connexion,
                    $id,
                    $niveauFormation,
                    $fac,
                    $moyenneFixee
                );

                return $id;
            }
        }

        // Vérifier qu'il n'est pas dans un autre lit
        if (isAffecteDansAutreLit($id, $litActuel)) {
            error("L’étudiant ($num_etu) est déjà affecté dans un autre lit.");
        }

        updateEtudiant2(
            $connexion,
            $id,
            $niveauFormation,
            $fac,
            $moyenneFixee
        );

        return $id;
    }

    // ============================
    // Etudiant inexistant
    // ============================

    $etu = getDonneesEtudiant_2($num_etu);

    if (!$etu) {
        error("Impossible de récupérer les données de l’étudiant ($num_etu).");
    }

    if ($etu["etat_inscription"] != "Inscrit(e)") {
        error("L’étudiant ($num_etu) n’est pas inscrit.");
    }

    if ($etu["payant"] != "Régime Non Payant") {
        error("L’étudiant ($num_etu) est en régime payant.");
    }

    if ($etu["annee"] != $_SESSION["annee"]) {
        error("L’étudiant ($num_etu) n’est pas inscrit pour l'année académique ".$_SESSION["annee"]);
    }

    return enregistrerEtudiant(
        $connexion,
        $num_etu,
        $etu['prenom'],
        $etu['nom'],
        $etu['telephone'],
        $etu['lieu_naissance'],
        $etu['date_naissance'],
        $fac,
        $fac,
        $niveauFormation,
        $moyenneFixee,
        $etu['num_identite'],
        $etu['sexe']
    );
}

/* -------------------------------------------------------
   VERIFICATION : TITULAIRE OBLIGATOIRE
------------------------------------------------------- */
if ($num_titulaire == "") {
    error("Le titulaire est obligatoire.");
}

/* -------------------------------------------------------
   TRAITEMENT TITULAIRE
------------------------------------------------------- */

$moyenneTitulaire = 10;

$idTitulaire = getOrCreateStudentId(
    $connexion,
    $num_titulaire,
    $niveauFormation,
    $moyenneTitulaire,
    $lit2
);

$occupants = getOccupantsByLit($lit2);
$nb = count($occupants);

// Aucun occupant
if ($nb == 0) {

    addAffectation($idLit, $idTitulaire);
    updatequota($connexion, $idLit, $niveauFormation);

    $occupants = getOccupantsByLit($lit2);
    $nb = count($occupants);

}
// Vérifier que le titulaire est bien celui du lit
else {

    $titulaireExiste = false;

    foreach ($occupants as $o) {
        if ($o['id_etu'] == $idTitulaire) {
            $titulaireExiste = true;
            break;
        }
    }

    if (!$titulaireExiste) {
        error("Un autre titulaire est déjà enregistré dans ce lit.");
    }
}

/* -------------------------------------------------------
   TRAITEMENT SUPPLEANT
------------------------------------------------------- */

if (!empty($num_suppleant)) {

    if ($litIndividuel) {
        error("Ce lit est individuel : aucun suppléant n'est autorisé.");
    }

    $occupants = getOccupantsByLit($lit2);
    $nb = count($occupants);

    if ($nb >= 2) {
        error("Ce lit possède déjà un suppléant.");
    }

    $moyenneSuppleant = 5;

    $idSuppleant = getOrCreateStudentId(
        $connexion,
        $num_suppleant,
        $niveauFormation,
        $moyenneSuppleant,
        $lit2
    );

    if ($idSuppleant == $idTitulaire) {
        error("Le suppléant doit être différent du titulaire.");
    }

    foreach ($occupants as $o) {
        if ($o['id_etu'] == $idSuppleant) {
            error("Cet étudiant est déjà enregistré dans ce lit.");
        }
    }

    addAffectationOnSuppleant($idLit, $idSuppleant);
}

/* -------------------------------------------------------
   SUCCESS
------------------------------------------------------- */
redirectWithMessage("Attribution enregistrée avec succès !", "success", $lit2);