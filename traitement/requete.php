<?php 
// Verifier la session si elle est actif, sinon on redirige vers la racine
if (empty($_SESSION['username']) && empty($_SESSION['mdp'])) {
    header('Location: /campuscoud.com/');
    exit();
}
// Verifier si la session stock toujours la valeur du niveau de la classe, sinon on l'initialise
if (isset($_SESSION['classe'])) {
    $classe = $_SESSION['classe'];
} else {
    $classe = "";
}
// appelle la page fonction.php
require_once(__DIR__ . '/fonction.php');

// Declaration des variables et tableaux
$tableauDataFaculte = [];
$tableauDataNiveauFormation = [];
$erreurClasse = "";
$messageErreurFaculte = "";
$messageErreurDepartement = "";

// Appel de la fonction getAllEtablissement() dans fonction.php, celle-ci affiche la liste de tous les etablissements
$dataEtablissement = getAllEtablissement();
if (isset($_GET['fac']) && !empty($_GET['fac'])) {
    // Appel de la fonction getAllDepartement() dans fonction.php, celle-ci affiche la liste de tous les Departements
    $resultatRequeteDepartement = getAllDepartement($_GET['fac']);
    // Appel de la fonction getOneByDepartemennt() dans fonction.php, celle-ci affiche la liste de tous les Departements sous format tableau
    $tableauDataFaculte = getOneByDepartemennt($resultatRequeteDepartement);
    if (isset($_GET['dep']) && !empty($_GET['dep'])) {
        $getDataDepartement = $_GET['dep'];
        // Appel de la fonction getAllNiveau() dans fonction.php, celle-ci affiche la liste de tous les niveau de formation
        $tableauDataNiveauFormation = getAllNiveau($getDataDepartement);
        if (isset($_GET['fac']) && $_GET['dep'] && $_GET['classe']) {
            $_SESSION['classe'] = $_GET['classe'];
            if ($_SESSION['profil'] == 'quota') {
                header("location:../personnels/listeLits.php?classe=" . $_SESSION['classe']);
            }
        } else {
            $erreurClasse = "La Classe est obligatoire !";
        }
    } else {
        $messageErreurDepartement = "Le Département est obligatoire !";
    }
} else {
    $messageErreurFaculte = "La Faculté est obligatoire !";
}
// Liste des chambres deja affecter a une classe selon le niveau de la classe
$resultatRequeteLitClasse = getLitOneByNiveau($classe, $_SESSION['sexe_agent']);

// Liste des pavillons deja affecter a une classe selon le niveau de la classe, elle sera appeler dans la page detailsLits.php (elle sert de filtre)
$resultatRequetePavillonClasse = getPavillonOneByNiveau($classe, $_SESSION['sexe_agent']);
// Liste des lits a valider selon la classe, elle sera valider par le personnels
$resultatRequeteLitClasseByValidePerso = getLitOneByNiveauFromPersonnel($classe, $_SESSION['sexe_agent']);
// affichage de toutes les lits de la table cofif_lit avec les option migré et non migré
$resultatRequeteTotalLit = getAllLit($_SESSION['sexe_agent']);


//Affiché la liste total des pavillon
$resultatRequetePavillon = getAllPavillon($_SESSION['sexe_agent']);
