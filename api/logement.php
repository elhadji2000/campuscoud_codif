<?php

header('Content-Type: application/json; charset=UTF-8');

/* Authentification */
$utilisateurs = [
    [
        'login' => 'admin',
        'password' => '123456'
    ],
    [
        'login' => 'agent1',
        'password' => 'agent123'
    ],
    [
        'login' => 'agent2',
        'password' => 'agent456'
    ]
];

$user = $_SERVER['PHP_AUTH_USER'] ?? '';
$pass = $_SERVER['PHP_AUTH_PW'] ?? '';

$autorise = false;

foreach ($utilisateurs as $u) {
    if (
        $u['login'] === $user &&
        $u['password'] === $pass
    ) {
        $autorise = true;
        break;
    }
}

if (!$autorise) {
    header('WWW-Authenticate: Basic realm="API Logement"');
    header('HTTP/1.0 401 Unauthorized');

    echo json_encode([
        'success' => false,
        'message' => 'Authentification requise'
    ]);
    exit;
}
$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

$segments = explode('/', $uri);

$numero = end($segments);
if (empty($numero)) {
    http_response_code(400);

    echo json_encode([
        'success' => false,
        'message' => 'Numéro obligatoire'
    ]);
    exit;
}

$numero = trim($numero);

/** Bases à consulter */
$annee = date('Y');

$bases_2 = [
    [
        'base' => 'u893234126_BdCc_' . ($annee - 1) . '_' . $annee,
        'pw' => 'Pass_BdCc_' . ($annee - 1) . '_' . $annee,
        'user' => 'u893234126_' . ($annee - 1) . '_' . $annee,
    ],
    [
        'base' => 'u893234126_BdCc_' . ($annee - 2) . '_' . ($annee - 1),
        'pw' => 'Pass_BdCc_' . ($annee - 2) . '_' . ($annee - 1),
        'user' => 'u893234126_' . ($annee - 2) . '_' . ($annee - 1),
    ]
];
$bases = [
    [
        'base' => 'campuscoud',
        'pw' => '',
        'user' => 'root'
    ],
    [
        'base' => 'campuscoud',
        'pw' => '',
        'user' => 'root'
    ]
];
$sql = " 
    SELECT
    e.num_etu,
    e.nom,
    e.prenoms,
    e.NiveauFormation,
    e.etablissement,
    COALESCE(aff_direct.statut, lg.statut) AS statut,
    l.campus,
    l.pavillon,
    l.chambre,
    l.lit
FROM codif_etudiant e

LEFT JOIN codif_loger lg
    ON lg.id_etu = e.id_etu

/* Affectation directe (Attributaire ou Suppléant) */
LEFT JOIN codif_affectation aff_direct
    ON aff_direct.id_etu = e.id_etu

/* Titulaire du même paiement */
LEFT JOIN codif_loger lg_tit
    ON lg_tit.id_paie = lg.id_paie
    AND lg_tit.statut = 'Attributaire'

/* Affectation du titulaire (pour les hébergés) */
LEFT JOIN codif_affectation aff_tit
    ON aff_tit.id_etu = lg_tit.id_etu

LEFT JOIN codif_lit l
    ON l.id_lit = COALESCE(
        aff_direct.id_lit,
        aff_tit.id_lit
    )

WHERE e.num_etu = ?
LIMIT 1
";

$datas = [];
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

foreach ($bases as $base) {
    try {
        $conn = mysqli_connect(
            'localhost',
            $base['user'],
            $base['pw'],
            $base['base']
        );

        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 's', $numero);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);

            $datas[] = [
                'annee' => str_replace('campuscoud_', '', $base['base']),
                'numero' => $row['num_etu'],
                'nom' => $row['nom'],
                'prenoms' => $row['prenoms'],
                'NiveauFormation' => $row['NiveauFormation'],
                'faculte' => $row['etablissement'],
                'statut' => $row['statut'],
                'campus' => $row['campus'],
                'pavillon' => $row['pavillon'],
                'chambre' => $row['chambre'],
                'lit' => $row['lit']
            ];
        }

        mysqli_close($conn);
    } catch (Exception $e) {
        // La base n'existe pas ou connexion impossible
        continue;
    }
}

if (empty($datas)) {
    http_response_code(404);

    echo json_encode([
        'success' => false,
        'message' => 'Aucun logement trouvé'
    ]);
    exit;
}

echo json_encode([
    'success' => true,
    'data' => $datas
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
