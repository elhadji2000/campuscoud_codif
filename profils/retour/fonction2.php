<?php

// Compte les étudiants déjà affectés (classe + sexe)
function countAffected($classe, $sexe) {
    global $connexion;

    // On compte le nombre de lits déjà affectés pour ce niveau et sexe
    $sql = "SELECT COUNT(*) AS total
            FROM codif_affectation a
            JOIN codif_lit l ON l.id_lit = a.id_lit
            JOIN codif_quota q ON q.id_lit_q = l.id_lit
            JOIN codif_etudiant e ON e.id_etu = a.id_etu
            WHERE e.niveauFormation = '$classe'
              AND e.sexe = '$sexe'";

    $res = mysqli_query($connexion, $sql);
    return intval(mysqli_fetch_assoc($res)['total']);
}
function getQuotaRestante($classe, $sexe) {
    global $connexion;

    // Total de lits pour ce niveau et sexe
    $sqlTotal = "SELECT COUNT(*) AS totalQuota
                 FROM codif_quota q
                 JOIN codif_lit l ON l.id_lit = q.id_lit_q
                 WHERE q.niveauFormation = '$classe'
                   AND l.sexe = '$sexe'";
    $resTotal = mysqli_query($connexion, $sqlTotal);
    $totalQuota = intval(mysqli_fetch_assoc($resTotal)['totalQuota']);

    // Nombre de lits déjà affectés pour ce niveau et sexe
    $sqlAffect = "SELECT COUNT(*) AS totalAffect
                  FROM codif_affectation a
                  JOIN codif_lit l ON l.id_lit = a.id_lit
                  JOIN codif_etudiant e ON e.id_etu = a.id_etu
                  WHERE e.niveauFormation = '$classe'
                    AND e.sexe = '$sexe'";
    $resAffect = mysqli_query($connexion, $sqlAffect);
    $totalAffect = intval(mysqli_fetch_assoc($resAffect)['totalAffect']);

    // Quota restante
    $quotaRestante = max(0, $totalQuota - $totalAffect);
    return $quotaRestante;
}


// Marque le statut (optionnel, mais utile)
function computeStatus(&$students, $quota_total) {
    foreach ($students as &$row) {

        if (empty($row["rang"])) {
            $row["statut"] = "HorsClasse";
            continue;
        }

        if ($row["forclusion"] !== NULL) {
            $row["statut"] = "Forclos(e)";
            continue;
        }

        if ($quota_total == 0) {
            $row["statut"] = "Non Defini";
        } elseif ($row["rang"] <= $quota_total) {
            $row["statut"] = "Attributaire";
        } elseif ($row["rang"] <= $quota_total * 2) {
            $row["statut"] = "Suppleant(e)";
        } else {
            $row["statut"] = "Non Attributaire";
        }
    }
}

// Charge les lits de quota d'une classe
function loadQuota($classe, $sexe) {
    global $connexion;

    $classe = mysqli_real_escape_string($connexion, $classe) . '%';
    $sql = "SELECT COUNT(*) AS quota 
            FROM codif_quota 
            JOIN codif_lit ON codif_lit.id_lit = codif_quota.id_lit_q 
            WHERE NiveauFormation LIKE '$classe' 
              AND codif_lit.sexe = '$sexe'";

    $res = mysqli_query($connexion, $sql);
    return mysqli_fetch_assoc($res)['quota'];
}

