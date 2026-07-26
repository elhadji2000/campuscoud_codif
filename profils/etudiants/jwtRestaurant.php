<?php
session_start();

require '../../vendor/autoload.php';
function getDonneesEtudiant_2($numero_carte)
{
    $result = [
        'faculte' => null,
        'departement' => null,
        'nom' => null,
        'prenom' => null,
        'date_naissance' => null,
        'lieu_naissance' => null,
        'sexe' => null,
        'num_identite' => null,
        'telephone' => null,
        'etat_inscription' => null,
        'niveau_formation' => null,
        'email_ucad' => null,
        'payant' => null,
        'annee' => null
    ];

    try {
        $json_url = "https://coud@ucad.sn:dhHNg4VmpfZYR6Q@coudservice.ucad.sn/api/etudiant/$numero_carte";
        $json = @file_get_contents($json_url);

        // Si l'API ne répond pas ou retourne une erreur
        if ($json === false) {
            return null;
        }

        $data = json_decode($json);

        // Vérifie si $data contient bien un index 0 et que c’est un objet
        if (isset($data[0]) && is_object($data[0])) {
            $result['faculte'] = $data[0]->faculte ?? null;
            $result['departement'] = $data[0]->departement ?? null;
            $result['nom'] = $data[0]->nom ?? null;
            $result['prenom'] = $data[0]->prenom ?? null;
            $result['date_naissance'] = $data[0]->date_naissance ?? null;
            $result['lieu_naissance'] = $data[0]->lieu_naissance ?? null;
            $result['sexe'] = $data[0]->sexe ?? null;
            if ($result['sexe'] == 'M') {
                $result['sexe'] = 'G';
            }
            $result['num_identite'] = $data[0]->num_identite ?? null;
            $result['telephone'] = $data[0]->telephone ?? null;
            $result['etat_inscription'] = $data[0]->etat_inscription ?? null;
            $result['payant'] = $data[0]->payant ?? null;
            $result['niveau_formation'] = $data[0]->niveau_formation ?? null;
            $result['email_ucad'] = $data[0]->niveau_formation ?? null;
            $result['annee'] = $data[0]->annee ?? null;
            $result['annee'] = str_replace('-', '_', $result['annee']);
        } else {
            // Si l'API ne renvoie rien ou pas de résultat
            return null;
        }
    } catch (Exception $e) {
        return null;
    }

    return $result;
}

use Firebase\JWT\JWT;

if (!isset($_SESSION['id_user'])) {
    die("Utilisateur non connecté");
}

// Récupération des informations de l'étudiant
$etu = getDonneesEtudiant_2($_SESSION['num_etu']);

if ($etu == null) {
    die("Impossible de récupérer les informations de l'étudiant.");
}

$privateKey = file_get_contents("../../private/private.pem");

$payload = [

    "iss" => "https://campuscoud.com",

    "aud" => "https://resto.esp.sn",

    "iat" => time(),

    "exp" => time() + 300,

    "jti" => bin2hex(random_bytes(16)),

    "numero" => $_SESSION['num_etu'],

    "nom" => $etu['nom'],

    "prenom" => $etu['prenom'],

    "faculte" => $etu['faculte'],

    "departement" => $etu['departement'],

    "sexe" => $etu['sexe'],

    "annee" => $etu['annee']

];

$jwt = JWT::encode($payload, $privateKey, 'RS256');

header(
    "Location:http://localhost/cCoud/profils/etudiants/verifier.php?token=" . urlencode($jwt)
);

exit;