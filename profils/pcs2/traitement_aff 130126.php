<?php
session_start();
include('../../traitement/fonction.php');
global $connexion;
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
function updateEtudiant2($connexion, $idEtu, $niveauFormation, $moyenne)
{
    $sql = "UPDATE codif_etudiant SET niveauFormation=?, moyenne=? WHERE id_etu=?";
    $stmt = mysqli_prepare($connexion, $sql);
    mysqli_stmt_bind_param($stmt, "sdi", $niveauFormation, $moyenne, $idEtu);
    mysqli_stmt_execute($stmt);
}


/* -------------------------------------------------------
   GET OR CREATE STUDENT
------------------------------------------------------- */
function getOrCreateStudentId($connexion, $num_etu, $niveauFormation, $moyenneFixee, $litActuel)
{
    $id = getIdByNumCarte($num_etu);

    if ($id) {
        $ee = studentConnect($num_etu);
        $quotaRow = getQuotaClasse($ee['niveauFormation'], $ee['sexe']);
        $quota = intval($quotaRow['COUNT(*)']);
        $dataStatutStudent = getOnestudentStatus($quota, $ee['niveauFormation'], $ee['sexe'], $num_etu);
        if($dataStatutStudent["statut"] == "Attributaire"){
            error("L’étudiant ($num_etu) est déjà Attributaire.");
        }
        if($dataStatutStudent["statut"] == "Suppleant(e)"){
            error("L’étudiant ($num_etu) est déjà Suppleant(e).");
        }

        // Si étudiant affecté dans un autre lit → erreur
        if (isAffecteDansAutreLit($id, $litActuel)) {
            error("L’étudiant ($num_etu) est déjà affecté dans un autre lit.");
        }

        updateEtudiant2($connexion, $id, $niveauFormation, $moyenneFixee);
        return $id;
    }

    // NON EXISTANT → récupération API
    $etu = getDonneesEtudiant_2($num_etu);
    if (!$etu) {
        error("Impossible de récupérer les données de l’étudiant ($num_etu).");
    }
    elseif ($etu["etat_inscription"] != "Inscrit(e)") {
        error("l’étudiant ($num_etu) n’est pas Inscrit(e).");
    }
    elseif ( $etu["payant"] != "Régime Non Payant") {
        error("l’étudiant ($num_etu) est Régime payant.");
    }
    elseif ( $etu["annee"] != $_SESSION["annee"]) {
        error("l’étudiant ($num_etu) n’est pas Inscrit(e) a l'annee academique ". $_SESSION['annee']);
    }

    return enregistrerEtudiant2(
        $connexion,
        $num_etu,
        $etu['prenom'],
        $etu['nom'],
        $etu['telephone'],
        $etu['lieu_naissance'],
        $etu['date_naissance'],
        $etu['faculte'],
        $etu['departement'],
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
$idTitulaire = getOrCreateStudentId($connexion, $num_titulaire, $niveauFormation, $moyenneTitulaire, $lit2);

// Cas 1: lit vide → insertion du titulaire
if ($nb == 0) {

    addAffectation($idLit, $idTitulaire);
    updatequota($connexion, $idLit, $niveauFormation);
    $nb++;

// Cas 2: lit non vide et titulaire différent → erreur
} elseif ($occupants[0]['id_etu'] != $idTitulaire) {

    error("Un titulaire existe déjà dans ce lit.");

}

/* -------------------------------------------------------
   TRAITEMENT SUPPLEANT
------------------------------------------------------- */
if ($num_suppleant != "") {

    if ($litIndividuel) {
        error("Ce lit est individuel : aucun suppléant n'est autorisé.");
    }

    if ($nb == 2) {
        error("Ce lit possède déjà un suppléant.");
    }

    $moyenneSuppleant = 5;
    $idSuppleant = getOrCreateStudentId($connexion, $num_suppleant, $niveauFormation, $moyenneSuppleant, $lit2);

    if ($nb == 0) {
        error("Impossible d'ajouter un suppléant avant le titulaire.");
    }

    if ($nb == 1) {
        addAffectationOnSuppleant($idLit, $idSuppleant);
    }
}

/* -------------------------------------------------------
   SUCCESS
------------------------------------------------------- */
redirectWithMessage("Attribution enregistrée avec succès !", "success", $lit2);