// Charge tous les étudiants d'une classe et calcule le rang
function loadRankedStudents($classe, $sexe) {
    global $connexion;

    $classe_escaped = mysqli_real_escape_string($connexion, $classe);
    $sexe_escaped   = mysqli_real_escape_string($connexion, $sexe);

    $sql = "
        SELECT ce.*, ranks.rang,
        CASE WHEN cf.id_etu IS NOT NULL THEN 'Forclos(e)' ELSE NULL END AS forclusion
        FROM codif_etudiant ce
        LEFT JOIN (
            SELECT 
                id_etu,
                ROW_NUMBER() OVER (
                    ORDER BY sessionId ASC, moyenne DESC, id_etu ASC, dateNaissance ASC
                ) AS rang
            FROM codif_etudiant
            WHERE niveauFormation = '$classe_escaped'
              AND sexe = '$sexe_escaped'
              AND id_etu NOT IN (SELECT id_etu FROM codif_forclusion)
        ) ranks ON ce.id_etu = ranks.id_etu
        LEFT JOIN codif_forclusion cf ON ce.id_etu = cf.id_etu
        WHERE ce.niveauFormation = '$classe_escaped'
          AND ce.sexe = '$sexe_escaped'
        ORDER BY rang ASC
    ";

    $result = mysqli_query($connexion, $sql);
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[$row["num_etu"]] = $row;
    }
    return $data;
}
function loadQuota_2($fac, $sexe) {
    global $connexion;

    $fac_escaped  = mysqli_real_escape_string($connexion, $fac);
    $sexe_escaped = mysqli_real_escape_string($connexion, $sexe);

    $sql = "
        SELECT COUNT(DISTINCT q.id_lit_q) AS quota
        FROM codif_quota q
        JOIN codif_lit l ON l.id_lit = q.id_lit_q
        WHERE l.sexe = '$sexe_escaped'
          AND q.id_lit_q IN (
              SELECT DISTINCT q2.id_lit_q
              FROM codif_quota q2
              JOIN codif_etudiant e ON e.niveauFormation = q2.niveauFormation
              WHERE e.etablissement LIKE '$fac_escaped'
          )
    ";

    $res = mysqli_query($connexion, $sql);
    $row = mysqli_fetch_assoc($res);
    return intval($row['quota']);
}

function countAffected_2($classe, $sexe) {
    global $connexion;

    // On compte le nombre de lits déjà affectés pour ce niveau et sexe
    $sql = "SELECT COUNT(DISTINCT a.id_lit) AS total
            FROM codif_affectation a
            JOIN codif_lit l ON l.id_lit = a.id_lit
            JOIN codif_quota q ON q.id_lit_q = l.id_lit
            JOIN codif_etudiant e ON e.id_etu = a.id_etu
            WHERE e.etablissement = '$classe'
              AND e.sexe = '$sexe'
              AND q.id_lit_q IN (
              SELECT DISTINCT q2.id_lit_q
              FROM codif_quota q2
              JOIN codif_etudiant e ON e.niveauFormation = q2.niveauFormation
              WHERE e.etablissement LIKE '$classe'
          )
              ";

    $res = mysqli_query($connexion, $sql);
    return intval(mysqli_fetch_assoc($res)['total']);
}


function getAttributaireAndSuppleantByFac_2($fac) {
    global $connexion;

    $resultBySexe = ['F' => [], 'G' => []];
    $etuRows = getEtuNonAffByFac($fac);
    static $cache = [];

    // Récupérer le quota restant global pour l'établissement, par sexe
    $quotaRestantParSexe = [];

foreach (['F', 'G'] as $sexe) {
    // On récupère le quota total pour l'établissement et le sexe
    $totalQuota = loadQuota_2($fac, $sexe);

    // On récupère le nombre déjà affecté pour l'établissement et le sexe
    $totalAffect = countAffected_2($fac, $sexe);

    // Calcul du quota restant
    $quotaRestantParSexe[$sexe] = max(0, $totalQuota - $totalAffect);
}


    // Parcourir les étudiants non affectés
    foreach ($etuRows as $etu) {
        $classe = $etu['niveauQuota'];
        $sexe   = $etu['sexe'];
        $num_etu = $etu['num_etu'];

        $key = $classe."-".$sexe;

        // Cache par classe/sexe
        if (!isset($cache[$key])) {
            $quota_total = loadQuota($classe, $sexe);
            $students = loadRankedStudents($classe, $sexe);
            computeStatus($students, $quota_total);

            $cache[$key] = [
                "quota_total" => $quota_total,
                "students" => $students
            ];
        }

        $data = $cache[$key];
        $students = $data["students"];
        $quota_total = $data["quota_total"];

        if (!isset($students[$num_etu]) || empty($students[$num_etu]["rang"])) {
            continue;
        }

        $etuData = $students[$num_etu];
        if ($etuData["rang"] > $quota_total) {
            continue;
        }

        $rangTitulaire = $etuData["rang"];
        $rangSuppleant = $rangTitulaire + $quota_total;

        $suppleant = null;
        foreach ($students as $st) {
            if ($st["rang"] == $rangSuppleant) {
                $suppleant = $st;
                break;
            }
        }

        $entry = [
            "lit" => null,
            "indiv" => null,
            "titulaire" => [
                "id_etu" => $etuData['id_etu'],
                "num_etu" => $etuData['num_etu'],
                "nom"     => $etuData['nom'],
                "prenoms" => $etuData['prenoms'],
                "classe"  => $classe,
                "sexe"    => $sexe,
                "rang"    => $rangTitulaire
            ],
            "suppleant" => $suppleant ? [
                "id_etu" => $suppleant['id_etu'],
                "num_etu" => $suppleant['num_etu'],
                "nom"     => $suppleant['nom'],
                "prenoms" => $suppleant['prenoms'],
                "classe"  => $classe,
                "sexe"    => $sexe,
                "rang"    => $suppleant['rang']
            ] : null
        ];

        $resultBySexe[$sexe][] = $entry;
    }

    // Tri et limitation par quota restant global par sexe
    $finalResult = [];
    foreach (['F', 'G'] as $sexe) {
        if (empty($resultBySexe[$sexe])) continue;

        usort($resultBySexe[$sexe], fn($a,$b) => $a['titulaire']['rang'] <=> $b['titulaire']['rang']);

        // Limiter selon quota restant global
        $finalResult = array_merge($finalResult, array_slice($resultBySexe[$sexe], 0, $quotaRestantParSexe[$sexe]));
    }


    return $finalResult;
}


