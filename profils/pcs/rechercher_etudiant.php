<?php
include('../../traitement/fonction.php'); // contient getDonneesEtudiant()

header('Content-Type: application/json');

if (!isset($_GET['num']) || empty($_GET['num'])) {
    echo json_encode(["success" => false]);
    exit();
}

$numero = $_GET['num'];
$etudiant = getDonneesEtudiant($numero);

if ($etudiant === null) {
    echo json_encode(["success" => false]);
    exit();
}
$idEtu = getIdByNumCarte($numero);
$estAffecte = isAffecte($idEtu);
echo json_encode([
    "success" => true,
    "nom" => $etudiant['nom'],
    "prenom" => $etudiant['prenom'],
    "faculte" => $etudiant['faculte'],
    "departement" => $etudiant['departement'],
    "telephone" => $etudiant['telephone'],
    "sexe" => $etudiant['sexe'],
    "date_naissance" => $etudiant['date_naissance'],
    "lieu_naissance" => $etudiant['lieu_naissance'],
    "estAffecte" => $estAffecte
]);
exit();
