<?php
// include('../traitement/fonction.php');
header('Content-Type: application/json; charset=UTF-8');

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

$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$segments = explode('/', $uri);
$numero = trim(end($segments));

if (empty($numero)) {
    http_response_code(400);

    echo json_encode([
        'success' => false,
        'message' => 'Numéro étudiant obligatoire.'
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

    exit;
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Connexion à la base
    $connexion = mysqli_connect(
        'localhost',
        'root',
        '',
        'campuscoud'
    );

    if (!$connexion) {
        throw new Exception('Impossible de se connecter à la base de données.');
    }

    mysqli_set_charset($connexion, 'utf8');

    // Si getDonneesEtudiant() utilise "global $connexion"
    $GLOBALS['connexion'] = $connexion;

    /** Vérifier que l'étudiant possède un compte */
    $sql = '
        SELECT id_user
        FROM codif_user
        WHERE username_user = ?
        LIMIT 1
    ';

    $stmt = mysqli_prepare($connexion, $sql);
    mysqli_stmt_bind_param($stmt, 's', $numero);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 0) {
        mysqli_close($connexion);

        http_response_code(404);

        echo json_encode([
            'success' => false,
            'message' => 'Cet étudiant ne possède pas de compte.'
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        exit;
    }

    /** Récupération des informations de l'étudiant */
    $etu = getDonneesEtudiant_2($numero);

    if (empty($etu)) {
        mysqli_close($connexion);

        http_response_code(404);

        echo json_encode([
            'success' => false,
            'message' => 'Étudiant introuvable.'
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        exit;
    }

    /* $data = [
        'numero' => $numero,
        'faculte' => $etu['faculte'],
        'departement' => $etu['departement'],
        'nom' => $etu['nom'],
        'prenom' => $etu['prenom'],
        'date_naissance' => $etu['date_naissance'],
        'lieu_naissance' => $etu['lieu_naissance'],
        'sexe' => $etu['sexe'],
        'num_identite' => $etu['num_identite'],
        'telephone' => $etu['telephone'],
        'etat_inscription' => $etu['etat_inscription'],
        'niveau_formation' => $etu['niveau_formation'],
        'email_ucad' => $etu['email_ucad'],
        'payant' => $etu['payant'],
        'annee' => $etu['annee']
    ]; */

    $etu['numero'] = $numero;

    mysqli_close($connexion);

    echo json_encode([
        'success' => true,
        'message' => 'Étudiant trouvé.',
        'data' => $etu
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
} catch (Exception $e) {
    if (isset($connexion) && $connexion) {
        mysqli_close($connexion);
    }

    http_response_code(500);

    echo json_encode([
        'success' => false,
        'message' => 'Erreur serveur.',
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}