?>


<?php
// Version optimisée finale
/* function getAttributaireAndSuppleantByFac_2($fac) {
    global $connexion;

    $result = [];
    $etuRows = getEtuNonAffByFac($fac);

    static $cache = [];

    foreach ($etuRows as $etu) {
        $classe = $etu['niveauQuota'];
        $sexe   = $etu['sexe'];
        $num_etu = $etu['num_etu'];

        $key = $classe."-".$sexe;

        // Cache par classe/sexe
        if (!isset($cache[$key])) {

            // Quota total
            $quota_total = loadQuota($classe, $sexe);

            // Quota restante calculée via les lits affectés
            $quota_restant = getQuotaRestante($classe, $sexe);

            $students = loadRankedStudents($classe, $sexe);
            computeStatus($students, $quota_total);

            $cache[$key] = [
                "quota_total"   => $quota_total,
                "quota_restant" => $quota_restant,
                "students"      => $students
            ];
        }

        $data = $cache[$key];
        $students       = $data["students"];
        $quota_total    = $data["quota_total"];
        $quota_restant  = $data["quota_restant"];

        // Étudiant trouvé ?
        if (!isset($students[$num_etu])) {
            continue;
        }

        $etuData = $students[$num_etu];

        // Rang valide ?
        if (empty($etuData["rang"])) {
            continue;
        }

        // SEULE condition du titulaire : rang <= quota_total
        if ($etuData["rang"] > $quota_total) {
            continue;
        }

        $rangTitulaire = $etuData["rang"];

        // Trouver suppléant (rang + quota_total)
        $suppleant = null;
        $rangSuppleant = $rangTitulaire + $quota_total;
        foreach ($students as $st) {
            if ($st["rang"] == $rangSuppleant) {
                $suppleant = $st;
                break;
            }
        }

        $result[] = [
            "lit"   => null,
            "indiv" => null,
            "titulaire" => [
                "num_etu" => $etuData['num_etu'],
                "nom"     => $etuData['nom'],
                "prenoms" => $etuData['prenoms'],
                "classe"  => $classe,
                "sexe"    => $sexe,
                "rang"    => $rangTitulaire
            ],
            "suppleant" => $suppleant ? [
                "num_etu" => $suppleant['num_etu'],
                "nom"     => $suppleant['nom'],
                "prenoms" => $suppleant['prenoms'],
                "classe"  => $classe,
                "sexe"    => $sexe,
                "rang"    => $suppleant['rang']
            ] : null
        ];
    }

    // Tri final par rang du titulaire
    usort($result, fn($a, $b) =>
        $a['titulaire']['rang'] <=> $b['titulaire']['rang']
    );


    return $result;
} */
?>
