<?php
include ('../../traitement/fonction.php');
header('Content-Type: application/json');

$numero = $_GET['num'] ?? null;
$type = $_GET['type'] ?? null;

if (!$numero || !$type) {
    echo json_encode(['success' => false, 'message' => 'Paramètres manquants']);
    exit;
}

/* ========= RÉCUPÉRATION ÉTUDIANT (LOCAL OU API) ========= */
$etudiant = getDonneesEtudiant_2($numero);
if (!$etudiant) {
    echo json_encode(['success' => false, 'message' => 'Étudiant introuvable']);
    exit;
}

/* ========= ID LOCAL (PEUT ÊTRE NULL) ========= */
$idLocal = getIdByNumCarte($numero);
$apaye = etudiantAPaye($connexion, $numero);

/* ================= TITULAIRE ================= */
if ($type === 'titulaire') {
    if (!$idLocal) {
        echo json_encode([
            'success' => false,
            'message' => "Le l'etudiant doit obligatoirement exister a la base local"
        ]);
        exit;
    }
    $etudiant_2 = studentConnect($numero);
    $quotaRow = getQuotaClasse(
        $etudiant_2['niveauFormation'],
        $etudiant_2['sexe']
    );

    $quota = ($quotaRow && isset($quotaRow['COUNT(*)']))
        ? (int) $quotaRow['COUNT(*)']
        : 0;

    $dataStatutStudent = getOnestudentStatus(
        $quota,
        $etudiant_2['niveauFormation'],
        $etudiant_2['sexe'],
        $numero
    );
    /* if (stripos($etudiant_2['etablissement'], 'social') === false) {
    echo json_encode([
        'success' => false,
        'message' => 'Étudiant quota non social'
    ]);
    exit;
    } */ 

    // VERIFIER SI L'etudiant est attributaire ou suppleant
    $statut = $dataStatutStudent['statut'] ?? null;
    if ($statut == 'Forclos(e)') {
            echo json_encode([
                'success' => false,
                'message' => 'Étudiant Forclos(e)'
            ]);
            exit;
        }
    if ($statut !== 'Attributaire' && $statut !== 'Suppleant(e)') {
        echo json_encode([
            'success' => false,
            'message' => 'Le sortant doit obligatoirement être Attributaire ou Suppleant(e)'
        ]);
        exit;
    }
}

/* ================= SUPPLÉANT ================= */
/* ================= SUPPLÉANT ================= */
if ($type === 'suppleant') {
    // Interdit : déjà logé
    if ($idLocal !== null && isAffecte($idLocal)) {
        echo json_encode([
            'success' => false,
            'message' => 'L\'entrant ne doit pas être affecté à un lit'
        ]);
        exit;
    }

    // Interdit : statut bloquant
    if ($idLocal !== null) {
        $etudiant_2 = studentConnect($numero);
        $quotaRow = getQuotaClasse(
            $etudiant_2['niveauFormation'],
            $etudiant_2['sexe']
        );

        $quota = ($quotaRow && isset($quotaRow['COUNT(*)']))
            ? (int) $quotaRow['COUNT(*)']
            : 0;

        $dataStatutStudent = getOnestudentStatus(
            $quota,
            $etudiant_2['niveauFormation'],
            $etudiant_2['sexe'],
            $numero
        );

        // Vérifier si $dataStatutStudent est bien un tableau et contient 'statut'
        $statut = $dataStatutStudent['statut'] ?? null;

        if ($statut == 'Forclos(e)') {
            echo json_encode([
                'success' => false,
                'message' => 'Étudiant Forclos(e)'
            ]);
            exit;
        }
        if ($statut == 'Attributaire' || $statut == 'Suppleant(e)') {
            echo json_encode([
                'success' => false,
                'message' => 'Étudiant déjà Attributaire ou Suppléant'
            ]);
            exit;
        }
        
    }

    // Tous les autres cas → OK
}

 /* 
 if ($etudiant['etat_inscription'] !== 'Inscrit(e)') {
    echo json_encode(['success' => false, 'message' => 'Étudiant non inscrit']);
    exit;
    }*/

if ($etudiant['payant'] !== 'Régime Non Payant') {
    echo json_encode(['success' => false, 'message' => 'Régime payant']);
    exit;
}

$anneeEtudiant = $etudiant['annee']; // ex: 2024_2025
$anneeFin = intval(substr($anneeEtudiant, -4)); // 2025
$annee = isset($_SESSION['annee']) ? $_SESSION['annee'] : date('Y');
$anneeCourante = intval(substr($annee, -4)); 

/* if (($anneeCourante - $anneeFin) >= 2) {
    echo json_encode([
        'success' => false,
        'message' => 'Année académique invalide (ancien étudiant)'
    ]);
    exit;
} 
 */

/* ========= RÉPONSE ========= */
echo json_encode([
    'success' => true,
    'nom' => $etudiant['nom'],
    'prenom' => $etudiant['prenom'], 
    'faculte' => $etudiant['faculte'],
    'departement' => $etudiant['departement'],
    'telephone' => $etudiant['telephone'],
    'sexe' => $etudiant['sexe'],
    'etat_inscription' => $etudiant['etat_inscription'],
    'payant' => $etudiant['payant'],
    'annee' => $etudiant['annee'],
    'estAffecte' => $idLocal ? isAffecte($idLocal) : false,
    "a_paye" => $apaye
]);