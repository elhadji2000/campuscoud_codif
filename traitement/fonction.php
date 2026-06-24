<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*
 * Connectez-vous à votre base de données MySQL
 */

function connexionBD()
{
    // si l'année n'existe pas encore on met une valeur par défaut
    $annee = isset($_SESSION['annee']) ? $_SESSION['annee'] : date('Y');

    // nom dynamique de la base
    // $dbName = "u893234126_BdCc_" . $annee;
    // $user = "u893234126_" . $annee;
    // $pw = "MDP_" . $annee;

    $dbName = 'campuscoud';
    $user = 'root';
    $pw = '';

    $connexion = mysqli_connect('localhost', $user, $pw, $dbName) or
        die('Serveur inaccessible. Merci de reessayer plus tard.');

    return $connexion;
}

$connexion = connexionBD();


function connexionDb_suivante()
{
    mysqli_report(MYSQLI_REPORT_OFF);

    $serveur = 'localhost';
    $user    = 'root';
    $pw      = '';
    $base    = 'campuscoud';

    try {

        $check = mysqli_connect($serveur, $user, $pw);

        if (!$check) {
            return false;
        }

        $result = mysqli_query(
            $check,
            "SHOW DATABASES LIKE '$base'"
        );

        if (!$result || mysqli_num_rows($result) == 0) {
            mysqli_close($check);
            return false;
        }

        mysqli_close($check);

        $connexion = mysqli_connect(
            $serveur,
            $user,
            $pw,
            $base
        );

        return $connexion ?: false;

    } catch (Exception $e) {
        return false;
    }
}

$connexion_suivante = connexionDb_suivante();


// FONCTION POUR APLLI SECURITE COUD (https://campuscoud/securite/) ///////////////////////////////////////////////
function connexionBD2()
{
    // si l'année n'existe pas encore on met une valeur par défaut
    $annee = '2024_2025';

    // nom dynamique de la base
    $dbName = 'campuscoud';
    $user = 'root';
    $pw = '';

    $connexion2 = mysqli_connect('localhost', $user, $pw, $dbName) or
        die('Serveur inaccessible. Merci de reessayer plus tard.');

    return $connexion2;
}

function connexion_user()
{
    // si l'année n'existe pas encore on met une valeur par défaut
    $annee = '2024_2025';

    // nom dynamique de la base
    $dbName = 'campuscoud';
    $user = 'root';
    $pw = '';

    $connexion2 = mysqli_connect('localhost', $user, $pw, $dbName) or
        die('Serveur inaccessible. Merci de reessayer plus tard.');

    return $connexion2;
}

$connexion_user = connexion_user();

$connexion2 = connexionBD2();
// /////////////////////////////////////////////////////////////////////////////////

// ######## POUR DETERMINER LE MONTANT DE LA FACTURATION #########################
function getMontant($type)
{
    global $connexion;
    $sql = '
        SELECT DISTINCT(f.montant) 
        FROM codif_facturation f 
        WHERE f.indiv = ?;
    ';

    $stmt = $connexion->prepare($sql);
    $stmt->bind_param('i', $type);
    $stmt->execute();
    $result = $stmt->get_result();

    // Vérifier si des résultats ont été trouvés
    if ($row = $result->fetch_assoc()) {
        // Retourner le montant comme un entier
        return (int) $row['montant'];  // On cast ici pour forcer l'entier
    }

    $stmt->close();
    return 0;  // Retourner 0 si aucune donnée trouvée
}

/*
 * function getPaiementWithDateInterval_2($date_debut, $date_fin, $username, $libelle = "")
 * {
 *     global $connexion;
 *
 *     // Définir les valeurs par défaut
 *     $date_debut = !empty($date_debut) ? $date_debut : '2025-01-01'; // Date par défaut
 *     $date_fin = !empty($date_fin) ? $date_fin : date('Y-m-d'); // Aujourd'hui par défaut
 *
 *     // Construire la requête avec des conditions dynamiques
 *     $sql = "SELECT ce.num_etu, ce.nom, ce.prenoms, pc.dateTime_paie, pc.montant, pc.quittance, pc.num_ordre_user, pc.username_user, pc.libelle
 *             FROM codif_etudiant ce
 *             JOIN codif_affectation a ON ce.id_etu = a.id_etu
 *             JOIN codif_validation vl ON a.id_aff = vl.id_aff
 *             JOIN codif_paiement pc ON pc.id_val = vl.id_val
 *             WHERE pc.dateTime_paie >= ? AND pc.dateTime_paie <= ? ";
 *
 *     // Ajouter la condition pour `username_user` si le paramètre est fourni
 *     if (!empty($username)) {
 *         $sql .= " AND pc.username_user = ?";
 *     }
 *
 *     // Ajouter la condition pour `libelle` si le paramètre est fourni avec LIKE pour recherche par motif
 *     if (!empty($libelle)) {
 *         $sql .= " AND pc.libelle LIKE ?";
 *     }
 *
 *     $sql .= " ORDER by  pc.dateTime_paie desc, pc.username_user asc , pc.num_ordre_user desc ";
 *
 *     // Préparer la requête
 *     $stmt = $connexion->prepare($sql);
 *
 *     // Variables pour lier les paramètres
 *     if (!empty($libelle)) {
 *         $libelleParam = '%' . $libelle . '%';
 *     } else {
 *         $libelleParam = null;
 *     }
 *
 *     // Associer les paramètres dynamiquement
 *     if (!empty($username) && !empty($libelle)) {
 *         $stmt->bind_param("ssss", $date_debut, $date_fin, $username, $libelleParam);
 *     } elseif (!empty($username)) {
 *         $stmt->bind_param("sss", $date_debut, $date_fin, $username);
 *     } elseif (!empty($libelle)) {
 *         $stmt->bind_param("sss", $date_debut, $date_fin, $libelleParam);
 *     } else {
 *         $stmt->bind_param("ss", $date_debut, $date_fin);
 *     }
 *
 *     // Exécuter la requête
 *     $stmt->execute();
 *     $result = $stmt->get_result();
 *
 *     // Récupérer les résultats
 *     $data = [];
 *     while ($row = $result->fetch_assoc()) {
 *         $data[] = $row;
 *     }
 *
 *     // Calcul du montant total en fonction de si libelle est vide ou non
 *     if (empty($libelle)) {
 *         // Si libelle est vide, calculer la somme des montants
 *         $sqlTotal = "SELECT SUM(pc.montant) AS montantTotal
 *                      FROM codif_paiement pc
 *                      JOIN codif_validation vl ON pc.id_val = vl.id_val
 *                      WHERE pc.dateTime_paie >= ? AND pc.dateTime_paie <= ?";
 *
 *         // Ajouter la condition pour `username_user` si le paramètre est fourni
 *         if (!empty($username)) {
 *             $sqlTotal .= " AND pc.username_user = ?";
 *         }
 *
 *         // Ajouter la condition pour `libelle` si le paramètre est fourni
 *         if (!empty($libelle)) {
 *             $sqlTotal .= " AND pc.libelle LIKE ?";
 *         }
 *
 *         // Préparer la requête pour la somme des montants
 *         $stmtTotal = $connexion->prepare($sqlTotal);
 *
 *         // Associer les paramètres dynamiquement pour la somme totale
 *         if (!empty($username) && !empty($libelle)) {
 *             $stmtTotal->bind_param("ssss", $date_debut, $date_fin, $username, $libelleParam);
 *         } elseif (!empty($username)) {
 *             $stmtTotal->bind_param("sss", $date_debut, $date_fin, $username);
 *         } elseif (!empty($libelle)) {
 *             $stmtTotal->bind_param("sss", $date_debut, $date_fin, $libelleParam);
 *         } else {
 *             $stmtTotal->bind_param("ss", $date_debut, $date_fin);
 *         }
 *
 *         // Exécuter la requête de somme des montants
 *         $stmtTotal->execute();
 *         $resultTotal = $stmtTotal->get_result();
 *
 *         // Calcul du montant total
 *         $totalMontant = 0;
 *         if ($rowTotal = $resultTotal->fetch_assoc()) {
 *             $totalMontant = $rowTotal['montantTotal']; // Montant total
 *         }
 *     } else {
 *         // Si libelle est fourni, compter le nombre de paiements
 *         $sqlTotal = "SELECT COUNT(pc.montant) AS countPayments
 *                      FROM codif_paiement pc
 *                      JOIN codif_validation vl ON pc.id_val = vl.id_val
 *                      WHERE pc.dateTime_paie >= ? AND pc.dateTime_paie <= ?";
 *
 *         // Ajouter la condition pour `username_user` si le paramètre est fourni
 *         if (!empty($username)) {
 *             $sqlTotal .= " AND pc.username_user = ?";
 *         }
 *
 *         // Ajouter la condition pour `libelle` si le paramètre est fourni
 *         if (!empty($libelle)) {
 *             $sqlTotal .= " AND pc.libelle LIKE ?";
 *         }
 *
 *         // Préparer la requête pour le comptage
 *         $stmtTotal = $connexion->prepare($sqlTotal);
 *
 *         // Associer les paramètres dynamiquement pour le comptage
 *         if (!empty($username) && !empty($libelle)) {
 *             $stmtTotal->bind_param("ssss", $date_debut, $date_fin, $username, $libelleParam);
 *         } elseif (!empty($username)) {
 *             $stmtTotal->bind_param("sss", $date_debut, $date_fin, $username);
 *         } elseif (!empty($libelle)) {
 *             $stmtTotal->bind_param("sss", $date_debut, $date_fin, $libelleParam);
 *         } else {
 *             $stmtTotal->bind_param("ss", $date_debut, $date_fin);
 *         }
 *
 *         // Exécuter la requête de comptage
 *         $stmtTotal->execute();
 *         $resultTotal = $stmtTotal->get_result();
 *
 *         // Calcul du montant total basé sur le comptage
 *         $totalMontant = 0;
 *         if ($rowTotal = $resultTotal->fetch_assoc()) {
 *             $totalMontant = $rowTotal['countPayments'] * 5000; // Montant total = nombre de paiements * 5000
 *         }
 *     }
 *
 *     // Retourner les données et la somme totale
 *     return ['data' => $data, 'totalMontant' => $totalMontant];
 * }
 */

function getPaiementWithDateInterval_2_old($date_debut, $date_fin, $username, $libelle = '')
{
    global $connexion;

    // Sécuriser les entrées
    $date_debut = !empty($date_debut) ? mysqli_real_escape_string($connexion, $date_debut) : '2025-01-01';
    $date_fin = !empty($date_fin) ? mysqli_real_escape_string($connexion, $date_fin) : date('Y-m-d');
    $username = !empty($username) ? mysqli_real_escape_string($connexion, $username) : '';
    $libelleFilter = $libelle;  // Sauvegarde de la valeur brute
    $libelle = !empty($libelle) ? '%' . mysqli_real_escape_string($connexion, $libelle) . '%' : '';

    // Construire la requête SQL
    $sql = "SELECT ce.num_etu, ce.nom, ce.prenoms, pc.id_paie, pc.dateTime_paie, pc.montant, pc.an, 
                   pc.id_val, pc.quittance, pc.username_user, pc.libelle 
            FROM codif_etudiant ce 
            JOIN codif_affectation a ON ce.id_etu = a.id_etu 
            JOIN codif_validation vl ON a.id_aff = vl.id_aff 
            JOIN codif_paiement pc ON pc.id_val = vl.id_val 
            WHERE pc.dateTime_paie >= '$date_debut' AND pc.dateTime_paie <= '$date_fin'";

    if (!empty($username)) {
        $sql .= " AND pc.username_user = '$username'";
    }

    if (!empty($libelle)) {
        if ($libelleFilter === 'LOYER') {
            $sql .= " AND pc.libelle != 'CAUTION'";
        } else {
            $sql .= " AND pc.libelle LIKE '$libelle'";
        }
    }

    $sql .= ' ORDER BY pc.dateTime_paie DESC, pc.quittance DESC, ce.nom ASC';

    // Exécuter la requête
    $result = $connexion->query($sql);
    $data = [];

    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    // Initialisation du montant total
    $totalMontant = 0;

    // Calcul du montant total
    if (empty($libelleFilter)) {
        $sqlTotal = "SELECT SUM(pc.montant) AS montantTotal 
                     FROM codif_paiement pc
                     JOIN codif_validation vl ON pc.id_val = vl.id_val
                     WHERE pc.dateTime_paie >= '$date_debut' AND pc.dateTime_paie <= '$date_fin'";

        if (!empty($username)) {
            $sqlTotal .= " AND pc.username_user = '$username'";
        }
    } elseif ($libelleFilter === 'CAUTION') {
        $sqlTotal = "SELECT COUNT(pc.montant) AS countPayments 
                     FROM codif_paiement pc
                     JOIN codif_validation vl ON pc.id_val = vl.id_val
                     WHERE pc.dateTime_paie >= '$date_debut' AND pc.dateTime_paie <= '$date_fin'";

        if (!empty($username)) {
            $sqlTotal .= " AND pc.username_user = '$username'";
        }

        $sqlTotal .= " AND pc.libelle LIKE '%CAUTION%'";
    } elseif ($libelleFilter === 'LOYER') {
        $sqlTotal = "SELECT SUM(
                    CASE 
                    WHEN pc.libelle LIKE '%CAUTION%' 
                    THEN pc.montant - 5000 
                    ELSE pc.montant 
                    END
                    ) AS montantTotal
                     FROM codif_paiement pc
                     JOIN codif_validation vl ON pc.id_val = vl.id_val
                     WHERE pc.dateTime_paie >= '$date_debut' AND pc.dateTime_paie <= '$date_fin'";

        if (!empty($username)) {
            $sqlTotal .= " AND pc.username_user = '$username'";
        }

        $sqlTotal .= " AND pc.libelle NOT LIKE 'CAUTION'";
    }

    // Exécuter la requête pour le montant total
    $resultTotal = $connexion->query($sqlTotal);

    if ($rowTotal = $resultTotal->fetch_assoc()) {
        $totalMontant = isset($rowTotal['montantTotal'])
            ? $rowTotal['montantTotal']
            : (isset($rowTotal['countPayments']) ? $rowTotal['countPayments'] * 5000 : 0);
    }

    return [
        'data' => $data,
        'totalMontant' => $totalMontant
    ];
}

function getPaiementWithDateInterval_2($date_debut, $date_fin, $username, $libelle = '')
{
    global $connexion;

    $date_debut = !empty($date_debut)
        ? mysqli_real_escape_string($connexion, $date_debut)
        : '2025-01-01';

    $date_fin = !empty($date_fin)
        ? mysqli_real_escape_string($connexion, $date_fin)
        : date('Y-m-d');

    $username = !empty($username)
        ? mysqli_real_escape_string($connexion, $username)
        : '';

    $libelleFilter = $libelle;

    $libelle = !empty($libelle)
        ? '%' . mysqli_real_escape_string($connexion, $libelle) . '%'
        : '';

    $baseQuery = "
    (
        SELECT
            ce.num_etu,
            ce.nom,
            ce.prenoms,
            pc.id_paie,
            pc.dateTime_paie,
            pc.montant,
            pc.an,
            pc.id_val,
            pc.quittance,
            pc.username_user,
            pc.libelle
        FROM codif_etudiant ce
        INNER JOIN codif_affectation a
            ON ce.id_etu = a.id_etu
        INNER JOIN codif_validation vl
            ON a.id_aff = vl.id_aff
        INNER JOIN codif_paiement pc
            ON pc.id_val = vl.id_val

        UNION ALL

        SELECT
            ce.num_etu,
            ce.nom,
            ce.prenoms,
            pc.id_paie,
            pc.dateTime_paie,
            pc.montant,
            pc.an,
            pc.id_val,
            pc.quittance,
            pc.username_user,
            pc.libelle
        FROM codif_etudiant ce
        INNER JOIN codif_paiement pc
            ON pc.id_etu = ce.id_etu
        WHERE pc.id_val IS NULL
           OR pc.id_val = 0
    ) p
    ";

    // =====================
    // LISTE DES PAIEMENTS
    // =====================

    $sql = "
    SELECT *
    FROM $baseQuery
    WHERE dateTime_paie >= '$date_debut'
      AND dateTime_paie <= '$date_fin'
    ";

    if (!empty($username)) {
        $sql .= " AND username_user = '$username'";
    }

    if (!empty($libelle)) {
        if ($libelleFilter === 'LOYER') {
            $sql .= " AND libelle <> 'CAUTION'";
        } else {
            $sql .= " AND libelle LIKE '$libelle'";
        }
    }

    $sql .= "
    ORDER BY
        dateTime_paie DESC,
        quittance DESC,
        nom ASC
    ";

    $result = $connexion->query($sql);

    $data = [];

    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    // =====================
    // TOTAL MONTANT
    // =====================

    $totalMontant = 0;

    if (empty($libelleFilter)) {

        $sqlTotal = "
        SELECT SUM(montant) AS montantTotal
        FROM $baseQuery
        WHERE dateTime_paie >= '$date_debut'
          AND dateTime_paie <= '$date_fin'
        ";

        if (!empty($username)) {
            $sqlTotal .= " AND username_user = '$username'";
        }

    } elseif ($libelleFilter === 'CAUTION') {

        $sqlTotal = "
        SELECT COUNT(*) AS countPayments
        FROM $baseQuery
        WHERE dateTime_paie >= '$date_debut'
          AND dateTime_paie <= '$date_fin'
        ";

        if (!empty($username)) {
            $sqlTotal .= " AND username_user = '$username'";
        }

        $sqlTotal .= " AND libelle LIKE '%CAUTION%'";

    } elseif ($libelleFilter === 'LOYER') {

        $sqlTotal = "
        SELECT
            SUM(
                CASE
                    WHEN libelle LIKE '%CAUTION%'
                    THEN montant - 5000
                    ELSE montant
                END
            ) AS montantTotal
        FROM $baseQuery
        WHERE dateTime_paie >= '$date_debut'
          AND dateTime_paie <= '$date_fin'
        ";

        if (!empty($username)) {
            $sqlTotal .= " AND username_user = '$username'";
        }

        $sqlTotal .= " AND libelle <> 'CAUTION'";
    }

    $resultTotal = $connexion->query($sqlTotal);

    if ($rowTotal = $resultTotal->fetch_assoc()) {
        $totalMontant = isset($rowTotal['montantTotal'])
            ? $rowTotal['montantTotal']
            : (
                isset($rowTotal['countPayments'])
                    ? $rowTotal['countPayments'] * 5000
                    : 0
            );
    }

    return [
        'data' => $data,
        'totalMontant' => $totalMontant
    ];
}

function verifajoutquota($niveau, $sexe)
{
    global $connexion;
    $query = '
        SELECT COUNT(*) AS total
        FROM codif_affectation a 
        JOIN codif_etudiant e ON a.id_etu = e.id_etu
        WHERE e.niveauFormation = ? and e.sexe = ?
    ';

    $stmt = $connexion->prepare($query);
    $stmt->bind_param('ss', $niveau, $sexe);
    $stmt->execute();

    // Récupérer le résultat
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $stmt->close();
    return $row['total'] > 0;
}

function maxIdEtu()
{
    global $connexion;
    $query = '
        SELECT max(id_etu) AS max
        FROM codif_etudiant';

    $stmt = $connexion->prepare($query);
    $stmt->execute();

    // Récupérer le résultat
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    $max = $row['max'] + 1;

    return $max;
}

function verifierDemarrage($niveauEtudiant, $sexe)
{
    global $connexion;
    $sql = 'SELECT COUNT(*) AS total FROM codif_demarre_choix WHERE niveauFormation = ? and sexe = ?';
    $stmt = $connexion->prepare($sql);
    $stmt->bind_param('ss', $niveauEtudiant, $sexe);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if ($result['total'] == 0) {
?>
<script langage='javascript'>
alert('La codification n’est pas encore ouverte pour votre formation!')
window.history.back();
</script>
<?php

        exit();
    }
}

function modifierPaiement($connexion, $id_paie, $new_montant, $new_libelle, $modified_by)
{
    // Échapper les variables pour éviter les injections SQL
    $id_paie = mysqli_real_escape_string($connexion, $id_paie);
    $new_montant = mysqli_real_escape_string($connexion, $new_montant);
    $new_libelle = mysqli_real_escape_string($connexion, $new_libelle);
    $modified_by = mysqli_real_escape_string($connexion, $modified_by);

    // Étape 1 : Récupérer les données actuelles avant modification
    $sqlSelect = "SELECT * FROM codif_paiement WHERE id_paie = $id_paie";
    $result = mysqli_query($connexion, $sqlSelect);

    if ($row = mysqli_fetch_assoc($result)) {
        // Échapper les valeurs récupérées
        $id_val = mysqli_real_escape_string($connexion, $row['id_val']);
        $montant = mysqli_real_escape_string($connexion, $row['montant']);
        $libelle = mysqli_real_escape_string($connexion, $row['libelle']);
        $username_user = mysqli_real_escape_string($connexion, $row['username_user']);
        $quittance = mysqli_real_escape_string($connexion, $row['quittance']);
        $an = mysqli_real_escape_string($connexion, $row['an']);
        $dateTime_paie = mysqli_real_escape_string($connexion, $row['dateTime_paie']);

        // Étape 2 : Insérer les anciennes données dans l'archive
        $sqlInsert = "INSERT INTO codif_archiv_acp 
                     (id_paie, id_val, montant, libelle, username_user, quittance, an, dateTime_paie, deleted_at, deleted_by)
                      VALUES ($id_paie, '$id_val', '$montant', '$libelle', '$username_user', '$quittance', '$an', '$dateTime_paie', NOW(), '$modified_by')";
        mysqli_query($connexion, $sqlInsert);

        // Étape 3 : Mettre à jour les nouvelles valeurs
        $sqlUpdate = "UPDATE codif_paiement SET montant = '$new_montant', libelle = '$new_libelle' WHERE id_paie = $id_paie";
        $resultUpdate = mysqli_query($connexion, $sqlUpdate);

        return $resultUpdate ? 'Modification enregistrée avec succès.' : 'Erreur lors de la mise à jour.';
    } else {
        return 'Erreur : Paiement introuvable.';
    }
}

// Fonction pour envoyer Rappel et mettre à jour la base de données
function rappel($message, $etudiant_id, $connexion)
{
    // Mettre à jour l'attribut 'rappel_envoye' pour cet étudiant dans la table 'affectation'
    $query = 'UPDATE codif_affectation SET rappel_envoye = NOW() WHERE id_etu = ?';
    $stmt = $connexion->prepare($query);
    $stmt->bind_param('i', $etudiant_id);  // "i" pour integer
    $stmt->execute();

    // Vérifier si la mise à jour a été effectuée avec succès
    if ($stmt->affected_rows > 0) {
        // Afficher l'alerte en JavaScript
        echo "<script type='text/javascript'>alert('$message');</script>";
    } else {
        echo "<script type='text/javascript'>alert('Erreur lors de la mise à jour de la base de données.');</script>";
    }
}

// ############ FONCTION POUR RECUPERER LES TITULAIRES ##############################
function getTitulaireByPavillon($pavillon, $connexion)
{
    $sql = "
        SELECT 
            l.pavillon,
            l.chambre,
            l.lit,
            e.id_etu AS etudiant_id,
            e.num_etu AS num_etu,
\t\t\te.telephone AS telephone,
            lg.id_paie AS id_paie,
            CONCAT(e.nom, ' ', e.prenoms) AS titulaire_nom
        FROM 
            codif_lit l
        JOIN 
            codif_affectation a ON l.id_lit = a.id_lit
        JOIN 
            codif_etudiant e ON a.id_etu = e.id_etu
        LEFT JOIN 
            codif_loger lg ON lg.id_etu = e.id_etu
        WHERE 
            l.pavillon = ?
            AND lg.statut = 'Attributaire'
        GROUP BY 
            l.pavillon, l.chambre, l.lit, e.id_etu
        ORDER BY 
        -- Trier par la partie avant la parenthèse dans le pavillon (si présent), sinon utiliser directement la lettre
        CAST(SUBSTRING_INDEX(l.pavillon, '(', 1) AS UNSIGNED), 
        
        -- Trier par la partie entre parenthèses, si elle existe
        IF(LOCATE('(', l.pavillon) > 0,  -- Si une parenthèse existe
            SUBSTRING(l.pavillon, LOCATE('(', l.pavillon) + 1, LOCATE(')', l.pavillon) - LOCATE('(', l.pavillon) - 1), 
            ''  -- Sinon, une chaîne vide
        ),
        
        -- Trier par chambre
        CAST(SUBSTRING_INDEX(l.chambre, '(', 1) AS UNSIGNED),  
        IF(LOCATE('(', l.chambre) > 0, 
            SUBSTRING(l.chambre, LOCATE('(', l.chambre) + 1, LOCATE(')', l.chambre) - LOCATE('(', l.chambre) - 1), 
            ''  -- Sinon, une chaîne vide
        ),
        
        -- Trier par lit
        CAST(SUBSTRING_INDEX(l.lit, '_', -1) AS UNSIGNED) ;
    ";

    $stmt = $connexion->prepare($sql);
    $stmt->bind_param('s', $pavillon);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    $stmt->close();
    return $data;
}

// ############ FONCTION POUR RECUPERER LES TITULAIRES ##############################
function getTitulaireByPavillon_nonLoger($pavillon, $connexion)
{
    $sql = "
        SELECT 
            l.pavillon,
            l.chambre,
            l.lit,
            e.id_etu AS etudiant_id,
            e.num_etu AS num_etu,
\t\t\te.telephone AS telephone,
            CONCAT(e.nom, ' ', e.prenoms) AS titulaire_nom
        FROM 
            codif_lit l
        JOIN 
            codif_affectation a ON l.id_lit = a.id_lit
        JOIN 
            codif_etudiant e ON a.id_etu = e.id_etu
        WHERE 
            l.pavillon = ?
             and e.id_etu not in(select id_etu from codif_loger)
        GROUP BY 
            l.pavillon, l.chambre, l.lit, e.id_etu
        ORDER BY 
        -- Trier par la partie avant la parenthèse dans le pavillon (si présent), sinon utiliser directement la lettre
        CAST(SUBSTRING_INDEX(l.pavillon, '(', 1) AS UNSIGNED), 
        
        -- Trier par la partie entre parenthèses, si elle existe
        IF(LOCATE('(', l.pavillon) > 0,  -- Si une parenthèse existe
            SUBSTRING(l.pavillon, LOCATE('(', l.pavillon) + 1, LOCATE(')', l.pavillon) - LOCATE('(', l.pavillon) - 1), 
            ''  -- Sinon, une chaîne vide
        ),
        
        -- Trier par chambre
        CAST(SUBSTRING_INDEX(l.chambre, '(', 1) AS UNSIGNED),  
        IF(LOCATE('(', l.chambre) > 0, 
            SUBSTRING(l.chambre, LOCATE('(', l.chambre) + 1, LOCATE(')', l.chambre) - LOCATE('(', l.chambre) - 1), 
            ''  -- Sinon, une chaîne vide
        ),
        
        -- Trier par lit
        CAST(SUBSTRING_INDEX(l.lit, '_', -1) AS UNSIGNED) ;
    ";

    $stmt = $connexion->prepare($sql);
    $stmt->bind_param('s', $pavillon);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    $stmt->close();
    return $data;
}

// ###############    FONCTION POUR RECUPERER LES TITULAIRES ET SES VOISINS #######################3
function getEtudiantByLit($lit, $paie, $connexion)
{
    $sql = "
        SELECT 
            e.id_etu AS etudiant_id,
            e.num_etu AS num_etu,
            e.nom,
            e.prenoms,
\t\t\te.telephone,
            lg.statut AS statut_etudiant
        FROM 
            codif_lit l
        RIGHT JOIN 
            codif_affectation a ON l.id_lit = a.id_lit
        RIGHT JOIN 
            codif_etudiant e ON a.id_etu = e.id_etu
        LEFT JOIN 
            codif_loger lg ON lg.id_etu = e.id_etu
        WHERE 
            (l.lit = ? and lg.id_etu IS not NULL)    
            OR lg.id_paie IN (
                SELECT id_paie
                FROM codif_loger
                WHERE id_paie = ?
                  AND statut = 'Attributaire'
            )
        ORDER BY 
            FIELD(lg.statut, 'Attributaire', 'Suppleant(e)', 'Clando');
    ";
    // and lg.id_etu IS not NULL)
    $stmt = $connexion->prepare($sql);
    $stmt->bind_param('si', $lit, $paie);  // `s` pour une chaîne de caractères
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    $stmt->close();
    return $data;
}

function getPaymentDetailsByPavillon($pavillonDonne, $connexion)
{
    $sql = "
   SELECT 
    l.pavillon,
    l.chambre,
    l.lit,
    e.id_etu AS etudiant_id,
    e.num_etu AS num_etu,
    e.nom AS etudiant_nom,
    e.prenoms AS etudiant_prenoms,
    l.indiv AS type_chambre,
    lg.id_log AS log_id,
    lg.id_val AS validation_id,
    lg.id_paie AS paiement_id,
    lg.username_user AS utilisateur,
    a.rappel_envoye,
    lg.datetime_loger AS date_log,
    COALESCE(
        (SELECT SUM(p.montant)
         FROM codif_paiement p
         WHERE p.id_val = v.id_val), 0) AS montant_paye_total
FROM 
    codif_lit l
JOIN 
    codif_affectation a ON l.id_lit = a.id_lit
JOIN 
    codif_etudiant e ON a.id_etu = e.id_etu
JOIN 
    codif_validation v ON a.id_aff = v.id_aff
LEFT JOIN 
    codif_loger lg ON lg.id_etu = e.id_etu  
WHERE 
  
    l.pavillon = '$pavillonDonne' and (a.statut='Attributaire' or a.id_aff IS NULL )
GROUP BY 
    l.pavillon, l.chambre, l.lit, e.id_etu, lg.id_log
ORDER BY 
        -- Trier par la partie avant la parenthèse dans le pavillon (si présent), sinon utiliser directement la lettre
        CAST(SUBSTRING_INDEX(l.pavillon, '(', 1) AS UNSIGNED), 
        
        -- Trier par la partie entre parenthèses, si elle existe
        IF(LOCATE('(', l.pavillon) > 0,  -- Si une parenthèse existe
            SUBSTRING(l.pavillon, LOCATE('(', l.pavillon) + 1, LOCATE(')', l.pavillon) - LOCATE('(', l.pavillon) - 1), 
            ''  -- Sinon, une chaîne vide
        ),
        
        -- Trier par chambre
        CAST(SUBSTRING_INDEX(l.chambre, '(', 1) AS UNSIGNED),  
        IF(LOCATE('(', l.chambre) > 0, 
            SUBSTRING(l.chambre, LOCATE('(', l.chambre) + 1, LOCATE(')', l.chambre) - LOCATE('(', l.chambre) - 1), 
            ''  -- Sinon, une chaîne vide
        ),
        
        -- Trier par lit
        CAST(SUBSTRING_INDEX(l.lit, '_', -1) AS UNSIGNED) ;

";
    // and lg.statut='Attributaire')
    $stmt = $connexion->prepare($sql);
    // $stmt->bind_param("s", $pavillonDonne);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $etudiantId = $row['etudiant_id'];
        $etudiant_num = $row['num_etu'];

        // Calculer le nombre de mois pour l'étudiant
        $nombreMois = getNbreMois2($etudiant_num);

        // Déterminer le prix du lit en fonction du type de chambre
        // $prixLit = ($row['type_chambre'] === 1) ? 4000 : 3000;
        $prixLit = getMontant($row['type_chambre']);

        // Calculer le montant facturé en fonction du nombre de mois et Ajouter la caution
        if (verifCaution($etudiantId)) {
            $montantFacture = ($nombreMois * $prixLit) + 5000;
        } else {
            $montantFacture = ($nombreMois * $prixLit);
        }

        // Vérifier que le montant payé n'est pas vide
        $montantPaye = isset($row['montant_paye_total']) ? $row['montant_paye_total'] : 0;

        // Calculer le reste à payer
        $resteAPayer = $montantFacture - $montantPaye;

        // Ajouter les informations uniquement si le reste à payer est supérieur à zéro
        // if ($resteAPayer > 0) {
        $data[] = [
            'pavillon' => $row['pavillon'],
            'chambre' => $row['chambre'],
            'lit' => $row['lit'],
            'etudiant_id' => $row['etudiant_id'],
            'etudiant_nom' => $row['etudiant_nom'],
            'etudiant_prenoms' => $row['etudiant_prenoms'],
            'num_etu' => $row['num_etu'],
            'montant_facture' => $montantFacture,
            'montant_paye' => $montantPaye,
            'reste_a_payer' => $resteAPayer,
            'log_id' => $row['log_id'],
            'validation_id' => $row['validation_id'],
            'paiement_id' => $row['paiement_id'],
            'utilisateur' => $row['utilisateur'],
            'rappel_envoye' => $row['rappel_envoye'],
            'date_log' => $row['date_log']
        ];
        // }
    }

    $stmt->close();
    return $data;
}

function getPaymentDetailsByPavillon_r7($pavillonDonne, $connexion, $dateDebut = null, $dateFin = null)
{
    // Construction de la condition de date si les paramètres sont fournis
    $dateCondition = '';
    if ($dateDebut && $dateFin) {
        $dateCondition = "AND p.dateTime_paie BETWEEN '$dateDebut' AND '$dateFin'";
    } elseif ($dateDebut) {
        $dateCondition = "AND p.dateTime_paie >= '$dateDebut'";
    } elseif ($dateFin) {
        $dateCondition = "AND p.dateTime_paie <= '$dateFin'";
    }

    $sql = "
    SELECT 
        l.pavillon,
        l.chambre,
        l.lit,
        e.id_etu AS etudiant_id,
        e.num_etu AS num_etu,
        e.nom AS etudiant_nom,
        e.prenoms AS etudiant_prenoms,
        l.indiv AS type_chambre,
        lg.id_log AS log_id,
        lg.id_val AS validation_id,
        lg.id_paie AS paiement_id,
        lg.username_user AS utilisateur,
        a.rappel_envoye,
        lg.datetime_loger AS date_log,
        COALESCE(
            (SELECT SUM(p.montant)
             FROM codif_paiement p
             WHERE p.id_val = v.id_val $dateCondition), 0) AS montant_paye_total,
        COALESCE(
    (SELECT 
        CASE 
            WHEN EXISTS (
                SELECT 1 FROM codif_paiement p 
                WHERE p.id_val = v.id_val $dateCondition
                AND (p.libelle LIKE '%CAUTION%' OR p.libelle LIKE '%caution%')
            ) THEN 5000 
            ELSE 0 
        END
    ), 0) AS caution_payee,
        COALESCE(
            (SELECT SUM(CASE WHEN p.libelle NOT LIKE '%CAUTION%' THEN p.montant ELSE 0 END)
             FROM codif_paiement p
             WHERE p.id_val = v.id_val $dateCondition), 0) AS loyer_paye
    FROM 
        codif_lit l
    JOIN 
        codif_affectation a ON l.id_lit = a.id_lit
    JOIN 
        codif_etudiant e ON a.id_etu = e.id_etu
    JOIN 
        codif_validation v ON a.id_aff = v.id_aff
    LEFT JOIN 
        codif_loger lg ON lg.id_etu = e.id_etu  
    WHERE 
       
       l.pavillon = '$pavillonDonne' and (a.statut='Attributaire' or a.id_aff IS NULL )
    GROUP BY 
        l.pavillon, l.chambre, l.lit, e.id_etu, lg.id_log
    ORDER BY 
        CAST(SUBSTRING_INDEX(l.pavillon, '(', 1) AS UNSIGNED), 
        IF(LOCATE('(', l.pavillon) > 0, 
            SUBSTRING(l.pavillon, LOCATE('(', l.pavillon) + 1, LOCATE(')', l.pavillon) - LOCATE('(', l.pavillon) - 1), 
            ''
        ),
        CAST(SUBSTRING_INDEX(l.chambre, '(', 1) AS UNSIGNED),  
        IF(LOCATE('(', l.chambre) > 0, 
            SUBSTRING(l.chambre, LOCATE('(', l.chambre) + 1, LOCATE(')', l.chambre) - LOCATE('(', l.chambre) - 1), 
            ''
        ),
        CAST(SUBSTRING_INDEX(l.lit, '_', -1) AS UNSIGNED)";

    $stmt = $connexion->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $etudiantId = $row['etudiant_id'];
        $etudiant_num = $row['num_etu'];

        // Calculer le nombre de mois pour l'étudiant
        $nombreMois = getNbreMois2($etudiant_num);

        // Déterminer le prix du lit
        $prixLit = getMontant($row['type_chambre']);

        // Calculer le montant facturé
        $montantLoyerFacture = ($nombreMois * $prixLit);
        $montantCautionFacture = verifCaution($etudiantId) ? 5000 : 0;
        $montantFactureTotal = $montantLoyerFacture + $montantCautionFacture;

        // Récupérer les montants payés
        $montantPayeTotal = $row['montant_paye_total'] ?? 0;
        $cautionPayee = $row['caution_payee'] ?? 0;
        $loyerPaye = $montantPayeTotal - $cautionPayee;

        // Calculer les restes à payer
        $resteLoyer = $montantLoyerFacture - $loyerPaye;
        $resteCaution = max(0, $montantCautionFacture - $cautionPayee);
        $resteAPayerTotal = $resteLoyer + $resteCaution;

        $data[] = [
            'pavillon' => $row['pavillon'],
            'chambre' => $row['chambre'],
            'lit' => $row['lit'],
            'etudiant_id' => $row['etudiant_id'],
            'etudiant_nom' => $row['etudiant_nom'],
            'etudiant_prenoms' => $row['etudiant_prenoms'],
            'num_etu' => $row['num_etu'],
            'type_chambre' => $row['type_chambre'],
            'montant_facture_total' => $montantFactureTotal,
            'montant_loyer_facture' => $montantLoyerFacture,
            'montant_caution_facture' => $montantCautionFacture,
            'montant_paye_total' => $montantPayeTotal,
            'loyer_paye' => $loyerPaye,
            'caution_payee' => $cautionPayee,
            'reste_loyer' => $resteLoyer,
            'reste_caution' => $resteCaution,
            'reste_a_payer_total' => $resteAPayerTotal,
            'log_id' => $row['log_id'],
            'validation_id' => $row['validation_id'],
            'paiement_id' => $row['paiement_id'],
            'utilisateur' => $row['utilisateur'],
            'rappel_envoye' => $row['rappel_envoye'],
            'date_log' => $row['date_log']
        ];
    }

    $stmt->close();
    return $data;
}

/*
 * function getPaymentDetailsByPavillon($pavillonDonne, $connexion) {
 *     $sql = "
 *         SELECT
 *             l.pavillon,
 *             l.chambre,
 *             l.lit,
 *             e.id_etu AS etudiant_id,
 *             e.num_etu AS num_etu,
 *             e.nom AS etudiant_nom,
 *             e.prenoms AS etudiant_prenoms,
 *             l.indiv AS type_chambre,
 *             lg.id_log AS log_id,
 *             lg.id_val AS validation_id,
 *             lg.id_paie AS paiement_id,
 *             lg.username_user AS utilisateur,
 *             a.rappel_envoye,
 *             lg.datetime_loger AS date_log,
 *             COALESCE(SUM(p.montant), 0) AS montant_paye_total
 *         FROM
 *             codif_lit l
 *         JOIN
 *             codif_affectation a ON l.id_lit = a.id_lit
 *         JOIN
 *             codif_etudiant e ON a.id_etu = e.id_etu
 *         JOIN
 *             codif_validation v ON a.id_aff = v.id_aff
 *         LEFT JOIN
 *             codif_paiement p ON p.id_val = v.id_val
 *         JOIN
 *             codif_loger lg ON lg.id_val = p.id_val
 *         WHERE
 *             l.pavillon = ?
 *         GROUP BY
 *             l.pavillon, l.chambre, l.lit, e.id_etu
 *         ORDER BY
 *             l.pavillon, l.chambre, l.lit, e.id_etu, lg.datetime_loger DESC;
 *     ";
 *
 *     $stmt = $connexion->prepare($sql);
 *     $stmt->bind_param("s", $pavillonDonne);
 *     $stmt->execute();
 *     $result = $stmt->get_result();
 *
 *     $data = [];
 *     while ($row = $result->fetch_assoc()) {
 *         $etudiantId = $row['etudiant_id'];
 *         $etudiant_num = $row['num_etu'];
 *
 *         // Calculer le nombre de mois pour l'étudiant
 *         $nombreMois = getNbreMois2($etudiant_num);
 *
 *         // Déterminer le prix du lit en fonction du type de chambre
 *         $prixLit = ($row['type_chambre'] === 1) ? 4000 : 3000;
 *
 *         // Calculer le montant facturé en fonction du nombre de mois
 *         $montantFacture = $nombreMois * $prixLit;
 *
 *         // Vérifier que le montant payé n'est pas vide
 *         $montantPaye = isset($row['montant_paye_total']) ? $row['montant_paye_total'] : 0;
 *
 *         // Calculer le reste à payer
 *         $resteAPayer = $montantFacture - $montantPaye;
 *
 *         // Ajouter les informations au tableau de résultats
 *         $data[] = [
 *             'pavillon' => $row['pavillon'],
 *             'chambre' => $row['chambre'],
 *             'lit' => $row['lit'],
 *             'etudiant_id' => $row['etudiant_id'],
 *             'etudiant_nom' => $row['etudiant_nom'],
 *             'etudiant_prenoms' => $row['etudiant_prenoms'],
 *             'num_etu' => $row['num_etu'],
 *             'montant_facture' => $montantFacture,
 *             'montant_paye' => $montantPaye,
 *             'reste_a_payer' => $resteAPayer,
 *             'log_id' => $row['log_id'],
 *             'validation_id' => $row['validation_id'],
 *             'paiement_id' => $row['paiement_id'],
 *             'utilisateur' => $row['utilisateur'],
 *             'rappel_envoye'  => $row['rappel_envoye'],
 *             'date_log' => $row['date_log']
 *         ];
 *     }
 *
 *     $stmt->close();
 *     return $data;
 * }
 */

/*
 * function connexionBD()
 * {
 *     $connexion = mysqli_connect("localhost", "u893234126_campuscoud_us2", "Passcampuscoud_Hostinger_2024", "u893234126_campuscoud_bd2");
 *     // Vérifiez la connexion
 *     if ($connexion === false) {
 *         die("Erreur : Impossible de se connecter. " . mysqli_connect_error());
 *     }
 *     return $connexion;
 * }
 * $connexion = connexionBD();
 */

function deconnexion($link)
{
    mysqli_close($link);
}

/*
 * Fonction pour recuperer téléphone étudiant via API de DISI
 * ********************************************************************************
 */

// ESSAYER DE LE STOCKER EN LOCAL EST + SUR

function getTelephoneEtudiant($num_etu)
{
    global $connexion;
    $telephone0 = '0';
    $telephone = '';

    // try
    // {
    /*$json_url = "https://coud@ucad.sn:dhHNg4VmpfZYR6Q@coudservice.ucad.sn/api/etudiant/$num_etu";
    $json = file_get_contents($json_url);
    $data = json_decode($json);
    $telephone= $data[0]->telephone; */
    // }
    /*catch (Exception $e) {
       // echo 'Caught exception: ',  $e->getMessage(), "\n";
    }*/

    $rt = "select telephone from codif_etudiant where num_etu='$num_etu'";
    $er = mysqli_query($connexion, $rt);
    $st = mysqli_fetch_assoc($er);
    $telephone = $st['telephone'];

    if ($telephone == NULL) {
        return $telephone0;
    } else {
        return $telephone;
    }
}

/********/
function generer_mdp()
{
    // $nbChar=
    return substr(str_shuffle('123456789'), 1, 4);
}

/*****/

// Verifier le type de mdp s'il est updated ou default pour etre redirigé   [ETUDIANT]
function verif_type_mdp($login)
{
    $type_mdp = info2($login)['1'];
    if ($type_mdp == 'default') {
?>
<script langage='javascript'>
alert('Connexion reussie: veuillez a present changer votre mot de passe (par default) pour plus de securite!');
</script>
<?php
        echo '<meta http-equiv="refresh" content="0;URL=mp">';
        exit();
    }
}

// /////////////////////////////////////////////////////////////////////////////////////////////////

// Verifier le type de mdp s'il est updated ou default pour etre redirigé   [AGENT]
function verif_type_mdp_2($login)
{
    $type_mdp = info2($login)['1'];
    if ($type_mdp == 'default') {
?>
<script langage='javascript'>
alert('Connexion reussie: veuillez a present changer votre mot de passe (par default) pour plus de securite!');
</script>
<?php
        echo '<meta http-equiv="refresh" content="0;URL=../mp">';
        exit();
    }
}

// /////////////////////////////////////////////////////////////////////////////////////////////////

// Fonction pour Envoi Msg aux etudiants ayant eu un compte dans le passé et n'etant pas dans la base actuelle
function ancien_eligible($login)
{
    global $connexion_user;
    $rr = "select username_user from codif_user where username_user='$login' and profil_user='user' and username_user not in (select num_etu from codif_etudiant)";
    $ee = mysqli_query($connexion_user, $rr);
    $ss = mysqli_num_rows($ee);

    if ($ss) {
?>
<script langage='javascript'>
alert('Acces non autorisé pour lannee academique choisie!');
</script>
<?php
        echo '<meta http-equiv="refresh" content="0;URL=https://campuscoud.com/">';
        exit();
    }
}

function ancien_eligible_2($login)
{
    global $connexion_user;  // base codif_user
    global $connexion;  // base codif_etudiant

    /* ========================
       1. Vérifier dans codif_user
    ========================= */
    $sqlUser = "SELECT username_user 
                FROM codif_user 
                WHERE username_user = ? 
                AND profil_user = 'user'";

    $stmtUser = mysqli_prepare($connexion_user, $sqlUser);
    mysqli_stmt_bind_param($stmtUser, 's', $login);
    mysqli_stmt_execute($stmtUser);
    $resUser = mysqli_stmt_get_result($stmtUser);

    if (!$resUser || mysqli_num_rows($resUser) == 0) {
        return;  // rien à faire
    }

    /* ========================
       2. Vérifier dans codif_etudiant
    ========================= */
    $sqlEtu = 'SELECT num_etu 
               FROM codif_etudiant 
               WHERE num_etu = ?';

    $stmtEtu = mysqli_prepare($connexion, $sqlEtu);
    mysqli_stmt_bind_param($stmtEtu, 's', $login);
    mysqli_stmt_execute($stmtEtu);
    $resEtu = mysqli_stmt_get_result($stmtEtu);

    /* ========================
       3. ÉQUIVALENT DE NOT IN
    ========================= */
    if (!$resEtu || mysqli_num_rows($resEtu) == 0) {
?>
<script>
alert('Chèr(e) étudiant(e), vous n’avez pas codifié pour cette année choisie.');
</script>
<?php
        echo '<meta http-equiv="refresh" content="0;URL=accueil">';
        exit();
    }
}

// //////////////////////////////////////////////////////////////////////////////////////////////

function calculateMontantTotal()
{
    global $connexion;

    // Définir la période par défaut
    $date_debut = '2025-01-01';  // Date de début fixe
    $date_fin = date('Y-m-d');  // Aujourd'hui comme date de fin

    // Construire la requête SQL
    $sql = 'SELECT SUM(pc.montant) AS montantTotal 
            FROM codif_paiement pc
            JOIN codif_validation vl ON pc.id_val = vl.id_val
            WHERE pc.dateTime_paie >= ? AND pc.dateTime_paie <= ?';

    // Préparer la requête
    $stmt = $connexion->prepare($sql);

    // Associer les paramètres
    $stmt->bind_param('ss', $date_debut, $date_fin);

    // Exécuter la requête
    $stmt->execute();
    $result = $stmt->get_result();

    // Récupérer le montant total
    $montantTotal = 0;
    if ($row = $result->fetch_assoc()) {
        $montantTotal = $row['montantTotal'] ?? 0;  // Valeur par défaut si aucun résultat
    }

    return $montantTotal;
}

function calculateCautionSum()
{
    global $connexion;

    // Définir les valeurs par défaut
    $date_debut = '2025-01-01';  // Date de début fixe
    $date_fin = date('Y-m-d');  // Aujourd'hui comme date de fin
    $libelle = '%Caution%';  // Libellé par défaut avec LIKE

    // Construire la requête SQL
    $sql = 'SELECT COUNT(pc.montant) AS countPayments 
            FROM codif_paiement pc
            JOIN codif_validation vl ON pc.id_val = vl.id_val
            WHERE pc.dateTime_paie >= ? AND pc.dateTime_paie <= ?
            AND pc.libelle LIKE ?';

    // Préparer la requête
    $stmt = $connexion->prepare($sql);

    // Associer les paramètres
    $stmt->bind_param('sss', $date_debut, $date_fin, $libelle);

    // Exécuter la requête
    $stmt->execute();
    $result = $stmt->get_result();

    // Récupérer le nombre de paiements
    $countPayments = 0;
    if ($row = $result->fetch_assoc()) {
        $countPayments = $row['countPayments'] ?? 0;  // Valeur par défaut si aucun résultat
    }

    // Calculer la somme totale
    $cautionSum = $countPayments * 5000;

    return $cautionSum;
}

function getAllRegisseurs($connexion)
{
    $query = "SELECT DISTINCT username_user FROM codif_paiement where dateTime_paie>'2024-12-31'  and username_user!='dba_p'";
    $result = mysqli_query($connexion, $query);

    // Vérification de la requête
    if (!$result) {
        die("Erreur lors de l'exécution de la requête : " . mysqli_error($connexion));
    }

    // Tableau pour stocker les regisseurs
    $regisseurs = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $regisseurs[] = $row['username_user'];
    }

    return $regisseurs;  // Retourne un tableau des regisseurs
}

/* function getPaiementWithDateInterval_cs($date_debut, $date_fin, $username)
{
    global $connexion;

    // Définir les valeurs par défaut
    // $date_debut = !empty($date_debut) ? $date_debut : '2025-01-01'; // Date par défaut
    // $date_fin = !empty($date_fin) ? $date_fin : date('Y-m-d'); // Aujourd'hui par défaut

    // Construire la requête avec des conditions dynamiques
    $sql = 'SELECT ce.num_etu, ce.nom, ce.prenoms, pc.dateTime_paie, pc.montant, pc.quittance, pc.username_user, pc.libelle 
            FROM codif_etudiant ce 
            JOIN codif_affectation a ON ce.id_etu = a.id_etu 
            JOIN codif_validation vl ON a.id_aff = vl.id_aff 
            JOIN codif_paiement pc ON pc.id_val = vl.id_val 
            WHERE pc.dateTime_paie >= ? AND pc.dateTime_paie <= ? order by username_user, num_ordre_user';

    // Ajouter la condition pour `username_user` si le paramètre est fourni
    if (!empty($username)) {
        $sql .= ' AND pc.username_user = ?';
    }

    // Préparer la requête
    $stmt = $connexion->prepare($sql);

    // Associer les paramètres dynamiquement
    if (!empty($username)) {
        $stmt->bind_param('sss', $date_debut, $date_fin, $username);
    } else {
        $stmt->bind_param('ss', $date_debut, $date_fin);
    }

    // Exécuter la requête
    $stmt->execute();
    $result = $stmt->get_result();

    // Récupérer les résultats
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    // Retourner les données
    return $data;
} */

function getPaiementWithDateInterval_cs($date_debut, $date_fin, $username)
{
    global $connexion;

    $sql = "
    SELECT *
    FROM (
        SELECT
            ce.num_etu,
            ce.nom,
            ce.prenoms,
            pc.dateTime_paie,
            pc.montant,
            pc.quittance,
            pc.username_user,
            pc.libelle,
            pc.num_ordre_user,
            pc.id_paie
        FROM codif_etudiant ce
        INNER JOIN codif_affectation a
            ON ce.id_etu = a.id_etu
        INNER JOIN codif_validation vl
            ON a.id_aff = vl.id_aff
        INNER JOIN codif_paiement pc
            ON pc.id_val = vl.id_val

        UNION ALL

        SELECT
            ce.num_etu,
            ce.nom,
            ce.prenoms,
            pc.dateTime_paie,
            pc.montant,
            pc.quittance,
            pc.username_user,
            pc.libelle,
            pc.num_ordre_user,
            pc.id_paie
        FROM codif_etudiant ce
        INNER JOIN codif_paiement pc
            ON pc.id_etu = ce.id_etu
        WHERE pc.id_val IS NULL OR pc.id_val = 0
    ) t
    WHERE t.dateTime_paie >= ?
      AND t.dateTime_paie <= ?
    ";

    $params = [$date_debut, $date_fin];
    $types = 'ss';

    if (!empty($username)) {
        $sql .= " AND t.username_user = ?";
        $params[] = $username;
        $types .= 's';
    }

    $sql .= " ORDER BY t.username_user, t.num_ordre_user";

    $stmt = $connexion->prepare($sql);
    $stmt->bind_param($types, ...$params);

    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    $stmt->close();

    return $data;
}

// Fonction Envoi SMS Apres Creation Compte///////////////////////////////////

function sms_compte_created($numero_destinataire, $login, $default_mdp)
{
    $nom = info($login)['3'];
    $prenoms = info($login)['4'];

    $user = 'coudsn';
    $mot_de_passe = 'Mdp@24#';

    // NOUVEAU CODE
    $message = 'Bonjour ' . $prenoms . '. Voici vos infos de connexion sur https://campuscoud.com. Utilisateur: NumeroCarte. Mot de passe: ' . $default_mdp . '.';
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://183.220.113.231/wsSendSMS',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => '{
    "ws_key": "' . $user . '",
    "ws_secret": "' . $mot_de_passe . '",
    "message": "' . $message . '",
    "to": "' . $numero_destinataire . '"
}',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
    ));
    $response = curl_exec($curl);
    curl_close($curl);

    /*if ($err) {
      echo "cURL Error #:" . $err;
    } else {
      echo $response;
    }*/
    /*if($err)
        echo "Erreur: le SMS n'a pas été envoyé !";
    else
        echo "Votre mot de passe vous a été envoyé par SMS au ".$numero_destinataire;*/
}

function sms_compte_created_2($numero_destinataire, $login, $prenoms, $default_mdp)
{
    $user = 'coudsn';
    $mot_de_passe = 'Mdp@24#';

    // NOUVEAU CODE
    $message = 'Bonjour ' . $prenoms . '. Voici vos infos de connexion sur https://campuscoud.com. Utilisateur: NumeroCarte. Mot de passe: ' . $default_mdp . '.';
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://183.220.113.231/wsSendSMS',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => '{
    "ws_key": "' . $user . '",
    "ws_secret": "' . $mot_de_passe . '",
    "message": "' . $message . '",
    "to": "' . $numero_destinataire . '"
}',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
    ));
    $response = curl_exec($curl);
    curl_close($curl);

    /*if ($err) {
      echo "cURL Error #:" . $err;
    } else {
      echo $response;
    }*/
    /*if($err)
        echo "Erreur: le SMS n'a pas été envoyé !";
    else
        echo "Votre mot de passe vous a été envoyé par SMS au ".$numero_destinataire;*/
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// ///////////////sms Attributaires
function sms_attributaires($num_etu)
{
    $user = 'coudsn';
    $mot_de_passe = 'Mdp@24#';

    $numero_destinataire = getTelephoneEtudiant($num_etu);  // $numero_destinataire="777089812";
    $prenoms = info($num_etu)['4'];

    // NOUVEAU CODE
    $message = 'Bonjour ' . $prenoms . '. Vous etes Attributaire dun lit au campus social. Rendez-vous sur la plateforme de codification https://campuscoud.com pour creer un compte et suivre la Rubrique Action à Faire.';
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://183.220.113.231/wsSendSMS',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => '{
    "ws_key": "' . $user . '",
    "ws_secret": "' . $mot_de_passe . '",
    "message": "' . $message . '",
    "to": "' . $numero_destinataire . '"
}',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
    ));
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
}

// ///////////////////////////////////////////////////////

function getDonneesEtudiant($numero_carte)
{
    $telephone0 = '777089812';
    $telephone = '';

    try {
        $json_url = "https://coud@ucad.sn:dhHNg4VmpfZYR6Q@coudservice.ucad.sn/api/etudiant/$numero_carte";
        $json = file_get_contents($json_url);
        $data = json_decode($json);

        $faculte = $data[0]->faculte;
        $departement = $data[0]->departement;
        // $niveauFormation= $data[0]->niveauFormation;  //NON DISPO
        $nom = $data[0]->nom;
        $prenom = $data[0]->prenom;
        $date_naissance = $data[0]->date_naissance;
        $lieu_naissance = $data[0]->lieu_naissance;
        $sexe = $data[0]->sexe;
        // $nationalite= $data[0]->nationalite;           //NON DISPO
        $num_identite = $data[0]->num_identite;
        // $typeEtudiant= $data[0]->typeEtudiant;        // NON DISPO
        // $moyenne= $data[0]->moyenne;                 //NON DISPO
        // $sessionId= $data[0]->sessionId;           //NON DISPO
        // $niveau= $data[0]->niveau;                 //NON DISPO
        // $email_perso= $data[0]->email_perso;       //  NON DISPO
        // $email_ucad= $data[0]->email_ucad;
        $telephone = $data[0]->telephone;
        $etat_inscription = $data[0]->etat_inscription;
        $annee = $data[0]->annee;
    } catch (Exception $e) {
        // echo 'Caught exception: ',  $e->getMessage(), "\n";
    }

    return array($faculte, $departement, $nom, $prenom, $date_naissance, $lieu_naissance, $sexe, $num_identite, $telephone, $etat_inscription, $annee);
}

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

// ///////////////sms Suppleants
function sms_suppleants($num_etu)
{
    $user = 'coudsn';
    $mot_de_passe = 'Mdp@24#';

    $numero_destinataire = getTelephoneEtudiant($num_etu);  // $numero_destinataire="777089812";
    $prenoms = info($num_etu)['4'];

    // NOUVEAU CODE
    $message = 'Bonjour ' . $prenoms . '. Vous etes Suppleant(e) sur un lit et avez la possibilite de loger avec votre Titulaire. Rendez-vous sur la plateforme de codification https://campuscoud.com pour creer un compte et suivre la rubrique Action à Faire.';
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://183.220.113.231/wsSendSMS',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => '{
    "ws_key": "' . $user . '",
    "ws_secret": "' . $mot_de_passe . '",
    "message": "' . $message . '",
    "to": "' . $numero_destinataire . '"
}',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
    ));
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
}

// ///////////////////////////////////////////////////////

// Fonction Envoi SMS aux Reclamants///////////////////////////////////

function sms_reclamation($numtel, $msg)
{
    $user = 'coudsn';
    $mot_de_passe = 'Mdp@24#';

    // NOUVEAU CODE
    $message = $msg;
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://183.220.113.231/wsSendSMS',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => '{
    "ws_key": "' . $user . '",
    "ws_secret": "' . $mot_de_passe . '",
    "message": "' . $message . '",
    "to": "' . $numtel . '"
}',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
    ));
    $response = curl_exec($curl);
    curl_close($curl);

    /*if ($err) {
      echo "cURL Error #:" . $err;
    } else {
      echo $response;
    }*/
    /*if($err)
        echo "Erreur: le SMS n'a pas été envoyé !";
    else
        echo "Votre mot de passe vous a été envoyé par SMS au ".$numero_destinataire;*/
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// Fonction Envoi SMS Apres Paiement Etudiant///////////////////////////////////

function sms_paiement_etudiant($montant, $num_etu, $num_recu)
{
    $telephone = getTelephoneEtudiant($num_etu);
    $prenoms = info($num_etu)['4'];  // echo $telephone." ".$prenoms; exit(); die;

    $user = 'coudsn';
    $mot_de_passe = 'Mdp@24#';
    $date = date('Y-m-d');
    $date = changedateusfr($date);
    $heure = date('H:i:s');

    $message = 'Bonjour ' . $prenoms . ', vous avez paye ' . $montant . 'F pour votre lit le ' . $date . ' a ' . $heure . '. Numero quittance: ' . $num_recu . '. Plus de details sur https://campuscoud.com';
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://183.220.113.231/wsSendSMS',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => '{
    "ws_key": "' . $user . '",
    "ws_secret": "' . $mot_de_passe . '",
    "message": "' . $message . '",
    "to": "' . $telephone . '"
}',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
    ));
    $response = curl_exec($curl);
    curl_close($curl);

    /*if ($err) {
      echo "cURL Error #:" . $err;
    } else {
      echo $response;
    }*/
    /*if($err)
        echo "Erreur: le SMS n'a pas été envoyé !";
    else
        echo "Votre mot de passe vous a été envoyé par SMS au ".$telephone;*/
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// Fonction Envoi SMS pour Recouvrement///////////////////////////////////

function sms_recouvrement($id_etu, $pavillon)
{
    $nom = info3($id_etu)['5'];
    $prenoms = info3($id_etu)['6'];
    $numero_destinataire = info3($id_etu)['17'];
    $num_etu = info3($id_etu)['2'];

    $user = 'coudsn';
    $mot_de_passe = 'Mdp@24#';

    // NOUVEAU CODE
    $message = 'Bonjour ' . $prenoms . ', resident du pavillon ' . $pavillon . ", vous etes pries de proceder au paiement de votre loyer pour eviter tout contentieux avec l'administration.";
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://183.220.113.231/wsSendSMS',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => '{
    "ws_key": "' . $user . '",
    "ws_secret": "' . $mot_de_passe . '",
    "message": "' . $message . '",
    "to": "' . $numero_destinataire . '"
}',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
    ));
    $response = curl_exec($curl);
    curl_close($curl);

    /*if ($err) {
      echo "cURL Error #:" . $err;
    } else {
      echo $response;
    }*/
    /*if($err)
        echo "Erreur: le SMS n'a pas été envoyé !";
    else
        echo "Votre mot de passe vous a été envoyé par SMS au ".$numero_destinataire;*/

    // Stockage
    enreg_sms($num_etu, $numero_destinataire, 'recouvrement');
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// Fonction Envoi SMS au Nouvel Attributtaire apres Forclusion///////////////////////////////////

function sms_nv_attributaire($num_etu)
{  // echo "id".$num_etu;

    $prenoms = info($num_etu)['4'];
    $numero_destinataire = info($num_etu)['16'];

    $user = 'coudsn';
    $mot_de_passe = 'Mdp@24#';

    // NOUVEAU CODE
    $message = 'Bonjour ' . $prenoms . ", suite à la forclusion d'un(e) etudiant(e), vous etes devenu(e) attributaire d'un lit. Rendez-vous vite sur https://campuscoud.com.";
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://183.220.113.231/wsSendSMS',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => '{
    "ws_key": "' . $user . '",
    "ws_secret": "' . $mot_de_passe . '",
    "message": "' . $message . '",
    "to": "' . $numero_destinataire . '"
}',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
    ));
    $response = curl_exec($curl);
    curl_close($curl);

    /*if ($err) {
      echo "cURL Error #:" . $err;
    } else {
      echo $response;
    }*/
    /*if($err)
        echo "Erreur: le SMS n'a pas été envoyé !";
    else
        echo "Votre mot de passe vous a été envoyé par SMS au ".$numero_destinataire;*/

    // Stockage
    enreg_sms($num_etu, $numero_destinataire, 'Nv_Attributaire');
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function sms_agents($telephone, $nom, $matricule)
{
    $user = 'coudsn';
    $mot_de_passe = 'Mdp@24#';

    // NOUVEAU CODE
    $message = 'Bonjour ' . $nom . '. Voici vos informations de connexion à la plateforme https://campuscoud.com . Nom dutilisateur: ' . $matricule . ' , Mot de passe (par default): COUD';
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://183.220.113.231/wsSendSMS',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => '{
    "ws_key": "' . $user . '",
    "ws_secret": "' . $mot_de_passe . '",
    "message": "' . $message . '",
    "to": "' . $telephone . '"
}',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
    ));
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err)
        echo "Erreur: le SMS n'a pas ete envoye :" . $telephone . '<br>';
    else
        echo 'SMS envoye  au ' . $telephone . '<br>';
}

/*
 * function sms_nv_attributaire($telephone,$nom)
 * {
 *
 * $user = "coudsn";
 * $mot_de_passe = "Mdp@24#";
 *
 * //NOUVEAU CODE
 * $message = "Bonjour ".$nom.". Suite à la forclusion d'etudiants, vous etes devenu.e. Attributaire. Rendez-vous vite sur la plateforme https://campuscoud.com et suivez la rubrique Action à faire.";
 * $curl = curl_init();
 *
 * curl_setopt_array($curl, array(
 *   CURLOPT_URL => 'http://183.220.113.231/wsSendSMS',
 *   CURLOPT_RETURNTRANSFER => true,
 *   CURLOPT_ENCODING => '',
 *   CURLOPT_MAXREDIRS => 10,
 *   CURLOPT_TIMEOUT => 0,
 *   CURLOPT_FOLLOWLOCATION => true,
 *   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
 *   CURLOPT_CUSTOMREQUEST => 'POST',
 *   CURLOPT_POSTFIELDS =>'{
 *     "ws_key": "'.$user.'",
 *     "ws_secret": "'.$mot_de_passe.'",
 *     "message": "'.$message.'",
 *     "to": "'.$telephone.'"
 * }',
 *   CURLOPT_HTTPHEADER => array(
 *     'Content-Type: application/json'
 *   ),
 * ));
 * $response = curl_exec($curl);
 * $err = curl_error($curl);
 * curl_close($curl);
 *
 * if($err)
 *     echo "Erreur: le SMS n'a pas ete envoye :".$telephone."<br>";
 * else
 *     echo "SMS envoye  au ".$telephone."<br>";
 *
 * }
 */

function sms_nv_suppleant($num_etu)  // ok
{  // echo "id".$num_etu;

    $prenoms = info($num_etu)['4'];
    $numero_destinataire = info($num_etu)['16'];

    $user = 'coudsn';
    $mot_de_passe = 'Mdp@24#';

    // NOUVEAU CODE
    $message = 'Bonjour ' . $prenoms . ", suite à la forclusion d'un(e) etudiant(e), vous etes devenu(e) attributaire d'un lit. Rendez-vous vite sur https://campuscoud.com.";
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://183.220.113.231/wsSendSMS',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => '{
    "ws_key": "' . $user . '",
    "ws_secret": "' . $mot_de_passe . '",
    "message": "' . $message . '",
    "to": "' . $numero_destinataire . '"
}',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
    ));
    $response = curl_exec($curl);
    curl_close($curl);

    /*if ($err) {
      echo "cURL Error #:" . $err;
    } else {
      echo $response;
    }*/
    /*if($err)
        echo "Erreur: le SMS n'a pas été envoyé !";
    else
        echo "Votre mot de passe vous a été envoyé par SMS au ".$numero_destinataire;*/

    // Stockage
    enreg_sms($num_etu, $numero_destinataire, 'Nv_suppleant');
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// Permet denvoyer un SMS aux suppleants relogés apres les retours de lits
function sms_retours_suppleant($num_etu)  // ok
{  // echo "id".$num_etu;

    $prenoms = info($num_etu)['4'];
    $numero_destinataire = info($num_etu)['16'];

    $user = 'coudsn';
    $mot_de_passe = 'Mdp@24#';

    // NOUVEAU CODE
    $message = 'Bonjour ' . $prenoms . ', suite à la forclusion de votre Titulaire, vous avez desormais la possibilité de loger. Rendez-vous vite sur https://campuscoud.com et suivez la rubrique Action à faire.';
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://183.220.113.231/wsSendSMS',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => '{
    "ws_key": "' . $user . '",
    "ws_secret": "' . $mot_de_passe . '",
    "message": "' . $message . '",
    "to": "' . $numero_destinataire . '"
}',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
    ));
    $response = curl_exec($curl);
    curl_close($curl);

    /*if ($err) {
      echo "cURL Error #:" . $err;
    } else {
      echo $response;
    }*/
    /*if($err)
        echo "Erreur: le SMS n'a pas été envoyé !";
    else
        echo "Votre mot de passe vous a été envoyé par SMS au ".$numero_destinataire;*/

    // Stockage
    enreg_sms($num_etu, $numero_destinataire, 'Nv_suppleant');
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/*
 * Les attributs de la pagination: Pagination par page de 54 elements
 * ********************************************************************************
 */
function getAttributByPagination()
{
    global $page, $limit, $offset, $counter;
    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    $limit = 90;
    $offset = ($page - 1) * $limit;
    $counter = 0;
}

getAttributByPagination();

/*
 * Fonction d'affichage de la liste des etablissements, elle est appeler dans requette.php et affiché dans la page niveau.php
 * ********************************************************************************
 */
function getAllEtablissement()
{
    global $connexion;
    $requeteListeEtablissement = 'SELECT DISTINCT (etablissement) FROM `codif_etudiant` order by etablissement asc';
    $resultatRequeteEtablissement = mysqli_query($connexion, $requeteListeEtablissement);
    return $resultatRequeteEtablissement;
}

function getAllEtablissement_dl()
{
    global $connexion;

    $requeteListeEtablissement = "
        SELECT DISTINCT faculte AS etablissement 
        FROM codif_delai
        WHERE faculte LIKE '%SOCIAL%'
        ORDER BY etablissement ASC
    ";

    $resultatRequeteEtablissement = mysqli_query($connexion, $requeteListeEtablissement);
    return $resultatRequeteEtablissement;
}

function getAllEtablissement_1()
{
    global $connexion;
    $sql = 'SELECT DISTINCT etablissement FROM codif_etudiant';
    $result = mysqli_query($connexion, $sql);

    $liste = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $liste[] = $row['etablissement'];
    }

    return $liste;
}

/*
 * Fonction d'affichage de la liste des etablissements, elle est appeler dans requette.php et affiché dans la page niveau.php
 * ********************************************************************************
 */
function getAllEtablissement_2()
{
    global $connexion;
    $requeteListeEtablissement = 'SELECT DISTINCT (faculte) FROM `codif_delai`';
    $resultatRequeteEtablissement = mysqli_query($connexion, $requeteListeEtablissement);
    return $resultatRequeteEtablissement;
}

/*
 * Fonction d'affichage de la liste des Niveau de formation, elle est appeler dans connecte.php
 * ********************************************************************************
 */
function getAllNiveauFormation()
{
    global $connexion;
    $requeteListeEtablissement = 'SELECT DISTINCT (niveauFormation) FROM `codif_etudiant`';
    $resultatRequeteEtablissement = mysqli_query($connexion, $requeteListeEtablissement);
    return $resultatRequeteEtablissement;
}

/*
 * Fonction d'affichage de la liste des Niveau de formation, elle est appeler dans connecte.php
 * ********************************************************************************
 */
function getAllNiveauFormationByQuota()
{
    global $connexion;
    $requeteListeEtablissement = 'SELECT DISTINCT (niveauFormation) FROM `codif_quota`';
    $resultatRequeteEtablissement = mysqli_query($connexion, $requeteListeEtablissement);
    return $resultatRequeteEtablissement;
}

/*
 * Fonction d'affichage de la liste des Niveau de formation, elle est appeler dans connecte.php
 * ********************************************************************************
 */
function getAllNiveauFormation_2($etablissement)
{
    global $connexion;
    $requeteListeEtablissement = "SELECT DISTINCT (niveauFormation) FROM `codif_etudiant` where etablissement='$etablissement'";
    $resultatRequeteEtablissement = mysqli_query($connexion, $requeteListeEtablissement);
    return $resultatRequeteEtablissement;
}

/*
 * Fonction d'affichage de la liste des departement, elle est appeler dans requette.php et affiché dans la page niveau.php
 * ********************************************************************************
 */
function getAllDepartement($dataFaculte)
{
    global $connexion;
    $requeteListeDepartement = "SELECT DISTINCT(departement) FROM `codif_etudiant` WHERE `etablissement`='" . $dataFaculte . "'";
    $resultatRequeteDepartement = mysqli_query($connexion, $requeteListeDepartement);
    return $resultatRequeteDepartement;
}

/*
 * Fonction d'affichage de la liste des departement sous forme d'un tableau de donnée, elle est appeler dans requette.php et affiché dans la page niveau.php
 * ********************************************************************************
 */
function getOneByDepartemennt($dataDepartement)
{
    $i = 0;
    while ($rowDepartement = mysqli_fetch_array($dataDepartement)) {
        $tableauDataFaculte[$i] = $rowDepartement['departement'];
        $i++;
    }
    return $tableauDataFaculte;
}

/*
 * Fonction d'affichage de la liste des niveaux de formation, elle est appeler dans requette.php et affiché dans la page niveau.php
 * ********************************************************************************
 */
function getAllNiveau($dataOneDepartement)
{
    try {
        global $connexion;
        $requeteNiveauFormation = "SELECT DISTINCT(niveauFormation) FROM `codif_etudiant` WHERE `departement`='" . $dataOneDepartement . "'";
        $resultatRequeteNiveauFormation = mysqli_query($connexion, $requeteNiveauFormation);
        $i = 0;
        while ($rowNiveauFormation = mysqli_fetch_array($resultatRequeteNiveauFormation)) {
            $tableauDataNiveauFormation[$i] = $rowNiveauFormation['niveauFormation'];
            $i++;
        }
        return $tableauDataNiveauFormation;
    } catch (Exception $e) {
        echo 'NiveauFormation introuvable !';
    }
}

/* * ********************************************************************************
Fonction pour recuperer les données du suppleant selon le rang du titulaire
********************************************************************************* */
function getOneSuppleantByTitulaire($quota, $classe, $sexe, $rang)
{
    $row_one_student = getAllDatastudentStatus($quota, $classe, $sexe);
    for ($i = 0; $i < count($row_one_student); $i++) {
        if ($row_one_student[$i]['rang'] == $rang + $quota) {
            return $row_one_student[$i];
        }
    }
}

/* *********************************************************************************
Fonction pour verifier si le supleant a deja valider sa validation et est sur le meme pavillon que le chef de residence
********************************************************************************* */
function getLogerSuppleant($num_etu, $pavillon)
{
    global $connexion;
    $sql = "SELECT * FROM `codif_validation` JOIN codif_affectation ON codif_affectation.id_aff = codif_validation.id_aff JOIN codif_lit ON codif_lit.id_lit = codif_affectation.id_lit JOIN codif_etudiant ON codif_etudiant.id_etu = codif_affectation.id_etu WHERE codif_etudiant.num_etu ='$num_etu' AND codif_lit.pavillon='$pavillon'";
    $result = mysqli_query($connexion, $sql);
    return $result->fetch_assoc();
}

/* *********************************************************************************
Fonction pour verifier si le letudiant a paye la caution
********************************************************************************* */
function verifCaution($id_etu)
{
    // Facturer la caution à tous sans exception

    /*
     * global $connexion;
     *  $sql = "SELECT * FROM `codif_paiement` WHERE libelle like '%CAUTION%' and id_val in (SELECT id_val from codif_validation where id_aff in (SELECT id_aff from codif_affectation where id_etu='$id_etu'));";
     *  $result = mysqli_query($connexion, $sql);
     *  return $result->fetch_assoc();
     */

    return 1;  // La verification aura tjr un resultat positif
}

/*
 * Fonction d'affichage de la Liste des chambres deja affecter a une classe selon le niveau de formation, elle est appeler dans requette.php et affiché dans la page detailsLits.php
 * ********************************************************************************
 */
function getLitOneByNiveau($classe, $sexe)
{
    global $connexion, $limit, $offset;
    $requeteLitClasse = "SELECT codif_lit.*, CASE WHEN codif_quota.id_lit_q IS NOT NULL AND codif_affectation.id_lit IS NOT NULL THEN 'Migré dans les deux' WHEN codif_quota.id_lit_q IS NOT NULL THEN 'Migré vers codif_quota uniquement' WHEN codif_affectation.id_lit IS NOT NULL THEN 'Migré vers codif_affectation uniquement' ELSE 'Non migré' END AS statut_migration FROM codif_lit LEFT JOIN codif_quota ON codif_lit.id_lit = codif_quota.id_lit_q LEFT JOIN codif_affectation ON codif_lit.id_lit = codif_affectation.id_lit WHERE codif_quota.NiveauFormation = '$classe' AND codif_lit.sexe='$sexe' AND (codif_affectation.statut != 'Suppleant(e)' OR codif_affectation.statut IS NULL)";
    $resultatRequeteLitClasse = mysqli_query($connexion, $requeteLitClasse);
    return $resultatRequeteLitClasse;
}

/*
 * Fonction d'affichage de la Liste des pavillon deja affecter a une classe selon le niveau de formation, elle est appeler dans requette.php et affiché dans la page detailsLits.php (elle sert de filtre des pavillon)
 * ********************************************************************************
 */
function getPavillonOneByNiveau($classe, $sexe)
{
    global $connexion, $limit, $offset;
    $requeteLitClasse = "SELECT DISTINCT (pavillon), CASE WHEN codif_quota.id_lit_q IS NOT NULL AND codif_affectation.id_lit IS NOT NULL THEN 'Migré dans les deux' WHEN codif_quota.id_lit_q IS NOT NULL THEN 'Migré vers codif_quota uniquement' WHEN codif_affectation.id_lit IS NOT NULL THEN 'Migré vers codif_affectation uniquement' ELSE 'Non migré' END AS statut_migration FROM codif_lit LEFT JOIN codif_quota ON codif_lit.id_lit = codif_quota.id_lit_q LEFT JOIN codif_affectation ON codif_lit.id_lit = codif_affectation.id_lit WHERE codif_quota.NiveauFormation = '$classe' AND codif_lit.sexe='$sexe' LIMIT $limit OFFSET $offset";
    $resultatRequeteLitClasse = mysqli_query($connexion, $requeteLitClasse);
    return $resultatRequeteLitClasse;
}

/*
 * Fonction d'affichage de la Liste des chambres deja affecter a une classe selon le niveau de formation, elle est appeler dans requette.php et affiché dans la page detailsLits.php
 * ********************************************************************************
 */
function getLitOneByNiveauFromPersonnel($classe, $sexe)
{
    global $connexion;
    $requeteLitClasse = "SELECT codif_affectation.*, codif_etudiant.*, codif_lit.*, CASE WHEN vl.id_aff IS NOT NULL THEN 'Migré' ELSE 'Non migré' END AS migration_status FROM codif_affectation INNER JOIN codif_etudiant ON codif_affectation.id_etu = codif_etudiant.id_etu INNER JOIN codif_lit ON codif_affectation.id_lit = codif_lit.id_lit LEFT JOIN codif_validation vl ON codif_affectation.id_aff = vl.id_aff WHERE codif_etudiant.niveauFormation = '$classe' AND codif_lit.sexe='$sexe'";
    $resultatRequeteLitClasse = mysqli_query($connexion, $requeteLitClasse);
    return $resultatRequeteLitClasse;
}

/*
 * Fonction d'affichage des information du lit deja choisi selon son numero etudiant, elle sera appeler dans la page validation
 * ********************************************************************************
 */
function getOneByAffectation($num_etu)
{
    global $connexion;
    $requeteLitClasse = "SELECT *, CASE WHEN vl.id_aff IS NOT NULL THEN 'Migré' ELSE 'Non migré' END AS migration_status FROM codif_affectation INNER JOIN codif_etudiant ON codif_affectation.id_etu = codif_etudiant.id_etu INNER JOIN codif_lit ON codif_affectation.id_lit = codif_lit.id_lit LEFT JOIN codif_validation vl ON codif_affectation.id_aff = vl.id_aff WHERE codif_etudiant.num_etu = '$num_etu'";
    $resultatRequeteLitClasse = mysqli_query($connexion, $requeteLitClasse);
    return $resultatRequeteLitClasse;
}

/*
 * Fonction d'affichage des information du lit deja valider par le personnel selon son numero etudiant, elle sera appeler dans la page paiement
 * ********************************************************************************
 */
function getOneByValidate($num_etu)
{
    global $connexion;
    $requeteLitClasseValide = "SELECT *, vl.id_val, CASE WHEN pc.id_val IS NOT NULL THEN 'Migré dans codif_paiement' WHEN codif_paiement.id_val IS NOT NULL THEN 'Migré dans autre_table' ELSE 'Non migré' END AS migration_status FROM codif_validation vl JOIN codif_affectation a ON vl.id_aff = a.id_aff JOIN codif_etudiant ce ON a.id_etu = ce.id_etu JOIN codif_lit cl ON a.id_lit = cl.id_lit LEFT JOIN codif_paiement pc ON vl.id_val = pc.id_val LEFT JOIN codif_paiement ON vl.id_val = codif_paiement.id_val WHERE ce.num_etu = '$num_etu'";
    $resultatRequeteLitClasseValide = mysqli_query($connexion, $requeteLitClasseValide);
    return $resultatRequeteLitClasseValide;
}

/*
 * Fonction d'affichage des information du lit deja valider par le personnel selon son numero etudiant, elle sera appeler dans la page paiement
 * ********************************************************************************
 */
function getOneByValidatePaiement($num_etu, $pavillon)
{
    global $connexion;
    $requeteLitClasseValide = "
SELECT 
    ce.id_etu,
    ce.num_etu,
    ce.nom,
    ce.prenoms,
    ce.etablissement,
    ce.niveauFormation,
    cl.id_lit,
    cl.pavillon,
    cl.campus,
    cl.lit,
    vl.id_val,
    vl.dateTime_val,
    pc.id_paie,
    pc.dateTime_paie,
    CASE 
        WHEN l.id_paie IS NOT NULL THEN 'Migré'
        ELSE 'Non migré'
    END AS etat_id_paie
FROM codif_etudiant ce
JOIN codif_affectation a ON ce.id_etu = a.id_etu
JOIN codif_validation vl ON a.id_aff = vl.id_aff
JOIN codif_lit cl ON a.id_lit = cl.id_lit
LEFT JOIN codif_paiement pc ON vl.id_val = pc.id_val
LEFT JOIN codif_loger l ON pc.id_paie = l.id_paie
WHERE ce.num_etu = '$num_etu'
AND cl.pavillon = '$pavillon'
ORDER BY pc.id_paie ASC
";
    $resultatRequeteLitClasseValide = mysqli_query($connexion, $requeteLitClasseValide);
    return $resultatRequeteLitClasseValide;
}

/*
 * Fonction d'affichage de la Liste des chambres aavec les option migré et non migré, elle est appeler dans requette.php et affiché dans la page listeLits.php
 * ********************************************************************************
 */
function getAllLit($sexe)
{
    global $connexion, $limit, $offset;
    $sql = "SELECT codif_lit.*, CASE WHEN codif_quota.id_lit_q IS NOT NULL THEN 'Migré' ELSE 'Non migré' END AS statut_migration FROM codif_lit LEFT JOIN codif_quota ON codif_lit.id_lit = codif_quota.id_lit_q WHERE codif_lit.sexe = '$sexe' LIMIT $limit OFFSET $offset";
    $resultatRequeteTotalLit = mysqli_query($connexion, $sql);
    return $resultatRequeteTotalLit;
}

/*
 * Fonction d'affichage de la Liste des chambres deja affecter a une classe selon la classe, elle est appeler dans requette.php et affiché dans la page codifier.php
 * ********************************************************************************
 */
function getLitValideByClasse($classe, $sexe)
{
    global $connexion, $limit, $offset;
    $requeteLitClasseEtudiant = "SELECT codif_lit.*, CASE WHEN codif_quota.id_lit_q IS NOT NULL AND codif_affectation.id_lit IS NOT NULL THEN 'Migré dans les deux' WHEN codif_quota.id_lit_q IS NOT NULL THEN 'Migré vers codif_quota uniquement' WHEN codif_affectation.id_lit IS NOT NULL THEN 'Migré vers codif_affectation uniquement' ELSE 'Non migré' END AS statut_migration FROM codif_lit LEFT JOIN codif_quota ON codif_lit.id_lit = codif_quota.id_lit_q LEFT JOIN codif_affectation ON codif_lit.id_lit = codif_affectation.id_lit WHERE codif_quota.NiveauFormation = '$classe' AND codif_lit.sexe = '$sexe' LIMIT $limit OFFSET $offset";
    $resultRequeteLitClasseEtudiant = mysqli_query($connexion, $requeteLitClasseEtudiant);
    return $resultRequeteLitClasseEtudiant;
}

/*
 * Fonction d'affichage de la Liste de toutes les pavillons, elle est appeler dans requette.php et affiché dans la page listeLits.php
 * ********************************************************************************
 */
function getAllPavillon($sexe)
{
    global $connexion;
    $requetePavillon = "SELECT DISTINCT (pavillon) FROM `codif_lit` WHERE codif_lit.sexe = '$sexe'";
    $resultatRequetePavillon = mysqli_query($connexion, $requetePavillon);
    return $resultatRequetePavillon;
}

/*
 * Comptez le nombre total d'options dans la base de données: pagination total lit dans la page listeLits.php
 * ********************************************************************************
 */
function getAllLitPagination($sexe)
{
    global $connexion, $limit, $count_data_total;
    $count_queryTotalLit = "SELECT COUNT(*) as total FROM codif_lit WHERE codif_lit.sexe = '$sexe'";
    $count_resultat_total = mysqli_query($connexion, $count_queryTotalLit);
    if ($count_resultat_total) {
        $count_data_total = mysqli_fetch_assoc($count_resultat_total);
        $total_lit_pages = ceil($count_data_total['total'] / $limit);
        return $total_lit_pages;
    } else {
        $total_lit_pages = 1;
        return $total_lit_pages;
    }
}

/*
 * Comptez le nombre total d'options dans la base de données: pagination liste lits d'une classe selon l'etudiant connecté dans la page codifier.php
 * ********************************************************************************
 */
function getLitByStudent($classe, $sexe)
{
    global $connexion, $limit, $count_dataEtudiant;
    $count_queryEtudiant = "SELECT COUNT(*) as total FROM codif_quota JOIN codif_lit ON codif_quota.id_lit_q = codif_lit.id_lit WHERE `NiveauFormation`='$classe' AND codif_lit.sexe = '$sexe'";
    $count_resultEtudiant = mysqli_query($connexion, $count_queryEtudiant);
    if ($count_resultEtudiant) {
        $count_dataEtudiant = mysqli_fetch_assoc($count_resultEtudiant);
        $total_pagesEtudiant = ceil($count_dataEtudiant['total'] / $limit);
        return $total_pagesEtudiant;
    } else {
        $total_pagesEtudiant = 1;
        return $total_pagesEtudiant;
    }
}

/*
 * Comptez le nombre total d'options dans la base de données details lits affecter (codif_quota)
 * ********************************************************************************
 */
function getLitByQuotas($classe, $sexe)
{
    global $connexion, $limit, $count_datas;
    $count_querys = "SELECT COUNT(*) as total FROM codif_quota JOIN codif_lit ON codif_quota.id_lit_q = codif_lit.id_lit WHERE `NiveauFormation`='$classe' AND codif_lit.sexe = '$sexe'";
    $count_results = mysqli_query($connexion, $count_querys);
    if ($count_results) {
        $count_datas = mysqli_fetch_assoc($count_results);
        $total_pagess = ceil($count_datas['total'] / $limit);
        return $total_pagess;
    } else {
        $total_pagess = 1;
        return $total_pagess;
    }
}

/*
 * Fonction pour enregistrer les donnees des codif_quota
 * ********************************************************************************
 */
function addQuotas($buttonId, $user, $NiveauFormation)
{
    global $connexion;
    $date = date('Y-n-j');
    $requeteInsertcodif_quota = "INSERT INTO `codif_quota` (`id_lit_q`, `username_user`, `NiveauFormation`, `annee`) VALUES ('$buttonId', '$user', '$NiveauFormation', '$date')";
    $requete = $connexion->prepare($requeteInsertcodif_quota);
    $requete->execute();
    return header('Location: ../profils/personnels/listeLits.php');
}

/*
 * Fonction pour enregistrer les SMS
 * ********************************************************************************
 */
function enreg_sms($num_etu, $telephone, $type)
{
    global $connexion, $requete;
    $datesys = date('Y-m-d H:i:s');
    $ins = "INSERT INTO `codif_sms`(`num_etu`,`telephone`,`type`,`datesys`) VALUES('$num_etu','$telephone','$type','$datesys')";
    $requete = $connexion->prepare($ins);
    return $requete->execute();
}

/*
 * Fonction permet l'enregistrement des lit validé par le personnels
 * ********************************************************************************
 */
function setValidation($buttonId, $user)
{
    global $connexion, $requete;
    $date = date('Y-m-d H:i:s');
    $requeteInsertcodif_quota = "INSERT INTO `codif_validation` (`id_aff`, `username_user`, `dateTime_val`) VALUES ('$buttonId', '$user', '$date')";
    $requete = $connexion->prepare($requeteInsertcodif_quota);
    return $requete->execute();
}

/*
 * Fonction permet l'enregistrement des paiements de lit validé par le personnels
 * ********************************************************************************
 */
function setPaiement($buttonId, $user, $montant, $libelle, $quittance, $an, $ordre)
{
    global $connexion, $requete;
    $date = date('Y-m-d H:i:s');

    $requeteInsertcodif_quota = "INSERT INTO `codif_paiement` (`id_val`, `username_user`, `dateTime_paie`, `montant`,`libelle`,`quittance`,`an`,`num_ordre_user`) 
\tVALUES ('$buttonId', '$user', '$date', '$montant', '$libelle','$quittance','$an','$ordre')";
    $requete = $connexion->prepare($requeteInsertcodif_quota);
    return $requete->execute();
}

/*
 * Fonction permet l'enregistrement des paiements de lit validé par le personnels
 * ********************************************************************************
 */
function accronyme($user)
{
    global $connexion_user;
    $rq = "SELECT var FROM codif_user WHERE `username_user`='$user'";
    $ex = mysqli_query($connexion_user, $rq);

    $st = mysqli_fetch_assoc($ex);
    $var = $st['var'];
    return $var;
}

/*
 * Fonction permet l'enregistrement du logement du titulaire
 * ********************************************************************************
 */
/*function setLoger($buttonId, $user)
{
    global $connexion, $requete;
    $date = date("Y-n-j");
    $requeteInsertcodif_quota = "INSERT INTO `codif_loger` (`id_paie`, `dateTime_loger`, `username_user`) VALUES ('$buttonId', '$date', '$user')";
    $requete = $connexion->prepare($requeteInsertcodif_quota);
    return $requete->execute();
}*/

/*
 * Fonction permet l'enregistrement du lpgement du Suppleant(e)
 * ********************************************************************************
 */
/*function setLogerSuppleant($buttonId, $user)
{
    global $connexion, $requete;
    $date = date("Y-n-j");
    $requeteInsertcodif_quota = "INSERT INTO `codif_loger` (`id_val`, `dateTime_loger`, `username_user`) VALUES ('$buttonId', '$date', '$user')";
    $requete = $connexion->prepare($requeteInsertcodif_quota);
    return $requete->execute();
}*/

function setLoger($id_paie, $user, $id_etu)
{
    /*global $connexion;
    $date = date("Y-m-d H:i:s");
    $requeteInsertcodif_quota = "INSERT INTO `codif_loger` (`id_paie`, `dateTime_loger`, `username_user`, `id_etu`, `statut`)
                                 VALUES (?, ?, ?, ?, ?)";
    $requete = $connexion->prepare($requeteInsertcodif_quota);
    if ($requete === false) {
        die('Erreur de préparation de la requête : ' . $connexion->error);
    }
    $requete->bind_param($id_paie, $date, $user, $id_etu, 'attributaire');
    return $requete->execute();*/

    global $connexion, $requete;
    $date = date('Y-m-d H:i:s');
    $requeteInsertcodif_quota = "INSERT INTO `codif_loger` (`id_val`,`id_paie`, `dateTime_loger`, `username_user`, `id_etu`, `statut`) 
\t                                                VALUES (NULL,'$id_paie', '$date', '$user', '$id_etu', 'Attributaire')";
    // echo $requeteInsertcodif_quota;exit();
    $requete = $connexion->prepare($requeteInsertcodif_quota);
    return $requete->execute();
}

function setLogerClando($id_paie, $user, $id_etu)
{
    global $connexion;
    $date = date('Y-m-d H:i:s');
    $clando = 'Clando';
    $requeteInsertcodif_quota = 'INSERT INTO `codif_loger` (`id_paie`, `dateTime_loger`, `username_user`, `id_etu`, `statut`) 
                                 VALUES (?, ?, ?, ?, ?)';
    $requete = $connexion->prepare($requeteInsertcodif_quota);
    if ($requete === false) {
        die('Erreur de préparation de la requête : ' . $connexion->error);
    }
    $requete->bind_param('issis', $id_paie, $date, $user, $id_etu, $clando);
    return $requete->execute();
}

/*
 * Fonction permet l'enregistrement du lpgement du suppleant
 * ********************************************************************************
 */
function setLogerSuppleant($buttonId, $user, $id_etu)
{
    global $connexion, $requete;
    $date = date('Y-n-j');
    $requeteInsertcodif_quota = "INSERT INTO `codif_loger` (`id_val`, `dateTime_loger`, `username_user`, `id_etu`, `statut`) VALUES ('$buttonId', '$date', '$user', '$id_etu', 'Suppleant(e)')";
    $requete = $connexion->prepare($requeteInsertcodif_quota);
    return $requete->execute();
}

/*
 * Fonction pour retiré les codif_quota deja affecter
 * ********************************************************************************
 */
function removeQuotas($buttonId)
{
    global $connexion;
    $sql0 = "DELETE FROM codif_quota WHERE id_lit_q = '$buttonId'";
    $query0 = $connexion->prepare($sql0);
    return $query0->execute();
}

/*
 * Fonction d'affichage de l'etudiant ayant deja choisi une lit
 * ********************************************************************************
 */
function getStudentChoiseLit($idEtu)
{
    global $connexion;
    $requeteAffectEtu = "SELECT * FROM `codif_affectation` where `id_etu`=$idEtu";
    $inforequeteAffectEtu = $connexion->query($requeteAffectEtu);
    return $inforequeteAffectEtu;
}

/*
 * Fonction pour verifier si le TITULAIRE a valider son hebergement
 * ********************************************************************************
 */
function getValidateLitByStudent_2($numEtudiant)
{
    global $connexion;
    $studentValidate = "SELECT * FROM codif_validation JOIN codif_affectation ON codif_validation.id_aff = codif_affectation.id_aff JOIN codif_etudiant 
\tON codif_etudiant.id_etu = codif_affectation.id_etu WHERE codif_etudiant.num_etu='$numEtudiant'";
    $infoValite = mysqli_query($connexion, $studentValidate);
    $data = $infoValite->fetch_assoc();
    // return $data;
    if ($data) {
        return 'oui';
    }
}

/*
 * Fonction d'affichage du lit deja choisie par l'etudiant connecté
 * ********************************************************************************
 */
function getOneLitByStudent($num_etu)
{
    global $connexion;
    $requeteLitEtu = "SELECT codif_lit.* FROM codif_affectation JOIN codif_lit ON codif_affectation.id_lit = codif_lit.id_lit JOIN codif_etudiant ON 
\tcodif_etudiant.id_etu = codif_affectation.id_etu where codif_etudiant.num_etu='$num_etu'";
    $resultatReqLitEtu = $connexion->query($requeteLitEtu);
    return $resultatReqLitEtu;
}

/*
 * Fonction d'affichage du lit choisi par l'etudiant, cette fonction sera appeler dans le fichier du convention
 * ********************************************************************************
 */
function getLitOneStudentByConvention($lit)
{
    global $connexion;
    $i = 0;
    $requeteLit = "SELECT * FROM `codif_lit` WHERE `id_lit`='$lit'";
    $resultRequeteLit = mysqli_query($connexion, $requeteLit);
    while ($row = mysqli_fetch_array($resultRequeteLit)) {
        $tab[$i] = $row;
        $i++;
    }
    return $tab;
}

/*
 * Fonction d'affichage de la date que l'etudiant a choisi le lit
 * ********************************************************************************
 */
function getDateLitByStudent($idLit)
{
    global $connexion;
    $requeteDateLit = "SELECT `dateTime` FROM `codif_affectation` WHERE `id_lit`='$idLit'";
    $resultRequeteDateLit = mysqli_query($connexion, $requeteDateLit);
    while ($row = mysqli_fetch_array($resultRequeteDateLit)) {
        $dateLit = $row;
    }
    $timestamp = strtotime($dateLit['dateTime']);
    $date_formatee = date('d-m-Y', $timestamp);
    return $date_formatee;
}

/*
 * Fonction de connexion dans l'espace utilisateur
 * ********************************************************************************
 */
function login($username, $password)
{
    global $connexion_user;
    $users = "SELECT * FROM `codif_user` where `username_user`='$username' and `password_user` =  '" . SHA1($password) . "' and is_active=1";
    // $users = "SELECT * FROM `codif_user` where `username_user`='$username' and `password_user` =  '$password' ";

    $info = $connexion_user->query($users);
    return $info->fetch_assoc();
}

/*
 * Fonction de verification du politique de confidentialité
 * ********************************************************************************
 */
function getPolitiqueConf($id)
{
    global $connexion;
    $usersPolitique = "SELECT * FROM `codif_politique` where `id_etu`='$id'";
    $infoPolitique = mysqli_query($connexion, $usersPolitique);
    return $infoPolitique->fetch_assoc();
}

/*
 * Fonction de filtre de la liste des lits
 * ********************************************************************************
 */
function setFiltre($filter, $sexe)
{
    global $connexion;
    $sqlFilter = "SELECT codif_lit.*, CASE WHEN codif_quota.id_lit_q IS NOT NULL AND codif_affectation.id_lit IS NOT NULL THEN 'Migré dans les deux' WHEN codif_quota.id_lit_q IS NOT NULL THEN 'Migré vers codif_quota uniquement' WHEN codif_affectation.id_lit IS NOT NULL THEN 'Migré vers codif_affectation uniquement' ELSE 'Non migré' END AS statut_migration FROM codif_lit LEFT JOIN codif_quota ON codif_lit.id_lit = codif_quota.id_lit_q LEFT JOIN codif_affectation ON codif_lit.id_lit = codif_affectation.id_lit WHERE pavillon='$filter' AND codif_lit.sexe = '$sexe'";
    if ($filter) {
        $resultatRequeteTotalLit = mysqli_query($connexion, $sqlFilter);
        return $resultatRequeteTotalLit;
    }
}

function setFiltre_detail($filter, $sexe, $niveau)
{
    global $connexion;
    $sqlFilter = "SELECT codif_lit.*, CASE WHEN codif_quota.id_lit_q IS NOT NULL AND codif_affectation.id_lit IS NOT NULL THEN 'Migré dans les deux' WHEN codif_quota.id_lit_q IS NOT NULL THEN 'Migré vers codif_quota uniquement' WHEN codif_affectation.id_lit IS NOT NULL THEN 'Migré vers codif_affectation uniquement' ELSE 'Non migré' END AS statut_migration FROM codif_lit LEFT JOIN codif_quota ON codif_lit.id_lit = codif_quota.id_lit_q LEFT JOIN codif_affectation ON codif_lit.id_lit = codif_affectation.id_lit WHERE pavillon='$filter' AND codif_lit.sexe = '$sexe'AND (codif_affectation.statut != 'Suppleant(e)' OR codif_affectation.statut IS NULL) AND codif_quota.NiveauFormation = '$niveau'";
    if ($filter) {
        $resultatRequeteTotalLit = mysqli_query($connexion, $sqlFilter);
        return $resultatRequeteTotalLit;
    }
}

/*
 * *********************************************************************************
 */

// Fonction du pagination du filtre, cette fonction sera appeler dans la page listeLits.php
function getPaginationFiltre($filter, $sexe)
{
    global $connexion, $limit, $offset, $count_data_total;
    $count_queryTotalLit = "SELECT COUNT(*) as total, CASE WHEN codif_quota.id_lit_q IS NOT NULL AND codif_affectation.id_lit IS NOT NULL THEN 'Migré dans les deux' WHEN codif_quota.id_lit_q IS NOT NULL THEN 'Migré vers codif_quota uniquement' WHEN codif_affectation.id_lit IS NOT NULL THEN 'Migré vers codif_affectation uniquement' ELSE 'Non migré' END AS statut_migration FROM codif_lit LEFT JOIN codif_quota ON codif_lit.id_lit = codif_quota.id_lit_q LEFT JOIN codif_affectation ON codif_lit.id_lit = codif_affectation.id_lit WHERE pavillon='$filter' AND codif_lit.sexe = '$sexe' LIMIT $limit OFFSET $offset";
    $count_resultat_total = mysqli_query($connexion, $count_queryTotalLit);
    if ($count_resultat_total) {
        $count_data_total = mysqli_fetch_assoc($count_resultat_total);
        if (!$limit) {
            $limit = 1;
        }
        $total_lit_pages = ceil($count_data_total['total'] / $limit);
        return $total_lit_pages;
    } else {
        $total_lit_pages = 1;
        return $total_lit_pages;
    }
}

/*
 * Fonction du pagination du filtre, cette fonction sera appeler dans la page listeLits.php
 * ********************************************************************************
 */
function getPaginationFiltreClasse($filter, $sexe)
{
    global $connexion, $limit, $offset, $count_data_total;
    $count_queryTotalLit = "SELECT COUNT(*) as total, CASE WHEN codif_quota.id_lit_q IS NOT NULL AND codif_affectation.id_lit IS NOT NULL THEN 'Migré dans les deux' WHEN codif_quota.id_lit_q IS NOT NULL THEN 'Migré vers codif_quota uniquement' WHEN codif_affectation.id_lit IS NOT NULL THEN 'Migré vers codif_affectation uniquement' ELSE 'Non migré' END AS statut_migration FROM codif_lit LEFT JOIN codif_quota ON codif_lit.id_lit = codif_quota.id_lit_q LEFT JOIN codif_affectation ON codif_lit.id_lit = codif_affectation.id_lit WHERE pavillon='$filter' AND codif_lit.sexe = '$sexe' LIMIT $limit OFFSET $offset";
    $count_resultat_total = mysqli_query($connexion, $count_queryTotalLit);
    if ($count_resultat_total) {
        $count_data_total = mysqli_fetch_assoc($count_resultat_total);
        $total_lit_pages = ceil($count_data_total['total'] / $limit);
        return $total_lit_pages;
    } else {
        $total_lit_pages = 1;
        return $total_lit_pages;
    }
}

/*
 * Fonction d'affichage les information de l'utilisateur connecté (etudiant)
 * ********************************************************************************
 */
function studentConnect($username)
{
    global $connexion;
    $users = "SELECT * FROM `codif_etudiant` where `num_etu`='$username'";
    $info = $connexion->query($users);
    return $info->fetch_assoc();
}

function studentConnect3($username)
{
    global $connexion;
    $users = "SELECT * FROM `codif_etudiant` where `num_etu`='$username'";
    $info = $connexion->query($users);
    return $info->fetch_assoc();
}

/*
 * Fonction d'affichage les information de l'utilisateur connecté (personnel)
 * ********************************************************************************
 */
function personnelConnect($username)
{
    global $connexion;
    $users = "SELECT * FROM `users` where `num_etu`='$username'";
    $info = $connexion->query($users);
    return $info->fetch_assoc();
}

/*
 * Fonction pour récupérer les informations de l'étudiant pour le paiement de la caution
 * ********************************************************************************
 */
function infoStudentPaie($numEtudiant)
{
    global $connexion;
    $sql = 'SELECT e.nom, e.prenom,a.id, e.numEtudiant, e.niveau,e.datenaissance,e.lieu_naissance, l.pavillon, l.chambre, l.litFROM etudiant e JOIN codif_affectation a ON e.id = a.idEtudiant JOIN lit l ON a.idLit = l.id WHERE e.numEtudiant = ?';
    $stmt = $connexion->prepare($sql);
    $stmt->bind_param('s', $numEtudiant);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result;
}

// Affiche date francais
function changedateusfr($dateus)
{
    $datefr = $dateus[8] . $dateus[9] . '-' . $dateus[5] . $dateus[6] . '-' . $dateus[0] . $dateus[1] . $dateus[2] . $dateus[3];
    return $datefr;
}

/*
 * Fonction d'affichage du format date
 * ********************************************************************************
 */
function dateFromat($date)
{
    $timestamp = strtotime($date);
    $date_formatee = date('Y-m-d', $timestamp);
    return $date_formatee;
}

/*
 * Fonction pour verifier si lE TITULAIRE a valider son hebergement
 * ********************************************************************************
 */
function getChoixLitByStudent($numEtudiant)
{
    global $connexion;
    $studentValidate = "SELECT * FROM codif_affectation JOIN codif_etudiant ON codif_etudiant.id_etu = codif_affectation.id_etu WHERE codif_etudiant.num_etu='$numEtudiant'";
    $infoValite = mysqli_query($connexion, $studentValidate);
    $data = $infoValite->fetch_assoc();
    // return $data;
    if ($data) {
        return 'Presentez-vous au service Hebergement pour valider votre codification!';
    } else {
        /* $infos_delai = getAllDelai("choix", info($numEtudiant)[5]);
         if (isset($infos_delai)){$date_limit_choix = $infos_delai['data_limite'];}*/

        // return "Choisir un lit avant le".$date_limit_choix;
        return "Cliquer <a href='/profils/etudiants/codifier'>ICI</a> pour choisir un lit.";
    }
}

/*
 * Fonction pour verifier au Suppleant(e) que si son etudiant titulaire a valider son lit
 * ********************************************************************************
 */
function getChoixLitByTitulaireOfSuppleant($numEtudiantTitulaireOfSupp)
{
    global $connexion;
    $studentValidate = "SELECT * FROM codif_affectation JOIN codif_etudiant ON codif_etudiant.id_etu = codif_affectation.id_etu WHERE codif_etudiant.num_etu='$numEtudiantTitulaireOfSupp'";
    $infoValite = mysqli_query($connexion, $studentValidate);
    $data = $infoValite->fetch_assoc();
    // return $data;
    if ($data) {
        return 'Presentez-vous au service Hebergement pour valider votre codification!';
    } else {
        return "Votre Titulaire n'en pas encore faire le choix de son lit, veuiller lui patienter !!!";
    }
}

/*
 * Fonction pour verifier si le TITULAIRE a valider son hebergement
 * ********************************************************************************
 */
function getValidateLitByStudent($numEtudiant)
{
    global $connexion;
    $studentValidate = "SELECT * FROM codif_validation JOIN codif_affectation ON codif_validation.id_aff = codif_affectation.id_aff JOIN codif_etudiant ON codif_etudiant.id_etu = codif_affectation.id_etu WHERE codif_etudiant.num_etu='$numEtudiant'";
    $infoValite = mysqli_query($connexion, $studentValidate);
    $data = $infoValite->fetch_assoc();
    // return $data;
    if ($data) {
        // return "Aller payer " . getMontantPaye($numEtudiant) . "F (Caution + mensualité(s))";
        return 'Aller payer Mensualité(s) (+ Caution)';
    }
}

/*
 * Fonction pour verifier si le TITULAIRE a valider son hebergement
 * ********************************************************************************
 */
function getValidateLitByStudent2($numEtudiant)
{
    global $connexion;
    $studentValidate = "SELECT * FROM codif_validation JOIN codif_affectation ON codif_validation.id_aff = codif_affectation.id_aff JOIN codif_etudiant ON codif_etudiant.id_etu = codif_affectation.id_etu WHERE codif_etudiant.num_etu='$numEtudiant'";
    $infoValite = mysqli_query($connexion, $studentValidate);
    $data = $infoValite->fetch_assoc();
    // return $data;
    if ($data) {
        return 'Lit validé, veuillez à présent payer votre caution!';
    } else {
        return 'Presentez-vous au service Hebergement pour valider votre codification!';
    }
}

/*
 * Fonction pour verifier si le TITULAIRE a valider son hebergement
 * ********************************************************************************
 */
function getValidateLitByTitulaireOfSuppleant($numEtudiant)
{
    global $connexion;
    $studentValidate = "SELECT * FROM codif_validation JOIN codif_affectation ON codif_validation.id_aff = codif_affectation.id_aff JOIN codif_etudiant ON codif_etudiant.id_etu = codif_affectation.id_etu WHERE codif_etudiant.num_etu='$numEtudiant'";
    $infoValite = mysqli_query($connexion, $studentValidate);
    return $infoValite->fetch_assoc();
}

/*
 * Fonction pour afficher le motif de forclusion dun etudiant
 * ********************************************************************************
 */
function getMotifForclusion($id_etu)
{
    global $connexion;
    $studentValidate = "SELECT motif_manuel,dateTime_for,type FROM codif_forclusion WHERE id_etu='$id_etu'";
    $infoValite = mysqli_query($connexion, $studentValidate);
    // return $infoValite->fetch_assoc();
    $infoValite = mysqli_fetch_assoc($infoValite);
    $motif = $infoValite['motif_manuel'];
    $type = $infoValite['type'];
    $date = $infoValite['dateTime_for'];
    $date = changedateusfr($date);
    if ($type == 'auto') {
        $motif = 'Retard.';
    }
    return array($date, $motif);
}

/*
 * Fonction pour afficher le motif de forclusion dun etudiant
 * ********************************************************************************
 */
function getIdPay($id_etu)
{
    global $connexion;
    $req = "select id_paie from codif_paiement where id_val in (select id_val from codif_validation 
where id_aff in(select id_aff from codif_affectation where id_etu='$id_etu'))";
    $ex = mysqli_query($connexion, $req);

    if ($st = mysqli_fetch_assoc($ex)) {
        $id_paie = $st['id_paie'];
    } else {
        $id_paie = 0;
    }

    return $id_paie;
}

/*
 * Fonction pour verifier si le Suppleant(e) a valider son hebergement
 * ********************************************************************************
 */
function getValidateLitBySuppleant($numEtudiant)
{
    global $connexion;
    $studentValidate = "SELECT 
    codif_affectation.*,
    codif_etudiant.*,
    codif_lit.*,
    codif_loger.*,
    codif_validation.*,
    CASE WHEN codif_loger.id_val IS NOT NULL THEN 'Migré' ELSE 'Non migré' END AS etat_id_val 
FROM 
    codif_validation
    JOIN codif_affectation ON codif_validation.id_aff = codif_affectation.id_aff
    JOIN codif_etudiant ON codif_etudiant.id_etu = codif_affectation.id_etu
    JOIN codif_lit ON codif_lit.id_lit = codif_affectation.id_lit
    LEFT JOIN codif_loger ON codif_loger.id_val = codif_validation.id_val  
WHERE 
    codif_etudiant.num_etu = '$numEtudiant'";
    $infoValite = mysqli_query($connexion, $studentValidate);
    return $infoValite->fetch_assoc();
}

/*
 * Fonction pour verifier si le TITULAIRE a valider son hebergement
 * ********************************************************************************
 */
function getValidateLogerByStudent($numEtudiant)
{
    global $connexion;
    $studentValidatePaie = "SELECT * FROM `codif_loger` JOIN codif_paiement ON codif_paiement.id_paie = codif_loger.id_paie JOIN codif_validation ON codif_validation.id_val = codif_paiement.id_val JOIN codif_affectation ON codif_affectation.id_aff = codif_validation.id_aff JOIN codif_etudiant ON codif_etudiant.id_etu = codif_affectation.id_etu WHERE codif_etudiant.num_etu ='$numEtudiant'";
    $infoValitePaie = mysqli_query($connexion, $studentValidatePaie);
    $data = $infoValitePaie->fetch_assoc();
    // return $data;
    if ($data) {
        return 'Vous avez déjà logé!';
    } else {
        if (getValidatePaiementLitByStudent($numEtudiant)) {
            return getValidatePaiementLitByStudent($numEtudiant);
        } else {
            if (getValidateLitByStudent($numEtudiant)) {
                return getValidateLitByStudent($numEtudiant);
            } else {
                if (getChoixLitByStudent($numEtudiant)) {
                    return getChoixLitByStudent($numEtudiant);
                }
            }
        }
    }
}

/*
 * Fonction pour afficher le dernier delai selon le statut de l'etudiant
 * ********************************************************************************
 */
function getLastDelai($numEtudiant)
{
    global $connexion;
    $req_paiement = "SELECT * FROM codif_paiement JOIN codif_validation ON codif_validation.id_val = codif_paiement.id_val JOIN codif_affectation ON codif_affectation.id_aff = codif_validation.id_aff JOIN codif_etudiant ON codif_etudiant.id_etu = codif_affectation.id_etu WHERE codif_etudiant.num_etu ='$numEtudiant'";
    $ex_paiement = mysqli_query($connexion, $req_paiement);
    $data_paiement = $ex_paiement->fetch_assoc();
    if ($data_paiement) {
        // return "VOUS AVEZ DEJA codif_loger !!!";
    } else {
        $req_validation = "SELECT * FROM codif_validation JOIN codif_affectation ON codif_affectation.id_aff = codif_validation.id_aff JOIN codif_etudiant ON codif_etudiant.id_etu = codif_affectation.id_etu WHERE codif_etudiant.num_etu ='$numEtudiant'";
        $ex_validation = mysqli_query($connexion, $req_validation);
        $data_validation = $ex_validation->fetch_assoc();

        if ($data_validation) {
            $infoDelai = getAllDelai('paiement', info($numEtudiant)[5]);
            if ($infoDelai) {
                $last_date = $infoDelai['data_limite'];
                return $last_date;
            }
        } else {
            $req_affectation = "SELECT * FROM codif_affectation JOIN codif_etudiant ON codif_etudiant.id_etu = codif_affectation.id_etu WHERE codif_etudiant.num_etu ='$numEtudiant'";
            $ex_affectation = mysqli_query($connexion, $req_affectation);
            $data_affectation = $ex_affectation->fetch_assoc();

            if ($data_affectation) {
                $infoDelai = getAllDelai('validation', info($numEtudiant)[5]);
                if ($infoDelai) {
                    $last_date = $infoDelai['data_limite'];
                    return $last_date;
                }
            } else {
                $infoDelai = getAllDelai('choix', info($numEtudiant)[5]);
                if ($infoDelai) {
                    $last_date = $infoDelai['data_limite'];
                    return $last_date;
                }
            }
        }
    }
}

/*
 * Fonction pour verifier si le TITULAIRE au Suppleant(e) a valider son hebergement
 * ********************************************************************************
 */
function getValidateLogerByTitulaire($numEtudiant)
{
    global $connexion;
    $studentValidatePaie = "SELECT * FROM `codif_loger` JOIN codif_paiement ON codif_paiement.id_paie = codif_loger.id_paie JOIN codif_validation ON 
\tcodif_validation.id_val = codif_paiement.id_val JOIN codif_affectation ON codif_affectation.id_aff = codif_validation.id_aff JOIN codif_etudiant ON 
\tcodif_etudiant.id_etu = codif_affectation.id_etu WHERE codif_etudiant.num_etu ='$numEtudiant'";
    $infoValitePaie = mysqli_query($connexion, $studentValidatePaie);
    return $infoValitePaie->fetch_assoc();
}

/*
 * Fonction pour verifier si le Suppleant(e) a valider son hebergement
 * ********************************************************************************
 */
function getValidateLogerBySuppleant($numEtudiant)
{
    global $connexion;
    $studentValidatePaie = "SELECT * FROM `codif_loger` JOIN codif_validation ON codif_validation.id_val = codif_loger.id_val JOIN codif_affectation ON codif_affectation.id_aff = codif_validation.id_aff JOIN codif_etudiant ON codif_etudiant.id_etu = codif_affectation.id_etu JOIN codif_lit on codif_lit.id_lit = codif_affectation.id_lit WHERE codif_etudiant.num_etu ='$numEtudiant'";
    $infoValitePaie = mysqli_query($connexion, $studentValidatePaie);
    return $infoValitePaie->fetch_assoc();
}

/*
 * Fonction pour verifier si l'etudiant a valider son hebergement
 * ********************************************************************************
 */
function getValidatePaiementLitBySuppleant($numEtudiant)
{
    global $connexion;
    $studentValidatePaie = "SELECT * FROM codif_paiement JOIN codif_validation ON codif_paiement.id_val = codif_validation.id_val JOIN codif_affectation ON codif_affectation.id_aff = codif_validation.id_aff JOIN codif_etudiant ON codif_etudiant.id_etu =codif_affectation.id_etu WHERE codif_etudiant.num_etu='$numEtudiant'";
    $infoValitePaie = mysqli_query($connexion, $studentValidatePaie);
    return $infoValitePaie->fetch_assoc();
}

/*
 * Fonction pour verifier si l'etudiant a valider son paiement
 * ********************************************************************************
 */
function getValidatePaiementLitByStudent($numEtudiant)
{
    global $connexion;
    $studentValidatePaie = "SELECT * FROM codif_paiement JOIN codif_validation ON codif_paiement.id_val = codif_validation.id_val JOIN codif_affectation ON codif_affectation.id_aff = codif_validation.id_aff JOIN codif_etudiant ON codif_etudiant.id_etu =codif_affectation.id_etu WHERE codif_etudiant.num_etu='$numEtudiant'";
    $infoValitePaie = mysqli_query($connexion, $studentValidatePaie);
    $data = $infoValitePaie->fetch_assoc();
    // return $data;
    if ($data) {
        return 'Caution payée, approchez-vous du chef de residence pour loger!';
    } else {
        if (getValidateLitByStudent($numEtudiant)) {
            return getValidateLitByStudent($numEtudiant);
        } else {
            if (getChoixLitByStudent($numEtudiant)) {
                return getChoixLitByStudent($numEtudiant);
            }
        }
    }
}

/*
 * Ajouter dans la table codif_affectation lorsque l'etudiant choisi une lit
 * ********************************************************************************
 */
function addAffectation($lastValue, $idEtu)
{
    try {
        global $connexion;
        $requeteInsertAff = "INSERT INTO `codif_affectation` (`id_lit`, `id_etu`, `dateTime_aff`, `statut`) VALUES ($lastValue, $idEtu, NOW(), 'Attributaire')";
        $requeteEtu = $connexion->prepare($requeteInsertAff);
        return $requeteEtu->execute();
    } catch (Exception $e) {
        echo $e;
    }
}

function addAffectation_clt($lastValue, $idEtu)
{
    global $connexion;

    $stmt = $connexion->prepare("
        INSERT INTO codif_affectation 
        (id_lit, id_etu, dateTime_aff, statut) 
        VALUES (?, ?, NOW(), 'Attributaire')
    ");

    $stmt->bind_param('ii', $lastValue, $idEtu);
    $stmt->execute();
    $stmt->close();

    return true;
}

/*
 * Ajouter dans la table codif_affectation l'etudiant Suppleant(e) via son titulaire
 * ********************************************************************************
 */
function addAffectationOnSuppleant($lastValue, $idEtu)
{
    global $connexion;
    $requeteInsertAff = "INSERT INTO `codif_affectation` (`id_lit`, `id_etu`, `dateTime_aff`, `statut`) VALUES ($lastValue, $idEtu, NOW(), 'Suppleant(e)')";
    $requeteEtu = $connexion->prepare($requeteInsertAff);
    return $requeteEtu->execute();
}

/*
 * *********************************************************************************
 */

// Fonction de traitement du politique de confidentiellité
function addPolitiqueConf($idEtu)
{
    global $connexion;
    $requeteInsert = "INSERT INTO `codif_politique` (`id_etu`, `dateTime`) VALUES ($idEtu, NOW())";
    $sql = $connexion->prepare($requeteInsert);
    return $sql->execute();
}

/*
 * *********************************************************************************
 */

// Fonction qui me retourne le quota de n'importe quelle classe
function getQuotaClasse($classe, $sexe)
{
    global $connexion;
    $requeteQuotaClasse = "SELECT COUNT(*) FROM `codif_quota` JOIN codif_lit ON codif_lit.id_lit = codif_quota.id_lit_q WHERE `NiveauFormation` = '$classe' AND codif_lit.sexe = '$sexe'";
    $resultRequeteQuotaClasse = mysqli_query($connexion, $requeteQuotaClasse);
    return $resultRequeteQuotaClasse->fetch_assoc();
}

/*
 * Fonction d'affichage de la liste des etudiant beneficiaire de lit titulaire et quota
 * ********************************************************************************
 */
/*function getStatutStudentByQuota($quota, $classe, $sexe)
{
    global $connexion;
    $requeteListeClasse = "SELECT
    ce.id_etu,
    ce.prenoms,
    ce.nom,
    ce.num_etu,
    ce.sessionId,
    ce.moyenne,
    ce.niveauFormation,
    ce.etablissement,
    ce.departement,
    ce.dateNaissance,
    ce.lieuNaissance,
    ce.sexe,
    ce.nationalite,
    ce.numIdentite,
    ce.typeEtudiant,
    ce.niveau,
    ce.email_perso,
    ce.email_ucad,
    COALESCE(ranks.rang, 'N/A') AS rang,
    CASE
        WHEN ($quota=0) THEN 'Non Defini'
        WHEN cf.id_etu IS NOT NULL THEN 'Forclos(e)'
        WHEN ranks.rang <= $quota THEN 'Attributaire'
        WHEN ranks.rang <= $quota*2 THEN 'Suppleant(e)'
        ELSE 'Non Attributaire'
    END AS statut
FROM codif_etudiant ce
LEFT JOIN (
    SELECT
        id_etu,
        ROW_NUMBER() OVER (ORDER BY sessionId ASC, moyenne DESC, id_etu ASC) AS rang
    FROM codif_etudiant
    WHERE niveauFormation = '$classe'
      AND sexe = '$sexe'
      AND id_etu NOT IN (SELECT id_etu FROM codif_forclusion)
) ranks ON ce.id_etu = ranks.id_etu
LEFT JOIN codif_forclusion cf ON ce.id_etu = cf.id_etu
WHERE ce.niveauFormation = '$classe'
  AND ce.sexe = '$sexe'
ORDER BY moyenne DESC, id_etu ASC;
";
    $resultRequeteListeClasse = mysqli_query($connexion, $requeteListeClasse);
    return $resultRequeteListeClasse;
}*/

/*
 * function getStatutStudentByQuota($quota, $classe, $sexe)
 * {
 *     global $connexion;
 *     $requeteListeClasse = "SELECT
 *     ce.id_etu,
 *     ce.prenoms,
 *     ce.nom,
 *     ce.num_etu,
 *     ce.sessionId,
 *     ce.moyenne,
 *     ce.niveauFormation,
 *     ce.etablissement,
 *     ranks.rang,
 *     CASE
 * 	    WHEN ($quota=0) THEN 'Non Defini'
 *         WHEN cf.id_etu IS NOT NULL THEN 'Forclos(e)'
 *         WHEN ranks.rang <= $quota THEN 'Attributaire'
 *         WHEN ranks.rang <= $quota*2 THEN 'Suppleant(e)'
 *         ELSE 'Non Attributaire'
 *     END AS statut
 * FROM codif_etudiant ce
 * LEFT JOIN (
 *     SELECT
 *         id_etu,
 *         ROW_NUMBER() OVER (ORDER BY sessionId ASC, moyenne DESC, dateNaissance ASC, id_etu ASC) AS rang
 *     FROM codif_etudiant
 *     WHERE niveauFormation = '$classe'
 *       AND sexe = '$sexe'
 *       AND id_etu NOT IN (SELECT id_etu FROM codif_forclusion)
 * ) ranks ON ce.id_etu = ranks.id_etu
 * LEFT JOIN codif_forclusion cf ON ce.id_etu = cf.id_etu
 * WHERE ce.niveauFormation = '$classe'
 *   AND ce.sexe = '$sexe'
 * ORDER BY rang ASC;
 * ";
 *     $resultRequeteListeClasse = mysqli_query($connexion, $requeteListeClasse);
 *
 *     $students = [];
 *     while ($row = mysqli_fetch_assoc($resultRequeteListeClasse)) {
 *         $students[] = $row;
 *     }
 *     for ($i = 0; $i < count($students); $i++) {
 *         if ($students[$i]['statut'] == 'Forclos(e)') {
 *             // oder by desc
 *             $lit_forclus = getLitStudentForclu_archive($students[$i]['id_etu']);
 *             $id_etu_heritier = $students[$i]['id_etu'] + 1;
 *             // $if_choix_lit_heritier = getChoixLitByStudent_2($id_etu_heritier);
 *
 *             // var_dump($if_choix_lit_heritier); die;
 *             updateCodifAffectation($lit_forclus['id_lit'], $id_etu_heritier);
 *             // print_r('id_etu heritier => ' . $id_etu_heritier);
 *             // print_r('<br/>');
 *             // print_r('id_lit Forclos(e) =>' . $lit_forclus['id_lit']);
 *             // die;
 *         }
 *     }
 *     return $students;
 * }
 */

/*
 * Fonction d'affichage de la liste des etudiant beneficiaire de lit titulaire et quota
 * ********************************************************************************
 */

/*
 * function getStatutStudentByQuota($quota, $classe, $sexe)
 * {
 *     global $connexion;
 *     $requeteListeClasse = "SELECT
 *     ce.id_etu,
 *     ce.prenoms,
 *     ce.nom,
 *     ce.num_etu,
 *     ce.sessionId,
 *     ce.moyenne,
 *     ce.niveauFormation,
 *     ce.etablissement,
 *     ranks.rang,
 *     CASE
 * 	    WHEN ($quota=0) THEN 'Non Defini'
 *         WHEN cf.id_etu IS NOT NULL THEN 'Forclos(e)'
 *         WHEN ranks.rang <= $quota THEN 'Attributaire'
 *         WHEN ranks.rang <= $quota*2 THEN 'Suppleant(e)'
 *         ELSE 'Non Attributaire'
 *     END AS statut
 * FROM codif_etudiant ce
 * LEFT JOIN (
 *     SELECT
 *         id_etu,
 *         ROW_NUMBER() OVER (ORDER BY sessionId ASC, moyenne DESC, dateNaissance ASC, id_etu ASC) AS rang
 *     FROM codif_etudiant
 *     WHERE niveauFormation = '$classe'
 *       AND sexe = '$sexe'
 *       AND id_etu NOT IN (SELECT id_etu FROM codif_forclusion)
 * ) ranks ON ce.id_etu = ranks.id_etu
 * LEFT JOIN codif_forclusion cf ON ce.id_etu = cf.id_etu
 * WHERE ce.niveauFormation = '$classe'
 *   AND ce.sexe = '$sexe'
 * ORDER BY rang ASC;
 * ";
 *     $resultRequeteListeClasse = mysqli_query($connexion, $requeteListeClasse);
 *
 *     $students = [];
 *     while ($row = mysqli_fetch_assoc($resultRequeteListeClasse)) {
 *         $students[] = $row;
 *     }
 *     for ($i = 0; $i < count($students); $i++) {
 *         if ($students[$i]['statut'] == 'Forclos(e)') {
 *             // oder by desc
 *             $lit_forclus = getLitStudentForclu_archive($students[$i]['id_etu']);
 *             $id_etu_heritier = $quota + 1;
 *             // $if_choix_lit_heritier = getChoixLitByStudent_2($id_etu_heritier);
 *             // var_dump($lit_forclus);
 *             // die;
 *             if ($lit_forclus['id_lit'] != NULL) {
 *                 // var_dump("hello");
 *                 // die;
 *                 updateCodifAffectation($lit_forclus['id_lit'], $id_etu_heritier);
 *             } else {
 *                 deleteValidation($id_etu_heritier);
 *                 deleteAffectation($id_etu_heritier);
 *             }
 *             // print_r('id_etu heritier => ' . $id_etu_heritier);
 *             // print_r('<br/>');
 *             // print_r('id_lit forclus =>' . $lit_forclus['id_lit']);
 *             // die;
 *         }
 *     }
 *     return $students;
 * }
 */

/*
 * Fonction d'affichage de la liste des etudiant beneficiaire de lit titulaire et quota
 * ********************************************************************************
 */

/*
 * function getStatutStudentByQuota($quota, $classe, $sexe)
 * {
 *     global $connexion;
 *     $requeteListeClasse = "SELECT
 *     ce.id_etu,
 *     ce.prenoms,
 *     ce.nom,
 *     ce.num_etu,
 *     ce.sessionId,
 *     ce.moyenne,
 *     ce.niveauFormation,
 *     ce.etablissement,
 *     ranks.rang,
 *     CASE
 *         WHEN cf.id_etu IS NOT NULL THEN 'Forclos(e)'
 *         WHEN ranks.rang <= $quota THEN 'Attributaire'
 *         WHEN ranks.rang <= $quota*2 THEN 'Suppleant(e)'
 *         ELSE 'Non Attributaire'
 *     END AS statut
 * FROM codif_etudiant ce
 * LEFT JOIN (
 *     SELECT
 *         id_etu,
 *         ROW_NUMBER() OVER (ORDER BY sessionId ASC, moyenne DESC, dateNaissance ASC, id_etu ASC) AS rang
 *     FROM codif_etudiant
 *     WHERE niveauFormation = '$classe'
 *       AND sexe = '$sexe'
 *       AND id_etu NOT IN (SELECT id_etu FROM codif_forclusion)
 * ) ranks ON ce.id_etu = ranks.id_etu
 * LEFT JOIN codif_forclusion cf ON ce.id_etu = cf.id_etu
 * WHERE ce.niveauFormation = '$classe'
 *   AND ce.sexe = '$sexe'
 * ORDER BY rang ASC;
 * ";
 *     $resultRequeteListeClasse = mysqli_query($connexion, $requeteListeClasse);
 *
 *     $students = [];
 *     while ($row = mysqli_fetch_assoc($resultRequeteListeClasse)) {
 *         $students[] = $row;
 *     }
 *     $comp = 0;
 *     for ($i = 0; $i < count($students); $i++) {
 *         if ($students[$i]['statut'] == 'Forclos(e)') {
 *             $comp++;
 *         }
 *     }
 *     for ($i = 0; $i < count($students); $i++) {
 *         if ($students[$i]['statut'] == 'Forclos(e)') {
 *             $lit_forclus = getLitStudentForclu_archive();
 *             $i = $i + 1;
 *             $id_etu_heritier = ($quota + $comp);
 *             if ($lit_forclus['id_lit'] != NULL) {
 *                 updateCodifAffectation($lit_forclus['id_lit'], $id_etu_heritier);
 *             } else {
 *                 deleteValidation($id_etu_heritier);
 *                 deleteAffectation($id_etu_heritier);
 *             }
 *         }
 *     }
 *     return $students;
 * }
 */

/*
 * Fonction d'affichage de la liste des etudiant beneficiaire de lit titulaire et quota
 * ********************************************************************************
 */
function getStatutStudentByQuota($quota, $classe, $sexe)
{
    global $connexion;
    $requeteListeClasse = "SELECT 
    ce.id_etu, 
    ce.prenoms, 
    ce.nom, 
    ce.telephone, 
    ce.sexe, 
    ce.num_etu, 
    ce.dateNaissance, 
    ce.sessionId, 
    ce.moyenne, 
    ce.lieuNaissance, 
    ce.nationalite, 
    ce.email_perso, 
    ce.email_ucad, 
    ce.numIdentite, 
    ce.niveauFormation,
    ce.etablissement,
    ranks.rang, 
    CASE
    WHEN $quota = 0 THEN 'Non Defini'

    WHEN cf.id_etu IS NOT NULL THEN 'Forclos(e)'

    WHEN cl.statut IS NOT NULL THEN cl.statut

    WHEN ranks.rang <= $quota THEN 'Attributaire'
    WHEN ranks.rang <= ($quota * 2) THEN 'Suppleant(e)'
    ELSE 'Non Attributaire'
END AS statut
FROM codif_etudiant ce
LEFT JOIN (
    SELECT 
        id_etu, 
        ROW_NUMBER() OVER (ORDER BY sessionId ASC, moyenne DESC, id_etu ASC, dateNaissance ASC) AS rang 
    FROM codif_etudiant 
    WHERE niveauFormation = '$classe' 
      AND sexe = '$sexe' 
      AND id_etu NOT IN (SELECT id_etu FROM codif_forclusion)
) ranks ON ce.id_etu = ranks.id_etu
LEFT JOIN codif_forclusion cf ON ce.id_etu = cf.id_etu
LEFT JOIN codif_loger cl
    ON ce.id_etu = cl.id_etu
WHERE ce.niveauFormation = '$classe' 
  AND ce.sexe = '$sexe' 
ORDER BY rang ASC;
";
    $resultRequeteListeClasse = mysqli_query($connexion, $requeteListeClasse);

    $students = [];
    while ($row = mysqli_fetch_assoc($resultRequeteListeClasse)) {
        $students[] = $row;
    }
    return $students;
}

function getValidatePaiementLitBySuppleant2($id_etu)
{
    global $connexion;
    $studentValidatePaie = "SELECT codif_paiement.id_paie, codif_paiement.montant, codif_paiement.montant, codif_paiement.libelle, codif_paiement.dateTime_paie, codif_paiement.username_user, codif_etudiant.id_etu FROM codif_paiement JOIN codif_validation ON codif_paiement.id_val = codif_validation.id_val JOIN codif_affectation ON codif_affectation.id_aff = codif_validation.id_aff JOIN codif_etudiant ON codif_etudiant.id_etu =codif_affectation.id_etu WHERE codif_etudiant.id_etu='$id_etu'";
    $infoValitePaie = mysqli_query($connexion, $studentValidatePaie);
    return $infoValitePaie;
}

function getValidateLogerByTitulaire2($id_etu)
{
    global $connexion;
    $studentValidatePaie = "SELECT * FROM `codif_loger` JOIN codif_paiement ON codif_paiement.id_paie = codif_loger.id_paie JOIN codif_validation ON codif_validation.id_val = codif_paiement.id_val JOIN codif_affectation ON codif_affectation.id_aff = codif_validation.id_aff JOIN codif_etudiant ON codif_etudiant.id_etu = codif_affectation.id_etu WHERE codif_etudiant.id_etu ='$id_etu'";
    $infoValitePaie = mysqli_query($connexion, $studentValidatePaie);
    return $infoValitePaie->fetch_assoc();
}

/* * ********************************************************************************
Fonction pour recuperer l'id du lit de l'etudiant deja forclu dans la table archive
********************************************************************************* */
/*function getLitStudentForclu_archive($id_etu)
{
    global $connexion;
    $req_lit_student = "SELECT id_lit FROM codif_archive JOIN codif_etudiant ON codif_etudiant.id_etu = codif_archive.id_etu
    WHERE codif_etudiant.id_etu = '$id_etu'";
    $_get_req = $connexion->query($req_lit_student);
    return $_get_req->fetch_assoc();
}*/

/* * ********************************************************************************
Fonction pour recuperer l'id du lit de l'etudiant deja forclu dans la table archive
********************************************************************************* */
function getLitStudentForclu_archive()
{
    global $connexion;
    $req_lit_student = "SELECT DISTINCT (id_lit) FROM codif_archive JOIN codif_etudiant ON codif_etudiant.id_etu = codif_archive.id_etu 
\tWHERE id_lit IS NOT NULL ORDER BY id_archi DESC LIMIT 1";
    $_get_req = $connexion->query($req_lit_student);
    return $_get_req->fetch_assoc();
}

/*
 * Fonction d'affichage du statu de titulaire selon le rang de l'etudiant Suppleant(e)
 * ********************************************************************************
 */
function getStatutByOneStudentTitulaireOfSuppl($quota, $classe, $sexe, $rang)
{
    global $connexion;
    $requeteListeClasse = "SELECT prenoms, nom, num_etu, sessionId, moyenne, rang, CASE WHEN $quota=0 THEN 'Non Defini' WHEN rang <= $quota THEN 'Attributaire' WHEN rang <= $quota*2 THEN 'Suppleant(e)' ELSE 'Non Attributaire' END AS statut FROM ( SELECT prenoms, nom, num_etu, sessionId, moyenne, ROW_NUMBER() OVER (order by sessionId ASC, moyenne desc,id_etu asc) AS rang FROM codif_etudiant  WHERE id_etu not in (SELECT id_etu from codif_forclusion) and niveauFormation = '$classe' AND sexe = '$sexe' ) AS ranked_students WHERE rang = $rang-$quota ORDER BY rang";
    $resultRequeteListeClasse = mysqli_query($connexion, $requeteListeClasse);
    return $resultRequeteListeClasse->fetch_assoc();
}

/*
 * fonction d'affichage de la table delai
 * ********************************************************************************
 */
function getAllDelai($nature, $faculte)
{
    global $connexion;
    $requete = "SELECT * FROM codif_delai where nature ='$nature' AND faculte ='$faculte'";
    $resultRequete = mysqli_query($connexion, $requete);
    return $resultRequete->fetch_assoc();
}

/*
 * fonction pour recuperer le lit choisi par l'etudiant selon son numero carte
 * ********************************************************************************
 */
function isIndivLitStudent($numEtudiant)
{
    try {
        global $connexion;
        $studentValidate = "SELECT * FROM codif_affectation JOIN codif_etudiant ON codif_etudiant.id_etu = codif_affectation.id_etu JOIN 
\tcodif_lit ON codif_lit.id_lit = codif_affectation.id_lit WHERE codif_etudiant.num_etu='$numEtudiant'";
        $infoValite = mysqli_query($connexion, $studentValidate);
        $data = $infoValite->fetch_assoc();
        if ($data['indiv'] == 1) {
            return 'oui';
        } else {
            return 'non';
        }
    } catch (Exception $e) {
    }
}

/*
 * supprimer validation lit de l'etudiant forclos
 * ********************************************************************************
 */
function deleteValidation($id_etu)
{
    global $connexion;
    $requeteFor0 = "DELETE FROM codif_validation WHERE EXISTS (SELECT $id_etu FROM codif_affectation JOIN codif_etudiant ON codif_affectation.id_etu = codif_etudiant.id_etu WHERE codif_validation.id_aff = codif_affectation.id_aff AND codif_etudiant.id_etu = '$id_etu')";
    $b = $connexion->prepare($requeteFor0);
    $b->execute();
}

/*
 * supprimer codif_affectation lit de l'etudiant forclos
 * ********************************************************************************
 */
function deleteAffectation($id_etu)
{
    global $connexion;
    $requeteFor1 = "DELETE FROM codif_affectation WHERE id_aff = (SELECT id_aff FROM codif_affectation JOIN codif_etudiant ON codif_affectation.id_etu = codif_etudiant.id_etu AND codif_etudiant.id_etu = '$id_etu')";
    $c = $connexion->prepare($requeteFor1);
    $c->execute();
}

/*
 * Verifier si l'etudiant est deja forclos
 * ********************************************************************************
 */
function getIsForclu($num_etu)
{
    global $connexion;
    $studentValidate = "SELECT * FROM `codif_forclusion` JOIN codif_etudiant ON codif_etudiant.id_etu =codif_forclusion.id_etu WHERE codif_etudiant.num_etu = '$num_etu'";
    $infoValite = mysqli_query($connexion, $studentValidate);
    $data = $infoValite->fetch_assoc();
    return $data;
}

/*
 * Recuperer le quota et statut à partir de num_etu
 * ********************************************************************************
 */
function quota_statut($login)
{
    // Mettre en majuscule et eliminer lespace eventuel
    $login = strtoupper($login);
    $login = str_replace(' ', '', $login);

    $quota = 0;
    $statut = 'indefini';
    $rang = rang($login);
    $niveauFormation = info($login)['7'];
    $sexe = info($login)['11'];
    $id_etu = info($login)['15'];

    global $link;
    $rr3 = "select count(id_quota) as quota from codif_quota JOIN codif_lit ON codif_lit.id_lit = codif_quota.id_lit_q where niveauFormation='$niveauFormation' 
and codif_lit.sexe = '$sexe' ";
    $ee3 = mysqli_query($link, $rr3);
    $ss3 = mysqli_fetch_array($ee3);
    if ($ss3['quota']) {
        $quota = $ss3['quota'];
    }

    if ($quota == 0) {
        $statut = 'Indefini';
    } else {
        if (getEtuForclu($id_etu) > 0) {
            $statut = 'Forclos(e)';
        } elseif ($rang > 0 and $rang <= $quota) {
            $statut = 'Attributaire';
        } elseif ($rang <= (2 * $quota) and $rang > $quota) {
            $statut = 'Suppleant(e)';
        } elseif ($rang > 2 * $quota) {
            $statut = 'Non Attributaire';
        }
    }
    return array($quota, $statut, $rang);
    // //////////Fin
}

/*
 * Verifier si au moins un etudiant est Forclos(e)
 * ********************************************************************************
 */
function getEtuForclu($id_etu)
{
    global $connexion;
    $studentValidate = "SELECT * FROM codif_forclusion where codif_forclusion.id_etu='$id_etu'";
    $infoValite = mysqli_query($connexion, $studentValidate);
    $g = mysqli_num_rows($infoValite);
    return $g;
}

/*
 * fonction d'affichage de toute les delais
 * ********************************************************************************
 */
function getDelai()
{
    global $connexion;
    $requete = 'SELECT DISTINCT(faculte) FROM codif_delai';
    $resultRequete = mysqli_query($connexion, $requete);
    return $resultRequete;
}

/*
 * Recupere les infos de la forclusion automatique
 * ********************************************************************************
 */
function getAllForclu($niveauFormation, $sexe)
{
    global $connexion;
    $studentValidate = "SELECT DISTINCT * FROM codif_forclusion JOIN codif_etudiant ON codif_etudiant.id_etu = codif_forclusion.id_etu 
\tJOIN codif_delai on codif_delai.id_delai = codif_forclusion.id_del WHERE codif_etudiant.niveauFormation='$niveauFormation' AND codif_etudiant.sexe='$sexe'";
    $infoValite = mysqli_query($connexion, $studentValidate);
    return $infoValite;
}  // ANNULE CAR NE COMPTE PAS LES FORCLU AUTO (SANS ID_DELAI)

/*
 * Compte toutes les lignes de la table forclusion
 * ********************************************************************************
 */
function getAllForclu_manuel($niveauFormation, $sexe)
{
    global $connexion;
    $studentValidate = "SELECT * FROM codif_forclusion JOIN codif_etudiant ON codif_etudiant.id_etu = codif_forclusion.id_etu 
\tWHERE codif_etudiant.niveauFormation='$niveauFormation' AND codif_etudiant.sexe='$sexe'";
    $infoValite = mysqli_query($connexion, $studentValidate);
    return $infoValite;
}

function getNiveauFormationAndSexeLitByQuota()
{
    global $connexion;
    $requete = 'SELECT  DISTINCT (NiveauFormation), (codif_lit.sexe) FROM `codif_quota` JOIN codif_lit on codif_lit.id_lit = codif_quota.id_lit_q';
    $result = $connexion->query($requete);
    return $result;
}

/*
 * Fonction permet de tester si l'etudiant est Forclos(e) ou pas
 * ********************************************************************************
 */
function isEtudiantForclus($id_etu)
{
    global $connexion;
    $req = "SELECT * FROM codif_forclusion JOIN codif_etudiant ON codif_etudiant.id_etu = codif_forclusion.id_etu WHERE codif_etudiant.id_etu = '$id_etu'";
    $result = $connexion->query($req);
    return $result->fetch_assoc();
}

function isEtudiantForclus_2($num_etu)
{
    global $connexion;
    $req = "SELECT * FROM codif_forclusion JOIN codif_etudiant ON codif_etudiant.id_etu = codif_forclusion.id_etu WHERE codif_etudiant.num_etu = '$num_etu'";
    $result = $connexion->query($req);
    return $result->fetch_assoc();
}

/*
 * Fonction pour recuperer le tableaux d'etudiants Attributaire, Suppleant(e), non-Attributaire et forclos
 * ********************************************************************************
 */
/*function getAllDatastudentStatus($quota, $classe, $sexe)
{
    $listeClasse = getStatutStudentByQuota($quota, $classe, $sexe);
    $tableau_data_etudiant = [];
    $i = 0;
    while ($row = mysqli_fetch_array($listeClasse)) {
        $tableau_data_etudiant[$i] = $row;
        $i++;
    }
    return $tableau_data_etudiant;
}*/

function getAllDatastudentStatus($quota, $classe, $sexe)
{
    $listeClasse = getStatutStudentByQuota($quota, $classe, $sexe);
    // $tableau_data_etudiant = [];
    // $i = 0;
    // while ($row = mysqli_fetch_array($listeClasse)) {
    //     $tableau_data_etudiant[$i] = $row;
    //     $i++;
    // }
    return $listeClasse;
}

function getAllDatastudentStatus_2($quota, $classe, $sexe, $rang)
{
    $listeClasse = getStatutStudentByQuota($quota, $classe, $sexe);
    for ($i = 0; $i < count($listeClasse); $i++) {
        if ($listeClasse[$i]['rang'] == $rang) {
            return $listeClasse[$i];
        }
    }
}

/* *********************************************************************************
Fonction pour recuperer les données d'un etudiants Attributaire, Suppleant(e), non-Attributaire et forclos
********************************************************************************* */
function getOnestudentStatus($quota, $classe, $sexe, $num_etu)
{
    $row_one_student = getAllDatastudentStatus($quota, $classe, $sexe);
    for ($i = 0; $i < count($row_one_student); $i++) {
        if ($num_etu == $row_one_student[$i]['num_etu']) {
            return $row_one_student[$i];
        }
    }
}

/* * ********************************************************************************
Fonction pour recuperer les données de l'Attributaire selon le rang du Suppleant(e)
********************************************************************************* */
function getOneTitulaireBySuppleant($quota, $classe, $sexe, $rang)
{
    $row_one_student = getAllDatastudentStatus($quota, $classe, $sexe);
    for ($i = 0; $i < count($row_one_student); $i++) {
        if ($row_one_student[$i]['rang'] == $rang - $quota) {
            return $row_one_student[$i];
        }
    }
}

/* * ********************************************************************************
Fonction stocké toutes les informations de l'etudiant forclos manuellement
********************************************************************************* */
// function addArchiveManuel($id_etu, $username_user = null)
// {
//     try {
//         global $connexion;
//         $req_add_archive = "INSERT INTO codif_archive (`id_etu`, `dateTime_sys`, `username_user`) VALUES ('$id_etu', NOW(), '$username_user')";
//         $insert_archive = $connexion->prepare($req_add_archive);
//         return $insert_archive->execute();
//     } catch (mysqli_sql_exception $e) {
//         echo $e->getMessage();
//     }
// }

/* * ********************************************************************************
Fonction pour recuperer l'id du lit et la date de choix du lit de l'etudiant deja forclos
********************************************************************************* */
function getLitStudentForclu($id_etu)
{
    global $connexion;
    $req_lit_student = "SELECT id_lit, dateTime_aff FROM codif_affectation JOIN codif_etudiant ON codif_etudiant.id_etu = codif_affectation.id_etu WHERE codif_etudiant.id_etu = '$id_etu'";
    $_get_req = $connexion->query($req_lit_student);
    return $_get_req->fetch_assoc();
}

/* *********************************************************************************
Fonction pour recuperer la date de validation de l'etudiant deja forclos
********************************************************************************* */
function getDateValStudentForclu($id_etu)
{
    global $connexion;
    $req_lit_student = "SELECT dateTime_val FROM codif_validation JOIN codif_affectation ON codif_affectation.id_aff = codif_validation.id_aff JOIN codif_etudiant ON codif_etudiant.id_etu = codif_affectation.id_etu WHERE codif_etudiant.id_etu = '$id_etu'";
    $_get_req = $connexion->query($req_lit_student);
    return $_get_req->fetch_assoc();
}

/* *********************************************************************************
Fonction pour recuperer la table facturation des lits
********************************************************************************* */
function getFacturation($indiv)
{
    global $connexion;
    $req_facturation_lit = "SELECT * FROM `codif_facturation` WHERE indiv= '$indiv'";
    $_get_req = $connexion->query($req_facturation_lit);
    return $_get_req->fetch_assoc();
}

/* *********************************************************************************
Fonction pour calculer le montant à payer
********************************************************************************* */
function getMontantPaye($numEtudiant)
{
    /*$faculte =  info($numEtudiant)[5];
    if(getAllDelai('depart', $faculte)['data_limite']){
    $dateDepart = getAllDelai('depart', $faculte)['data_limite'];
    $date_debut = DateTime::createFromFormat('Y-m-d', dateFromat($dateDepart));
    $date_sys = DateTime::createFromFormat('Y-m-d', dateFromat(date("Y-n-j")));
    $nbr_mois = $date_debut->diff($date_sys);
    $nbr_mois = $nbr_mois->format('%m');}*/

    if (!getValidatePaiementLitBySuppleant($numEtudiant)) {
        if (isIndivLitStudent($numEtudiant) == 'non') {
            $montant = 5000 + getFacturation(false)['montant'];
            return $montant;
        } else {
            $montant = 5000 + getFacturation(true)['montant'];
            return $montant;
        }
    } else {
        if (isIndivLitStudent($numEtudiant) == 'non') {
            $montant = getFacturation(false)['montant'];
            return $montant;
        } else {
            $montant = getFacturation(true)['montant'];
            return $montant;
        }
    }
}

/* *********************************************************************************
Fonction pour calculer le le prix mensuel du lit
********************************************************************************* */
function getPrixMensuelLit($numEtudiant)
{
    if (isIndivLitStudent($numEtudiant) == 'non') {
        $montant = getFacturation(false)['montant'];
        return $montant;
    } else {
        $montant = getFacturation(true)['montant'];
        return $montant;
    }
}

/* *********************************************************************************
Recuperer les paiments dans un intervalle de date données
********************************************************************************* */
function getPaiementWithDateInterval($date_debut, $date_fin, $username)
{
    global $connexion;

    $sql = "
    SELECT *
    FROM (
        SELECT
            ce.num_etu,
            ce.nom,
            ce.prenoms,
            pc.dateTime_paie,
            pc.montant,
            pc.quittance,
            pc.libelle,
            pc.id_paie
        FROM codif_etudiant ce
        INNER JOIN codif_affectation a
            ON ce.id_etu = a.id_etu
        INNER JOIN codif_validation vl
            ON a.id_aff = vl.id_aff
        INNER JOIN codif_paiement pc
            ON pc.id_val = vl.id_val
        WHERE pc.username_user = ?

        UNION ALL

        SELECT
            ce.num_etu,
            ce.nom,
            ce.prenoms,
            pc.dateTime_paie,
            pc.montant,
            pc.quittance,
            pc.libelle,
            pc.id_paie
        FROM codif_etudiant ce
        INNER JOIN codif_paiement pc
            ON pc.id_etu = ce.id_etu
        WHERE pc.username_user = ?
          AND (pc.id_val IS NULL OR pc.id_val = 0)
    ) t
    WHERE 1=1
    ";

    $params = [$username, $username];
    $types = 'ss';

    if (!empty($date_debut)) {
        $sql .= " AND t.dateTime_paie >= ?";
        $params[] = $date_debut . ' 00:00:00';
        $types .= 's';
    }

    if (!empty($date_fin)) {
        $sql .= " AND t.dateTime_paie <= ?";
        $params[] = $date_fin . ' 23:59:59';
        $types .= 's';
    }

    $sql .= " ORDER BY t.id_paie DESC";

    $stmt = $connexion->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();

    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    $stmt->close();

    return $data;
}

// Fonction permettant de recuperer toustes les infos de la table etudiant
function info($login)
{
    // Recherche des infos de l'etudiant
    global $connexion;
    $rr = "select * from codif_etudiant where num_etu='$login'";
    $ee = mysqli_query($connexion, $rr);
    $ss = mysqli_fetch_array($ee);

    $numIdentite = $ss['numIdentite'];
    $dateNaissance = $ss['dateNaissance'];
    $lieuNaissance = $ss['lieuNaissance'];
    $nom = $ss['nom'];
    $prenoms = $ss['prenoms'];
    $etablissement = $ss['etablissement'];
    $departement = $ss['departement'];
    $typeEtudiant = $ss['typeEtudiant'];
    $sessionId = $ss['sessionId'];
    $niveauFormation = $ss['niveauFormation'];
    $moyenne = $ss['moyenne'];
    $sexe = $ss['sexe'];
    $email = $ss['email_ucad'];
    $email2 = $ss['email_perso'];
    $id_etu = $ss['id_etu'];
    $telephone = $ss['telephone'];
    // $email="moulaye.camara@ucad.edu.sn";

    // /////////Recuperer le 1er caractere de la cni pour determiner le sexe
    $sexeL = '';
    if ($sexe == 'G' or $sexe == 'M') {
        $sexeL = 'Garçons';
    }
    if ($sexe == 'F') {
        $sexeL = 'Filles';
    }
    // //////////Fin

    return array($numIdentite, $dateNaissance, $lieuNaissance, $nom, $prenoms, $etablissement, $departement, $niveauFormation, $moyenne, $typeEtudiant, $sessionId, $sexe, $sexeL, $email, $email2, $id_etu, $telephone);
    // fin
}

function info2($login)
{
    // Recherche des infos du user
    global $connexion_user;
    $rr = "select password_user,type_mdp from codif_user where username_user='$login'";
    $ee = mysqli_query($connexion_user, $rr);
    $ss = mysqli_fetch_array($ee);

    $mdp = $ss['password_user'];
    $type_mdp = $ss['type_mdp'];

    return array($mdp, $type_mdp);
    // fin
}

function info4($id)
{
    // Recherche des infos de l'etudiant
    global $connexion;
    $rr = "select * from codif_etudiant where id_etu='$id'";
    $ee = mysqli_query($connexion, $rr);
    $ss = mysqli_fetch_array($ee);

    $id_etu = $ss['id_etu'];
    $numIdentite = $ss['numIdentite'];
    $num_etu = $ss['num_etu'];
    $dateNaissance = $ss['dateNaissance'];
    $lieuNaissance = $ss['lieuNaissance'];
    $nom = $ss['nom'];
    $prenoms = $ss['prenoms'];
    $etablissement = $ss['etablissement'];
    $departement = $ss['departement'];
    $typeEtudiant = $ss['typeEtudiant'];
    $sessionId = $ss['sessionId'];
    $niveauFormation = $ss['niveauFormation'];
    $moyenne = $ss['moyenne'];
    $sexe = $ss['sexe'];
    $email = $ss['email_ucad'];
    $email2 = $ss['email_perso'];
    // /////////Recuperer le 1er caractere de la cni pour determiner le sexe
    $sexeL = '';
    if ($sexe == 'G' or $sexe == 'M') {
        $sexeL = 'Garçons';
    }
    if ($sexe == 'F') {
        $sexeL = 'Filles';
    }
    return array($id_etu, $numIdentite, $num_etu, $dateNaissance, $lieuNaissance, $nom, $prenoms, $etablissement, $departement, $niveauFormation, $moyenne, $typeEtudiant, $sessionId, $sexe, $sexeL, $email, $email2);
}

/*
 * Fonction d'affichage de la situation de l'etudiant (paiement caution et mensualité)
 * ********************************************************************************
 */
/*function getAllSituation($num_etu)
{
    global $connexion;
    $requeteSelect = "SELECT * FROM `codif_paiement` JOIN `codif_validation` ON codif_validation.id_val = codif_paiement.id_val JOIN `codif_affectation`
    on codif_affectation.id_aff = codif_validation.id_aff JOIN `codif_etudiant` on codif_etudiant.id_etu = codif_affectation.id_etu WHERE codif_etudiant.num_etu='$num_etu'";
    $resulteRequete = $connexion->query($requeteSelect);
    return $resulteRequete;
}*/

/*
 * Fonction d'affichage de la situation de l'etudiant (paiement caution et mensualité)
 * ********************************************************************************
 */
function getAllSituation_2($num_etu)
{
    global $connexion;
    $requeteSelect = 
    "SELECT * FROM `codif_paiement` 
        JOIN `codif_validation` ON codif_validation.id_val = codif_paiement.id_val 
        JOIN `codif_affectation`ON codif_affectation.id_aff = codif_validation.id_aff 
        JOIN `codif_etudiant` ON codif_etudiant.id_etu = codif_affectation.id_etu 
        JOIN `codif_lit` ON codif_lit.id_lit = codif_affectation.id_lit 
        JOIN `codif_etudiant` AS etu ON etu.id_etu = codif_paiement.id_etu 
    WHERE codif_etudiant.num_etu='$num_etu' ORDER BY id_paie ASC";
    $resulteRequete = $connexion->query($requeteSelect);
    return $resulteRequete;
}

function getAllSituation($num_etu)
{
    global $connexion;

    $requeteSelect = "
        SELECT *
        FROM codif_paiement
        LEFT JOIN codif_validation
            ON codif_validation.id_val = codif_paiement.id_val
        LEFT JOIN codif_affectation
            ON codif_affectation.id_aff = codif_validation.id_aff
        LEFT JOIN codif_etudiant
            ON codif_etudiant.id_etu = codif_affectation.id_etu
        LEFT JOIN codif_lit
            ON codif_lit.id_lit = codif_affectation.id_lit
        LEFT JOIN codif_etudiant AS etu
            ON etu.id_etu = codif_paiement.id_etu
        WHERE (
            codif_etudiant.num_etu = '$num_etu'
            OR etu.num_etu = '$num_etu'
        )
        ORDER BY codif_paiement.id_paie ASC
    ";

    $resulteRequete = $connexion->query($requeteSelect);
    return $resulteRequete;
}

/*
 * Fonction de calcul du total des paiements de l'etudiant (caution et mensualité)
 * ********************************************************************************
 */
function getTotalPaye($num_etu)
{
    global $connexion;
    $requeteSelect = "SELECT sum(montant) as total FROM `codif_paiement` JOIN `codif_validation` ON codif_validation.id_val = codif_paiement.id_val JOIN `codif_affectation`
\ton codif_affectation.id_aff = codif_validation.id_aff JOIN `codif_etudiant` on codif_etudiant.id_etu = codif_affectation.id_etu JOIN `codif_lit` 
\ton codif_lit.id_lit = codif_affectation.id_lit WHERE codif_etudiant.num_etu='$num_etu' ORDER BY id_paie ASC";

    $exx = mysqli_query($connexion, $requeteSelect);
    $row = mysqli_fetch_array($exx);
    $montant_total = $row['total'];
    return $montant_total;
}

/* *********************************************************************************
Fonction pour calculer le nombre de mois total à payer par l'etudiant
********************************************************************************* */
function getNbreMois($numEtudiant)
{
    $dateDepart = getAllDelai('depart', info($numEtudiant)[5]);
    if (!$dateDepart = 'NULL') {
        $date_debut = DateTime::createFromFormat('Y-m-d', dateFromat($dateDepart['data_limite']));
        $date_sys = DateTime::createFromFormat('Y-m-d', dateFromat(date('Y-n-j')));
        $nbr_mois = $date_debut->diff($date_sys);
        $nbr_mois = $nbr_mois->format('%m');
        return $nbr_mois;
    }
}

Function dateFormat($date)
{
    return date('Y-m-d', strtotime($date));
}

/* *********************************************************************************
Fonction pour calculer le nombre de mois total à payer par l'etudiant
********************************************************************************* */
function getNbreMois2($numEtudiant)
{
    $dateDepart = getAllDelai('depart', info($numEtudiant)[5]);
    if ($dateDepart != NULL) {
        $date_debut = DateTime::createFromFormat('Y-m-d', dateFormat($dateDepart['data_limite']));
        $date_sys = DateTime::createFromFormat('Y-m-d', dateFormat(date('Y-n-j')));

        // Recuperation date fin codification du niveauFormation de l'etudiant
        $rdate_fin = getAllDelai('fermeture', info($numEtudiant)[5]);
        $date_fin0 = dateFromat($rdate_fin['data_limite']);
        // $date_fin = date("m", strtotime($date_fin0));
        $date_fin = DateTime::createFromFormat('Y-m-d', dateFormat($date_fin0));

        // Limiter la facturation à la date de fermeture
        if ($date_sys > $date_fin) {
            $date_sys = $date_fin;
        }

        // $nbr_mois = $date_debut->diff($date_sys);
        // $nbr_mois = $nbr_mois->format('%m');
        // return $nbr_mois + 1;

        $interval = $date_debut->diff($date_sys);

        $nbr_mois = ($interval->y * 12) + $interval->m;
        $nbrs_reduction = getNbrsReduction($numEtudiant);

        $nbr_mois = $nbr_mois - $nbrs_reduction;

        return $nbr_mois + 1;
    }
}

/* *********************************************************************************
Fonction pour calculer le nombre de mois entre deux dates
********************************************************************************* */
function calcul_nbreMois($debut)
{
    $datesys = date('Y-m-d');

    $ts1 = strtotime($debut);
    $ts2 = strtotime($datesys);

    $year1 = date('Y', $ts1);
    $year2 = date('Y', $ts2);

    $month1 = date('m', $ts1);
    $month2 = date('m', $ts2);

    $nbrmois = (($year2 - $year1) * 12) + ($month2 - $month1) + 1;

    return $nbrmois;
}

/*
 * Fonction pour recuperer le mois en chiffre a traver le nom du mois en lettre, puis le concataine avec l'annee en cour et le premier de chaque mois
 * ********************************************************************************
 */
function getMois($mois)
{
    $annee = array(
        '01' => 'JANVIER',
        '02' => 'FEVRIER',
        '03' => 'MARS',
        '04' => 'AVRIL',
        '05' => 'MAI',
        '06' => 'JUIN',
        '07' => 'JUILLET',
        '08' => 'AOUT',
        '09' => 'SEPTEMBRE',
        '10' => 'OCTOBRE',
        '11' => 'NOVEMBRE',
        '12' => 'DECEMBRE',
    );
    $date_sys = date('Y', strtotime(date('Y-m-d')));
    foreach ($annee as $cle => $value) {
        if ($value == $mois) {
            if ($cle < 9) {
                return $date_sys . '-' . $cle . '-01';
            } else {
                return $date_sys - 1 . '-' . $cle . '-01';
            }
        }
    }
}

/* *********************************************************************************
Compter le nombre de mots dans une chaîne de caractères tout en ignorant les espaces et les virgules
********************************************************************************* */
function countWords($string)
{
    // Utiliser preg_split pour séparer les mots en ignorant les espaces et les virgules
    $words = preg_split('/[\s,]+/', trim($string), -1, PREG_SPLIT_NO_EMPTY);
    // Compter le nombre de mots
    return count($words);
}

function getLitsBySexeAndNiveau2()
{
    global $connexion;

    // Requête SQL pour récupérer le nombre de lits par sexe, niveau et établissement
    $sql = '
    SELECT 
        e.niveauFormation,
        e.etablissement,
        l.sexe,
        COUNT(DISTINCT q.id_lit_q) AS nombre_lits
    FROM 
        codif_etudiant e
    INNER JOIN 
        codif_quota q ON e.niveauFormation = q.niveauFormation
    INNER JOIN 
        codif_lit l ON q.id_lit_q = l.id_lit
    GROUP BY 
        e.niveauFormation, e.etablissement, l.sexe;  
    ';

    // Préparation de la requête
    $stmt = $connexion->prepare($sql);

    // Vérification de la préparation de la requête
    if ($stmt === false) {
        die('Erreur de préparation de la requête : ' . htmlspecialchars($connexion->error));
    }

    // Exécution de la requête
    if (!$stmt->execute()) {
        die("Erreur lors de l'exécution de la requête : " . htmlspecialchars($stmt->error));
    }

    // Récupération des résultats
    $result = $stmt->get_result();

    // Vérification de la récupération des résultats
    if ($result === false) {
        die('Erreur lors de la récupération des résultats : ' . htmlspecialchars($stmt->error));
    }

    // Tableau pour stocker les données
    $lits = [];
    $totalGarçons = 0;
    $totalFilles = 0;
    $totalLits = 0;

    // Tableau pour stocker les totaux par établissement
    $totauxParEtablissement = [];

    // Stockage des résultats dans le tableau
    while ($row = $result->fetch_assoc()) {
        $niveau = $row['niveauFormation'];
        $etablissement = $row['etablissement'];
        $sexe = $row['sexe'];
        $nombre_lits = $row['nombre_lits'];

        // Initialisation si le niveau et l'établissement n'existent pas encore dans le tableau
        if (!isset($lits[$etablissement][$niveau])) {
            $lits[$etablissement][$niveau] = ['garçons' => 0, 'filles' => 0, 'total' => 0];
        }

        // Ajout du nombre de lits selon le sexe
        if ($sexe === 'G') {
            $lits[$etablissement][$niveau]['garçons'] += $nombre_lits;
            $totalGarçons += $nombre_lits;

            // Accumuler le total par établissement
            if (!isset($totauxParEtablissement[$etablissement])) {
                $totauxParEtablissement[$etablissement] = ['garçons' => 0, 'filles' => 0];
            }
            $totauxParEtablissement[$etablissement]['garçons'] += $nombre_lits;
        } elseif ($sexe === 'F') {
            $lits[$etablissement][$niveau]['filles'] += $nombre_lits;
            $totalFilles += $nombre_lits;

            // Accumuler le total par établissement
            if (!isset($totauxParEtablissement[$etablissement])) {
                $totauxParEtablissement[$etablissement] = ['garçons' => 0, 'filles' => 0];
            }
            $totauxParEtablissement[$etablissement]['filles'] += $nombre_lits;
        }

        // Calcul du total
        $lits[$etablissement][$niveau]['total'] = $lits[$etablissement][$niveau]['garçons'] + $lits[$etablissement][$niveau]['filles'];
        $totalLits = $totalGarçons + $totalFilles;  // Calcul du total général
    }

    // Retourner le tableau de résultats et les totaux
    return [
        'lits' => $lits,
        'totaux' => [
            'garçons' => $totalGarçons,
            'filles' => $totalFilles,
            'total' => $totalLits,
        ],
        'totauxParEtablissement' => $totauxParEtablissement,
    ];
}

function getFacPcs($fac)
{
    global $connexion;

    // Requête SQL pour récupérer le nombre de lits par sexe, niveau et établissement
    $sql = "
    SELECT 
        e.niveauFormation,
        e.etablissement,
        l.sexe,
        COUNT(DISTINCT q.id_lit_q) AS nombre_lits
    FROM 
        codif_etudiant e
    INNER JOIN 
        codif_quota q ON e.niveauFormation = q.niveauFormation
    INNER JOIN 
        codif_lit l ON q.id_lit_q = l.id_lit
    GROUP BY 
        e.niveauFormation, e.etablissement, l.sexe HAVING e.etablissement like '$fac%'  
    ";

    // Préparation de la requête
    $stmt = $connexion->prepare($sql);

    // Vérification de la préparation de la requête
    if ($stmt === false) {
        die('Erreur de préparation de la requête : ' . htmlspecialchars($connexion->error));
    }

    // Exécution de la requête
    if (!$stmt->execute()) {
        die("Erreur lors de l'exécution de la requête : " . htmlspecialchars($stmt->error));
    }

    // Récupération des résultats
    $result = $stmt->get_result();

    // Vérification de la récupération des résultats
    if ($result === false) {
        die('Erreur lors de la récupération des résultats : ' . htmlspecialchars($stmt->error));
    }

    // Tableau pour stocker les données
    $lits = [];
    $totalGarçons = 0;
    $totalFilles = 0;
    $totalLits = 0;

    // Tableau pour stocker les totaux par établissement
    $totauxParEtablissement = [];

    // Stockage des résultats dans le tableau
    while ($row = $result->fetch_assoc()) {
        $niveau = $row['niveauFormation'];
        $etablissement = $row['etablissement'];
        $sexe = $row['sexe'];
        $nombre_lits = $row['nombre_lits'];

        // Initialisation si le niveau et l'établissement n'existent pas encore dans le tableau
        if (!isset($lits[$etablissement][$niveau])) {
            $lits[$etablissement][$niveau] = ['garçons' => 0, 'filles' => 0, 'total' => 0];
        }

        // Ajout du nombre de lits selon le sexe
        if ($sexe === 'G') {
            $lits[$etablissement][$niveau]['garçons'] += $nombre_lits;
            $totalGarçons += $nombre_lits;

            // Accumuler le total par établissement
            if (!isset($totauxParEtablissement[$etablissement])) {
                $totauxParEtablissement[$etablissement] = ['garçons' => 0, 'filles' => 0];
            }
            $totauxParEtablissement[$etablissement]['garçons'] += $nombre_lits;
        } elseif ($sexe === 'F') {
            $lits[$etablissement][$niveau]['filles'] += $nombre_lits;
            $totalFilles += $nombre_lits;

            // Accumuler le total par établissement
            if (!isset($totauxParEtablissement[$etablissement])) {
                $totauxParEtablissement[$etablissement] = ['garçons' => 0, 'filles' => 0];
            }
            $totauxParEtablissement[$etablissement]['filles'] += $nombre_lits;
        }

        // Calcul du total
        $lits[$etablissement][$niveau]['total'] = $lits[$etablissement][$niveau]['garçons'] + $lits[$etablissement][$niveau]['filles'];
        $totalLits = $totalGarçons + $totalFilles;  // Calcul du total général
    }

    // Retourner le tableau de résultats et les totaux
    return [
        'lits' => $lits,
        'totaux' => [
            'garçons' => $totalGarçons,
            'filles' => $totalFilles,
            'total' => $totalLits,
        ],
        'totauxParEtablissement' => $totauxParEtablissement,
    ];
}

// ////FONCTION CONTROLE LA SAISIE DE QUOTA EN FAISANT LE TEST SUR LA VALEUR DE LA NATURE FERMETURE
function controlSaisieQuota($faculte)
{
    global $connexion;

    // Préparer la requête pour récupérer la nature "fermeture" pour la faculté donnée
    $query = "SELECT COUNT(*) AS count FROM codif_delai WHERE faculte = ? AND nature = 'fermeture'";

    // Préparer la requête MySQLi
    $stmt = $connexion->prepare($query);
    $stmt->bind_param('s', $faculte);  // Lier l'ID de la faculté à la requête
    $stmt->execute();

    // Récupérer le résultat
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    // Si la nature "fermeture" existe pour la faculté, retourner true, sinon false
    // return $row['count'] > 0;

    if ($row['count'] == 0) {
?>
<script langage='javascript'>
alert('Veuiller renseigner au prealable toutes les dates butoirs!')
window.history.back();
</script>
<?php
        exit();
    }
}

// //FONCTION DE RECUPERATION D4UNE FACULTE PAR LE NIVEAU DE FORMATION
function getFaculteByNiveauFormation($niveauFormation)
{
    global $connexion;  // Connexion à la base de données

    // Requête pour récupérer la faculté associée au niveauFormation
    $query = 'SELECT DISTINCT etablissement FROM codif_etudiant WHERE niveauFormation = ?';

    // Préparer la requête
    $stmt = $connexion->prepare($query);
    $stmt->bind_param('s', $niveauFormation);  // Paramètre pour le niveauFormation
    $stmt->execute();

    // Récupérer les résultats
    $result = $stmt->get_result();

    // Vérifier si une faculté est trouvée pour ce niveau de formation
    if ($result->num_rows > 0) {
        // Retourner la faculté (supposons que chaque niveauFormation a une seule faculté associée)
        $row = $result->fetch_assoc();
        return $row['etablissement'];  // Faculté associée à ce niveauFormation
    } else {
        // Aucun résultat trouvé pour ce niveauFormation
        return null;  // Aucun niveauFormation trouvé, retourner null
    }
}

// ///FONCTION D'AJOUT DELAI (LA FONCTION EST APPELée DANS LA FONCTION validate_date_limite_codif_delai
function addDelai($nature, $faculte, $date)
{
    global $connexion;
    $requete = "INSERT INTO codif_delai (`nature`, `faculte`,`data_limite`) VALUES ('$nature', '$faculte', '$date')";
    $add = $connexion->prepare($requete);
    $add->execute();
}

// ///FONCTION DE VALIDATION DES DATES LORS DE L'ENREGISTREMENTS DE DELAIS POUR CHAQUE NATURE
function validate_date_limite_codif_delai($faculte, $nature, $date_limite)
{
    global $connexion;
    $messages = [];  // Tableau pour stocker les messages

    // Définir l'ordre des natures
    $natures = ['depart', 'choix', 'validation', 'paiement', 'fermeture'];

    // Vérifier si la nature est valide
    if (!in_array($nature, $natures)) {
        $messages[] = "Nature invalide.\n";
        return $messages;
    }

    // Initialiser le tableau des dates existantes
    $date_existantes = array_fill_keys($natures, null);

    // Requête pour récupérer les dates existantes pour la faculté
    $query = 'SELECT nature, data_limite FROM codif_delai WHERE faculte = ?';
    $stmt = $connexion->prepare($query);
    $stmt->bind_param('s', $faculte);
    $stmt->execute();
    $result = $stmt->get_result();

    // Remplir le tableau des dates existantes
    while ($row = $result->fetch_assoc()) {
        $date_existantes[$row['nature']] = $row['data_limite'];
    }

    // Vérifier si la nature existe déjà
    if ($date_existantes[$nature]) {
        $messages[] = "La nature '$nature' existe déjà pour '$faculte' avec la date: " . $date_existantes[$nature] . "\n";
        return $messages;
    }

    // Vérifier la nature précédente
    $index = array_search($nature, $natures);
    if ($index > 0 && !$date_existantes[$natures[$index - 1]]) {
        $messages[] = "La nature précédente ('" . $natures[$index - 1] . "') doit être définie avant d'insérer cette date pour '$faculte'.\n";
        return $messages;
    }

    // Vérifier que la date limite est supérieure à la date de la nature précédente
    if ($index > 0 && strtotime($date_limite) <= strtotime($date_existantes[$natures[$index - 1]])) {
        $messages[] = "La date de '$nature' doit être supérieure à celle de '" . $natures[$index - 1] . "' pour '$faculte'.\n";
        return $messages;
    }

    // Si toutes les vérifications passent, renvoyer un message de succès
    addDelai($nature, $faculte, $date_limite);
    $messages[] = "Date de '$nature' validée avec succès .\n";
    return $messages;  // Retourner true pour indiquer que la validation est réussie
}

/*
 * Fonction stocké toutes les informations de l'etudiant forclu automatique
 * ********************************************************************************
 */
function addArchive($id_etu, $username_user = null, $id_etu_heritier = null, $naissance_heritier = null, $sessionId_heritier = null, $moyenne_heritier = null, $id_suppleant = null, $naissance_suppleant = null, $session_suppleant = null, $moyenne_suppleant = null)
{
    global $connexion;
    try {
        // Verification du lit choisi par l'etudiant s'il existe
        $affectation = getLitStudentForclu($id_etu);
        if ($affectation) {
            $id_lit = $affectation['id_lit'];
            $date_choix = $affectation['dateTime_aff'];
        } else {
            $id_lit = null;
            $date_choix = null;
        }
        /**********************************************/
        $affectation = getLitStudentForclu($id_suppleant);
        if ($affectation) {
            $id_lit = $affectation['id_lit'];
            $date_choix = $affectation['dateTime_aff'];
        } else {
            $id_lit = null;
            $date_choix = null;
        }
        // FIN

        // Verification de la validation du lit choisi par l'etudiant s'il existe
        if ($validation = getDateValStudentForclu($id_etu)) {
            $date_val = $validation['dateTime_val'];
        } else {
            $date_val = null;
        }
        /* */
        if ($validation = getDateValStudentForclu($id_suppleant)) {
            $date_val = $validation['dateTime_val'];
        } else {
            $date_val = null;
        }
        // FIN

        // Verification du paiement du lit choisi par l'etudiant s'il existe
        $paiement = getValidatePaiementLitBySuppleant2($id_etu);
        if ($paiement->num_rows != 0) {
            while ($archi_paie = mysqli_fetch_array($paiement)) {
                add_archive_paie($archi_paie['id_etu'], $archi_paie['montant'], $archi_paie['montant'], 0, $archi_paie['libelle'], $archi_paie['dateTime_paie']);
                $date_paie = $archi_paie['dateTime_paie'];
            }
        } else {
            $date_paie = NULL;
        }
        // FIN

        // Verification du logement de l'etudiant s'il existe
        $loger = getValidateLogerByTitulaire2($id_etu);
        if ($loger) {
            $date_loger = $loger['dateTime_loger'];
        } else {
            $date_loger = NULL;
        }
        /* */
        $loger = getValidateLogerByTitulaire2($id_suppleant);
        if ($loger) {
            $date_loger = $loger['dateTime_loger'];
        } else {
            $date_loger = NULL;
        }
        // FIN

        $archive_paie = getArchivePaiement($id_etu);
        if ($archive_paie) {
            $id_paie_archive = $archive_paie['id_archive_paie'];
        } else {
            $id_paie_archive = NULL;
        }

        // ADD ARCHIVE
        $req_add_archive = 'INSERT INTO codif_archive (`id_etu`, `id_lit`, `date_choix`, `date_val`, `id_paie_archive`, `date_paie`, `date_log`, `dateTime_sys`, `username_user`, `id_etu_heritier`, `naissance_heritier`, `sessionId_heritier`, `moyenne_heritier`, `id_suppleant`, `naissance_suppleant`, `session_suppleant`, `moyenne_suppleant`) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?,?,?,?)';
        $insert_archive = $connexion->prepare($req_add_archive);
        $date = date('Y-m-d H:i:s');
        $insert_archive->bind_param(
            'iississssisssisss',
            $id_etu,
            $id_lit,
            $date_choix,
            $date_val,
            $id_paie_archive,
            $date_paie,
            $date_loger,
            $date,
            $username_user,
            $id_etu_heritier,
            $naissance_heritier,
            $sessionId_heritier,
            $moyenne_heritier,
            $id_suppleant,
            $naissance_suppleant,
            $session_suppleant,
            $moyenne_suppleant
        );
        if (!($date_loger || $date_paie)) {
            deleteValidation($id_etu);
            /* SUPPLEANT********* */
            deleteValidation($id_suppleant);
        } else {
            deleteLogement($id_etu);
            deletePaiement($id_etu);
            deleteValidation($id_etu);
            /* SUPPLEANT********** */
            deleteLogement($id_suppleant);
            deleteValidation($id_suppleant);
        }
        return $insert_archive->execute();
    } catch (mysqli_sql_exception $e) {
        echo 'Erreur SQL : ' . $e->getMessage();
    } catch (Exception $e) {
        echo 'Erreur : ' . $e->getMessage();
    }
}

/*
 * Fonction pour recuperer l'identifiant de codif_archive de des paiement de l'etudiant
 * ********************************************************************************
 */
function getArchivePaiement($id_etu)
{
    global $connexion;
    $requete = "SELECT * FROM `codif_archive_paie` WHERE id_etu='$id_etu'";
    $result = $connexion->query($requete);
    return $result->fetch_assoc();
}

/*
 * Fonction stocké toutes les informations de paiement de l'etudiant forclu
 * ********************************************************************************
 */
function add_archive_paie($id_etu, $montant_due, $montant_recu, $restant, $libelle, $dateTime_paie)
{
    global $connexion;
    $requete = 'INSERT INTO codif_archive_paie (`id_etu`, `montant_due`, `montant_recu`, `restant`, `libelle`, `dateTime_paie`) VALUES (?, ?, ?, ?, ?, ?)';
    $add_requette = $connexion->prepare($requete);
    $add_requette->bind_param(
        'isssss',
        $id_etu,
        $montant_due,
        $montant_recu,
        $restant,
        $libelle,
        $dateTime_paie
    );
    return $add_requette->execute();
}

/*
 * supprimer logement lit de l'etudiant forclu
 * ********************************************************************************
 */
function deleteLogement($id_etu)
{
    global $connexion;
    $requeteFor0 = "DELETE FROM codif_loger WHERE id_etu= '$id_etu'";
    $b = $connexion->prepare($requeteFor0);
    $b->execute();
}

/*
 * supprimer paiements lit de l'etudiant forclu
 * ********************************************************************************
 */
function deletePaiement($id_etu)
{
    global $connexion;
    $requeteFor0 = "DELETE FROM codif_paiement WHERE codif_paiement.id_val = (SELECT codif_validation.id_val FROM codif_validation JOIN codif_paiement ON codif_paiement.id_val = codif_validation.id_val JOIN codif_affectation ON codif_affectation.id_aff = codif_validation.id_aff JOIN codif_etudiant ON codif_etudiant.id_etu = codif_affectation.id_etu WHERE codif_etudiant.id_etu = codif_affectation.id_etu AND codif_etudiant.id_etu = '$id_etu' LIMIT 1)";
    $b = $connexion->prepare($requeteFor0);
    $b->execute();
}

/*
 * Fonction pour modifier les informations de la table codif_etudiant
 * ********************************************************************************
 */
function updateEtudiant($moyenne, $id_etu)
{
    global $connexion;
    $req_put = 'UPDATE `codif_etudiant` SET `moyenne` = ? WHERE `id_etu` = ?';
    $stmt = $connexion->prepare($req_put);
    if ($stmt) {
        $stmt->bind_param('si', $moyenne, $id_etu);
        if ($stmt->execute()) {
            return $stmt;
        } else {
            echo 'Erreur lors de la mise à jour : ' . $stmt->error;
        }
    } else {
        echo 'Échec de la préparation de la requête : ' . $connexion->error;
    }
}

/*
 * Modifier le lit choisi par l'etudiant
 * ********************************************************************************
 */
function updateCodifAffectation($statut, $id_heritier, $idEtu)
{
    // S'assurer que les valeurs sont des entiers
    $id_heritier = (int) $id_heritier;
    $idEtu = (int) $idEtu;
    global $connexion;

    // Préparer la requête SQL
    $sql = 'UPDATE `codif_affectation`
            SET `dateTime_aff` = NOW(), `statut` = ?, `id_etu` = ? WHERE `id_etu` = ?';

    // Initialiser une déclaration préparée
    $stmt = $connexion->prepare($sql);

    if ($stmt) {
        // Bind parameters (s for string, i for integer, etc. as needed)
        $stmt->bind_param('ssi', $statut, $id_heritier, $idEtu);  // Adjust types accordingly

        // Execute the statement
        if ($stmt->execute()) {
            return $stmt;
        } else {
            echo 'Error updating record: ' . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    } else {
        echo 'Prepare failed: ' . $connexion->error;
    }
}

// Fonction permettant de recuperer toustes les infos de la table etudiant
function info3($id)
{
    // Recherche des infos de l'etudiant
    global $connexion;
    $rr = "select * from codif_etudiant where id_etu='$id'";
    $ee = mysqli_query($connexion, $rr);
    $ss = mysqli_fetch_array($ee);

    $id_etu = $ss['id_etu'];
    $numIdentite = $ss['numIdentite'];
    $num_etu = $ss['num_etu'];
    $dateNaissance = $ss['dateNaissance'];
    $lieuNaissance = $ss['lieuNaissance'];
    $nom = $ss['nom'];
    $prenoms = $ss['prenoms'];
    $etablissement = $ss['etablissement'];
    $departement = $ss['departement'];
    $typeEtudiant = $ss['typeEtudiant'];
    $sessionId = $ss['sessionId'];
    $niveauFormation = $ss['niveauFormation'];
    $moyenne = $ss['moyenne'];
    $sexe = $ss['sexe'];
    $email = $ss['email_ucad'];
    $email2 = $ss['email_perso'];
    $telephone = $ss['telephone'];
    // /////////Recuperer le 1er caractere de la cni pour determiner le sexe
    $sexeL = '';
    if ($sexe == 'G' or $sexe == 'M') {
        $sexeL = 'Garçons';
    }
    if ($sexe == 'F') {
        $sexeL = 'Filles';
    }
    return array($id_etu, $numIdentite, $num_etu, $dateNaissance, $lieuNaissance, $nom, $prenoms, $etablissement, $departement, $niveauFormation, $moyenne, $typeEtudiant, $sessionId, $sexe, $sexeL, $email, $email2, $telephone);
}

/*
 * Fonction permet l'enregistrement forclusions manuel
 * ********************************************************************************
 */
function addForcloreManuel($num_etu, $motif, $username_user)
{
    // Les informations de l'etudiant a forclos
    $info_studentsForclu = info($num_etu);
    $info_studentsForclu_sexe = $info_studentsForclu[11];
    $info_studentsForclu_niv = $info_studentsForclu[7];
    $info_student_quota = getQuotaClasse($info_studentsForclu_niv, $info_studentsForclu_sexe)['COUNT(*)'];
    // Fin de recuperation

    // Les informations de l'etudiant heritier (le non attributaire le mieux placer)
    $rang_studentHeritier = (($info_student_quota * 2) + 1);
    $info_heritier = getAllDatastudentStatus_2($info_student_quota, $info_studentsForclu_niv, $info_studentsForclu_sexe, $rang_studentHeritier);
    $info_heritier_dateNaissance = $info_heritier['dateNaissance'];
    $info_heritier_moyenne = $info_heritier['moyenne'];
    $info_heritier_sessionId = $info_heritier['sessionId'];
    $id_studentHeritier = $info_heritier['id_etu'];

    $all_students = getStatutStudentByQuota($info_student_quota, $info_studentsForclu_niv, $info_studentsForclu_sexe);
    for ($i = 0; $i < count($all_students); $i++) {
        if ($all_students[$i]['num_etu'] == $num_etu) {
            // Les informations de l'etudiant a forclos apres verification
            $id_etu = $all_students[$i]['id_etu'];
            $moyenne = $all_students[$i]['moyenne'];
            $rang = $all_students[$i]['rang'];
            // FIN Les informations de l'etudiant a forclos apres verification

            // RECUPERATION DES INFORMATION DU SUPPLEANT
            $rang_suppleant = ($rang + $info_student_quota);
            $info_suppleant = getAllDatastudentStatus_2($info_student_quota, $info_studentsForclu_niv, $info_studentsForclu_sexe, $rang_suppleant);
            $id_suppleant = $info_suppleant['id_etu'];
            $naissance_suppleant = $info_suppleant['dateNaissance'];
            $moyenne_suppleant = $info_suppleant['moyenne'];
            $session_suppleant = $info_suppleant['sessionId'];
            // FIN DE RECUPERATION DES INFORMATIONS DU SUPPLEANT

            // Appel de la fonction addArchiche() pour stocker, les informations, de l'etudiant forclos, du suppleant et du non attributaire le mieux placé
            $req_archive = addArchive($id_etu, $username_user, $id_studentHeritier, $info_heritier_dateNaissance, $info_heritier_sessionId, $info_heritier_moyenne, $id_suppleant, $naissance_suppleant, $session_suppleant, $moyenne_suppleant);
            if ($req_archive) {
                // Changement d'affectation de lit entre le non attributaire et le suppleant
                $aff_1 = updateCodifAffectation('Suppleant(e)', $id_studentHeritier, $id_suppleant);
                // Changement d'affectation de lit entre le non le forclo et le suppleant
                $aff_2 = updateCodifAffectation('Attributaire', $id_suppleant, $id_etu);
                if ($aff_2 && $aff_1) {
                    // Permutation de données(date de naissance, moyenne et session) du suppleant au non attributaire
                    $resulte_2 = updateEtudiant($moyenne_suppleant, $id_studentHeritier);
                    if ($resulte_2) {
                        // Permutation de données(date de naissance, moyenne et session) de l'etudiant forclos au suppleante
                        $resulte_1 = updateEtudiant($moyenne, $id_suppleant);
                        if ($resulte_1) {
                            if ($resulte_1) {
                                global $connexion;
                                $requeteInsertForclusion = "INSERT INTO codif_forclusion (id_etu, dateTime_for, type, motif_manuel, username_user) VALUES ('$id_etu', NOW(), 'manuel', '$motif', '$username_user' )";
                                $requete = $connexion->prepare($requeteInsertForclusion);
                                return $requete->execute();
                            }
                        }
                    }
                }
            }
        }
    }
}

// ################ DETAILS PAIEMENT ##########################

function details($id_etu, $connexion)
{
    // Requête SQL pour récupérer les paiements d'un étudiant en fonction de id_etu
    $sql = "
        SELECT
            e.num_etu AS num_etu,
            e.nom,
            e.prenoms,
            p.dateTime_paie,
            p.montant,
            p.libelle,
\t\t\tp.quittance,
            p.id_paie
        FROM 
            codif_paiement p
        JOIN codif_validation v ON v.id_val = p.id_val
        JOIN codif_affectation a ON v.id_aff = a.id_aff
        JOIN codif_etudiant e ON e.id_etu = a.id_etu
        WHERE 
            e.id_etu = '$id_etu';  -- Filtrer par l'identifiant de l'étudiant
    ";

    $result = $connexion->query($sql);
    if (empty($result)) {
        die('Aucun paiement trouvé pour cet étudiant.' . $connexion->error);
    }
    // Récupérer les résultats
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    $result->free();
    // Si aucun résultat n'est trouvé, retourner un message d'information
    if (empty($data)) {
        return 'Aucun paiement trouvé pour cet étudiant.';
    }

    return $data;
}

/*
 * FORCLUSION AUTOMATIQUE: Ajouter des etudiants dans la table forclu
 * ********************************************************************************
 */
function addForclu($id_etu, $id_delai)
{
    // RECUPERATION DES INFORMATION DE L'ETUDIANT A FORCLORE
    $info_studentsForclu = info3($id_etu);
    $info_studentsForclu_sexe = $info_studentsForclu[13];
    $info_studentsForclu_niv = $info_studentsForclu[9];
    $info_studentsForclu_moyenne = $info_studentsForclu[10];
    $info_studentsForclu_session = $info_studentsForclu[12];
    $info_studentsForclu_naissance = $info_studentsForclu[3];
    $info_student_quota = getQuotaClasse($info_studentsForclu_niv, $info_studentsForclu_sexe)['COUNT(*)'];

    // CALCUL ET RECUPERATION DES INFORMATIONS DE L'ETUDIANT HERITIER
    $total_forclu = getAllForclu_manuel($info_studentsForclu_niv, $info_studentsForclu_sexe)->num_rows;
    $id_studentHeritier = ((2 * $info_student_quota) + $total_forclu + 1);
    $info_heritier = info3($id_studentHeritier);
    $info_heritier_dateNaissance = $info_heritier[3];
    $info_heritier_niv = $info_heritier[9];
    $info_heritier_sexe = $info_heritier[13];
    $info_heritier_moyenne = $info_heritier[10];
    $info_heritier_sessionId = $info_heritier[12];

    // TESTE SI L'ETUDIANT FORCLO ET L'ETUDIANT HERITIER ON LA MEME CLASSE ET LE MEME SEXE
    if ($info_studentsForclu_niv == $info_heritier_niv) {
        if ($info_studentsForclu_sexe == $info_heritier_sexe) {
            // ARCHIVAGE DES INFORMATION DES DEUX ETUDIANTS
            $req_archive = addArchive($id_etu, NULL, $id_studentHeritier, $info_heritier_dateNaissance, $info_heritier_sessionId, $info_heritier_moyenne);
            if ($req_archive) {
                // MODIFICATION DE L'AFFECTION DE LIT S'IL EXISTER
                updateCodifAffectation($id_studentHeritier, $id_etu);
                // LA PERMUTATION DES INFORMATIONS DE L'ETUDIANT FORCLU A L'ETUDIANT HERITIER
                $resulte = updateEtudiant($info_studentsForclu_moyenne, $id_studentHeritier);
                if ($resulte) {
                    global $connexion;
                    $requeteInsertForclusion = "INSERT into `codif_forclusion` (`id_etu`, `id_del`, `dateTime_for`) VALUES ($id_etu, $id_delai, NOW())";
                    $requete = $connexion->prepare($requeteInsertForclusion);
                    return $requete->execute();
                }
            }
        }
    }
}

function getLitsBySexeAndNiveau3($sexe)
{
    global $connexion;

    // Requête SQL pour récupérer le nombre de lits par sexe, niveau et établissement
    $sql = "
    SELECT
        e.niveauFormation,
        e.etablissement,
        l.sexe,
        COUNT(DISTINCT q.id_lit_q) AS nombre_lits
    FROM
        codif_etudiant e
    INNER JOIN
        codif_quota q ON e.niveauFormation = q.niveauFormation
    INNER JOIN
        Codif_lit l ON q.id_lit_q = l.id_lit
    WHERE
        l.sexe='$sexe'
    GROUP BY
        e.niveauFormation, e.etablissement, l.sexe;  
    ";

    // Préparation de la requête
    $stmt = $connexion->prepare($sql);

    // Vérification de la préparation de la requête
    if ($stmt === false) {
        die('Erreur de préparation de la requête : ' . htmlspecialchars($connexion->error));
    }

    // Exécution de la requête
    if (!$stmt->execute()) {
        die("Erreur lors de l'exécution de la requête : " . htmlspecialchars($stmt->error));
    }

    // Récupération des résultats
    $result = $stmt->get_result();

    // Vérification de la récupération des résultats
    if ($result === false) {
        die('Erreur lors de la récupération des résultats : ' . htmlspecialchars($stmt->error));
    }

    // Tableau pour stocker les données
    $lits = [];
    $totalGarçons = 0;
    $totalFilles = 0;
    $totalLits = 0;

    // Tableau pour stocker les totaux par établissement
    $totauxParEtablissement = [];

    // Stockage des résultats dans le tableau
    while ($row = $result->fetch_assoc()) {
        $niveau = $row['niveauFormation'];
        $etablissement = $row['etablissement'];
        $sexe = $row['sexe'];
        $nombre_lits = $row['nombre_lits'];

        // Initialisation si le niveau et l'établissement n'existent pas encore dans le tableau
        if (!isset($lits[$etablissement][$niveau])) {
            $lits[$etablissement][$niveau] = ['garçons' => 0, 'filles' => 0, 'total' => 0];
        }

        // Ajout du nombre de lits selon le sexe
        if ($sexe === 'G') {
            $lits[$etablissement][$niveau]['garçons'] += $nombre_lits;
            $totalGarçons += $nombre_lits;

            // Accumuler le total par établissement
            if (!isset($totauxParEtablissement[$etablissement])) {
                $totauxParEtablissement[$etablissement] = ['garçons' => 0, 'filles' => 0];
            }
            $totauxParEtablissement[$etablissement]['garçons'] += $nombre_lits;
        } elseif ($sexe === 'F') {
            $lits[$etablissement][$niveau]['filles'] += $nombre_lits;
            $totalFilles += $nombre_lits;

            // Accumuler le total par établissement
            if (!isset($totauxParEtablissement[$etablissement])) {
                $totauxParEtablissement[$etablissement] = ['garçons' => 0, 'filles' => 0];
            }
            $totauxParEtablissement[$etablissement]['filles'] += $nombre_lits;
        }

        // Calcul du total
        $lits[$etablissement][$niveau]['total'] = $lits[$etablissement][$niveau]['garçons'] + $lits[$etablissement][$niveau]['filles'];
        $totalLits = $totalGarçons + $totalFilles;  // Calcul du total général
    }

    // Retourner le tableau de résultats et les totaux
    return [
        'lits' => $lits,
        'totaux' => [
            'garçons' => $totalGarçons,
            'filles' => $totalFilles,
            'total' => $totalLits,
        ],
        'totauxParEtablissement' => $totauxParEtablissement,
    ];
}

// FONCTION POUR VERIFIER LA SITUATION DE L'ETUDIANT
function isLoger_titulaire($num_etu)
{
    global $connexion;
    $requete = "SELECT * FROM `codif_loger` JOIN codif_paiement ON codif_paiement.id_paie=codif_loger.id_paie JOIN codif_validation ON codif_validation.id_val=codif_paiement.id_val JOIN codif_affectation ON codif_affectation.id_aff=codif_validation.id_aff JOIN codif_etudiant ON codif_etudiant.id_etu=codif_affectation.id_etu WHERE codif_etudiant.num_etu='$num_etu'";
    $result = mysqli_query($connexion, $requete);
    return $result->fetch_assoc();
}

function isLoger($num_etu)
{
    global $connexion;
    $requete = "SELECT * FROM `codif_loger` JOIN codif_validation ON codif_validation.id_val=codif_loger.id_val JOIN codif_affectation ON codif_affectation.id_aff=codif_validation.id_aff JOIN codif_etudiant ON codif_etudiant.id_etu=codif_affectation.id_etu WHERE codif_etudiant.num_etu='$num_etu'";
    $result = mysqli_query($connexion, $requete);
    return $result->fetch_assoc();
}

function isPaie_titulaire($num_etu)
{
    global $connexion;
    $requete = "SELECT * FROM `codif_paiement` JOIN codif_validation ON codif_validation.id_val=codif_paiement.id_val JOIN codif_affectation ON codif_affectation.id_aff=codif_validation.id_aff JOIN codif_etudiant ON codif_etudiant.id_etu=codif_affectation.id_etu WHERE codif_etudiant.num_etu='$num_etu'";
    $result = mysqli_query($connexion, $requete);
    return $result->fetch_assoc();
}

function isValider($num_etu)
{
    global $connexion;
    $requete = "SELECT * FROM `codif_validation` JOIN codif_affectation ON codif_affectation.id_aff=codif_validation.id_aff JOIN codif_etudiant ON codif_etudiant.id_etu=codif_affectation.id_etu WHERE codif_etudiant.num_etu='$num_etu'";
    $result = mysqli_query($connexion, $requete);
    return $result->fetch_assoc();
}

function isChoix($num_etu)
{
    global $connexion;
    $requete = "SELECT * FROM `codif_affectation` JOIN codif_etudiant ON codif_etudiant.id_etu=codif_affectation.id_etu WHERE codif_etudiant.num_etu='$num_etu' 
    and codif_affectation.statut='Attributaire'";
    $result = mysqli_query($connexion, $requete);
    return $result->fetch_assoc();
}

// ######## FONCTION UTILISER DANS DBA ###################
// ############ POUR RECUPERER LES USERS ############################
function getUsers()
{
    global $connexion_user;

    // Requête SQL sécurisée
    $query = "SELECT id_user, username_user, prenom_user, nom_user, telephone_user, profil_user, var, sexe_user, type_mdp, pavillon, campus, is_active, datesys FROM codif_user where profil_user !='user'";
    $stmt = $connexion_user->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();

    // Stocker les utilisateurs dans un tableau
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }

    return $users;  // Retourne la liste des utilisateurs
}

// ############  POUR RECUPERER LES PAVILLONS  #################
function getAllPavillons($connexion)
{
    $query = 'SELECT DISTINCT pavillon FROM codif_lit';
    $result = mysqli_query($connexion, $query);

    // Vérification de la requête
    if (!$result) {
        die("Erreur lors de l'exécution de la requête : " . mysqli_error($conn));
    }

    // Tableau pour stocker les pavillons
    $pavillons = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $pavillons[] = $row['pavillon'];
    }

    return $pavillons;  // Retourne un tableau des pavillons
}

function getAllPavillons_2($connexion)
{
    $query = 'SELECT DISTINCT pavillon FROM codif_lit_complet';
    $result = mysqli_query($connexion, $query);

    // Vérification de la requête
    if (!$result) {
        die("Erreur lors de l'exécution de la requête : " . mysqli_error($conn));
    }

    // Tableau pour stocker les pavillons
    $pavillons = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $pavillons[] = $row['pavillon'];
    }

    return $pavillons;  // Retourne un tableau des pavillons
}

function getPavillonsByCampus0($connexion, $campus)
{
    $query = 'SELECT DISTINCT pavillon FROM codif_lit_complet';
    $result = mysqli_query($connexion, $query);

    // Vérification de la requête
    if (!$result) {
        die("Erreur lors de l'exécution de la requête : " . mysqli_error($conn));
    }

    // Tableau pour stocker les pavillons
    $pavillons = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $pavillons[] = $row['pavillon'];
    }

    return $pavillons;  // Retourne un tableau des pavillons
}

function getPavillonsByCampus($connexion, $campus)
{
    $query = "SELECT DISTINCT pavillon FROM codif_lit_complet WHERE campus='$campus'";
    $result = mysqli_query($connexion, $query);

    // Vérification de la requête
    if (!$result) {
        die("Erreur lors de l'exécution de la requête : " . mysqli_error($conn));
    }

    // Tableau pour stocker les pavillons
    $pavillons = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $pavillons[] = $row['pavillon'];
    }

    return $pavillons;  // Retourne un tableau des pavillons
}

function getPavillonsByCampus2($connexion, $campus)
{
    $query = "SELECT DISTINCT pavillon FROM codif_lit_complet WHERE campus='$campus' or campus2='$campus'";
    $result = mysqli_query($connexion, $query);

    // Vérification de la requête
    if (!$result) {
        die("Erreur lors de l'exécution de la requête : " . mysqli_error($conn));
    }

    // Tableau pour stocker les pavillons
    $pavillons = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $pavillons[] = $row['pavillon'];
    }

    return $pavillons;  // Retourne un tableau des pavillons
}

function getAllCampus($connexion)
{
    $query = 'SELECT DISTINCT campus FROM codif_lit_complet';
    $result = mysqli_query($connexion, $query);

    // Vérification de la requête
    if (!$result) {
        die("Erreur lors de l'exécution de la requête : " . mysqli_error($conn));
    }

    // Tableau pour stocker les pavillons
    $campus = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $campus[] = $row['campus'];
    }

    return $campus;  // Retourne un tableau des pavillons
}

function getAllProfiles($connexion)
{
    $query = 'SELECT DISTINCT profiles FROM codif_profile';
    $result = mysqli_query($connexion, $query);

    // Vérification de la requête
    if (!$result) {
        die("Erreur lors de l'exécution de la requête : " . mysqli_error($conn));
    }

    // Tableau pour stocker les pavillons
    $pavillons = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $profiles[] = $row['profiles'];
    }

    return $profiles;  // Retourne un tableau des pavillons
}

function enregistrerUtilisateur($connexion, $nom, $prenom, $var, $sexe, $telephone, $username, $profil, $pavillon, $campus, $fac)
{
    // Mot de passe par défaut (haché avec SHA1)
    $passwordHash = sha1('COUD');

    // Requête SQL avec des requêtes préparées
    $sql = 'INSERT INTO codif_user (nom_user, prenom_user, var, sexe_user, telephone_user, username_user, password_user, profil_user, pavillon, campus, fac, datesys)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?, NOW())';

    // Préparation de la requête
    $stmt = mysqli_prepare($connexion, $sql);
    if (!$stmt) {
        return 'Erreur de préparation : ' . mysqli_error($connexion);
    }

    // Gérer le cas où pavillon est null
    if (empty($pavillon)) {
        $pavillon = null;
    }
    if (empty($campus)) {
        $campus = null;
    }
    // Gérer le cas où var est null
    if (empty($var)) {
        $var = null;
    }
    // Gérer le cas où fac est null
    if (empty($fac)) {
        $fac = null;
    }

    // Liaison des paramètres (avec gestion de `NULL`)
    mysqli_stmt_bind_param($stmt, 'sssssssssss', $nom, $prenom, $var, $sexe, $telephone, $username, $passwordHash, $profil, $pavillon, $campus, $fac);

    // Exécution de la requête
    $result = mysqli_stmt_execute($stmt);

    // Vérification et fermeture
    if ($result) {
        mysqli_stmt_close($stmt);
        return true;  // Succès
    } else {
        return "Erreur lors de l'insertion : " . mysqli_error($connexion);
    }
}

function enregistrerUtilisateur_2($connexion, $nom, $prenom, $var, $sexe, $telephone, $username, $profil, $password)
{
    // Mot de passe par défaut (haché avec SHA1)
    $passwordHash = sha1($password);

    // Requête SQL avec des requêtes préparées
    $sql = 'INSERT INTO codif_user (nom_user, prenom_user, var, sexe_user, telephone_user, username_user, password_user, profil_user, datesys)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())';

    // Préparation de la requête
    $stmt = mysqli_prepare($connexion, $sql);
    if (!$stmt) {
        return 'Erreur de préparation : ' . mysqli_error($connexion);
    }
    // Liaison des paramètres (avec gestion de `NULL`)
    mysqli_stmt_bind_param($stmt, 'ssssssss', $nom, $prenom, $var, $sexe, $telephone, $username, $passwordHash, $profil);

    // Exécution de la requête
    $result = mysqli_stmt_execute($stmt);

    // Vérification et fermeture
    if ($result) {
        mysqli_stmt_close($stmt);
        return true;  // Succès
    } else {
        return "Erreur lors de l'insertion : " . mysqli_error($connexion);
    }
}

function supprimerUtilisateur($connexion, $id_user)
{
    // Requête de suppression
    $sql = 'DELETE FROM codif_user WHERE id_user = ?';
    $stmt = mysqli_prepare($connexion, $sql);

    if (!$stmt) {
        return 'Erreur de préparation : ' . mysqli_error($connexion);
    }

    // Lier les paramètres et exécuter
    mysqli_stmt_bind_param($stmt, 'i', $id_user);
    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        mysqli_stmt_close($stmt);
        return true;
    } else {
        return 'Erreur lors de la suppression : ' . mysqli_error($connexion);
    }
}

// Fonction pour mettre à jour le statut de l'utilisateur
function mettreAJourStatutUtilisateur($connexion, $id_user, $isActive)
{
    $sql = "UPDATE codif_user SET is_active = $isActive WHERE id_user = $id_user";

    if (mysqli_query($connexion, $sql)) {
        return true;  // Succès
    } else {
        return mysqli_error($connexion);  // Retourne l'erreur MySQL
    }
}

function modifierUtilisateur($connexion, $id_user, $nom, $prenom, $var, $sexe, $telephone, $username, $profil, $pavillon, $campus)
{
    // Requête SQL pour la mise à jour avec des requêtes préparées
    $sql = 'UPDATE codif_user 
            SET nom_user = ?, 
                prenom_user = ?, 
                var = ?, 
                sexe_user = ?, 
                telephone_user = ?, 
                username_user = ?, 
                profil_user = ?, 
                pavillon = ? ,
                campus = ? 
            WHERE id_user = ?';

    // Préparation de la requête
    $stmt = mysqli_prepare($connexion, $sql);
    if (!$stmt) {
        return 'Erreur de préparation : ' . mysqli_error($connexion);
    }

    // Gérer le cas où pavillon est null
    if (empty($pavillon)) {
        $pavillon = null;
    }

    // Liaison des paramètres (avec gestion de `NULL`)
    mysqli_stmt_bind_param($stmt, 'sssssssssi', $nom, $prenom, $var, $sexe, $telephone, $username, $profil, $pavillon, $campus, $id_user);

    // Exécution de la requête
    $result = mysqli_stmt_execute($stmt);

    // Vérification et fermeture
    if ($result) {
        mysqli_stmt_close($stmt);
        return true;  // Succès
    } else {
        return 'Erreur lors de la mise à jour : ' . mysqli_error($connexion);
    }
}

function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// ######### FIN DANS DBA ##################

function getAllNiveauFormation2($faculte)
{
    global $connexion;
    $requeteListeEtablissement = "SELECT DISTINCT niveauFormation, sexe FROM `codif_etudiant` WHERE etablissement='$faculte'";
    $resultatRequeteEtablissement = mysqli_query($connexion, $requeteListeEtablissement);
    return $resultatRequeteEtablissement;
}

// POUR enregistrer un etudiant

function enregistrerEtudiant($connexion, $num_etu, $prenoms, $nom, $telephone, $lieuNaissance, $dateNaissance, $etablissement, $departement, $niveauFormation, $moyenne, $numIdentite, $sexe)
{
    // Sécurisation des entrées
    $num_etu = mysqli_real_escape_string($connexion, $num_etu);
    $prenoms = mysqli_real_escape_string($connexion, $prenoms);
    $nom = mysqli_real_escape_string($connexion, $nom);
    $telephone = mysqli_real_escape_string($connexion, $telephone);
    $lieuNaissance = mysqli_real_escape_string($connexion, $lieuNaissance);
    $dateNaissance = mysqli_real_escape_string($connexion, $dateNaissance);
    $etablissement = mysqli_real_escape_string($connexion, $etablissement);
    $departement = mysqli_real_escape_string($connexion, $departement);
    $niveauFormation = mysqli_real_escape_string($connexion, $niveauFormation);
    $moyenne = mysqli_real_escape_string($connexion, $moyenne);
    $numIdentite = mysqli_real_escape_string($connexion, $numIdentite);
    $sexe = mysqli_real_escape_string($connexion, $sexe);

    // Requête SQL d'insertion
    $datesys = date('Y-m-d H:i:s');
    $var = 'Ajout Manuel ' . $datesys = date('Y-m-d H:i:s');
    $sql = "INSERT INTO codif_etudiant (num_etu, prenoms, nom, telephone, lieuNaissance, dateNaissance, etablissement, departement, niveauFormation, moyenne, numIdentite, sexe, var) 
            VALUES ('$num_etu', '$prenoms', '$nom', '$telephone', '$lieuNaissance', '$dateNaissance', '$etablissement', '$departement', '$niveauFormation', '$moyenne', '$numIdentite', '$sexe', '$var')";

    // Exécution de la requête
    if (mysqli_query($connexion, $sql)) {
        echo "<script>alert('Étudiant enregistré avec succès !'); window.location.href='etudiant.php';</script>";
    } else {
        echo "<script>alert('Erreur lors de l\'enregistrement : " . mysqli_error($connexion) . "');</script>";
    }
}

function studentConnect2($username)
{
    global $connexion;
    $users = "SELECT ce.id_etu,ce.num_etu, ce.nom, ce.sexe, ce.dateNaissance,ce.moyenne, ce.telephone, a.dateTime_aff, ce.niveauFormation, ce.departement, ce.etablissement, ce.prenoms, lg.dateTime_loger, li.lit, vl.dateTime_val, pc.dateTime_paie, pc.montant, pc.quittance, pc.username_user, pc.libelle 
FROM codif_etudiant ce 
LEFT JOIN codif_affectation a ON ce.id_etu = a.id_etu 
LEFT JOIN codif_lit li ON li.id_lit = a.id_lit
LEFT JOIN codif_loger lg ON ce.id_etu = lg.id_etu 
LEFT JOIN codif_validation vl ON a.id_aff = vl.id_aff 
LEFT JOIN codif_paiement pc ON pc.id_val = vl.id_val 
WHERE ce.num_etu = '$username'";
    $info = $connexion->query($users);
    return $info->fetch_assoc();
}

function getQuotaClasse_2($classe, $sexe)
{
    global $connexion;
    $requeteQuotaClasse = "SELECT DISTINCT pavillon, chambre, lit, id_lit_q  FROM `codif_quota` JOIN codif_lit ON codif_lit.id_lit = codif_quota.id_lit_q WHERE `NiveauFormation` = '$classe' AND codif_lit.sexe = '$sexe'";
    $resultRequeteQuotaClasse = mysqli_query($connexion, $requeteQuotaClasse);
    return $resultRequeteQuotaClasse;
}

function isDemarre($classe, $sexe)
{
    global $connexion;
    $requeteQuotaClasse = "SELECT * FROM `codif_demarrage` WHERE `niveauFormation`='$classe' AND `sexe`='$sexe'";
    $resultRequeteQuotaClasse = mysqli_query($connexion, $requeteQuotaClasse);
    return $resultRequeteQuotaClasse->fetch_assoc();
}

function addDemarrage($classe, $username_user, $sexe)
{
    global $connexion;
    $dateTime_sys = date('Y-m-d H:i:s');
    $sql = "INSERT INTO `codif_demarrage` (`niveauFormation`, `dateTime_sys`, `username_user`, `sexe`) VALUES ('$classe', '$dateTime_sys', '$username_user', '$sexe')";
    $add = $connexion->prepare($sql);
    $add->execute();
}

function getMessageEnvoyer($niveauFormation, $sexe)
{
    global $connexion;
    $requeteListeEtablissement = "SELECT COUNT(*) FROM `codif_demarre_choix` WHERE niveauFormation='$niveauFormation' AND sexe='$sexe'";
    $resultatRequeteEtablissement = mysqli_query($connexion, $requeteListeEtablissement);
    return $resultatRequeteEtablissement->fetch_assoc();
}

function addSendMessage($niveauFormation, $dateTime_sys, $user, $sexe)
{
    global $connexion;
    $requete = 'INSERT INTO codif_demarre_choix(`niveauFormation`, `dateTime_sys`, `user`, `sexe`) VALUES (?, ?, ?, ?)';
    $add_requette = $connexion->prepare($requete);
    $add_requette->bind_param(
        'ssss',
        $niveauFormation,
        $dateTime_sys,
        $user,
        $sexe
    );
    return $add_requette->execute();
}

/* *********************************************************************************
Fonction pour verifier si letudiant a effectué son 1er paiement
********************************************************************************* */
function verifPremierPaiement($num_etu)
{
    global $connexion;
    $sql = "SELECT * FROM `codif_paiement` WHERE id_val in (SELECT id_val from codif_validation where id_aff in (SELECT id_aff from codif_affectation where id_etu in (SELECT id_etu from codif_etudiant where num_etu='$num_etu')))";
    $result = mysqli_query($connexion, $sql);
    return $result->fetch_assoc();
}

/*
 * FONCTION POUR SUPPRIMER LE CHOIX DU LIT D'UN ETUDIANT
 * ********************************************************************************
 */
function delete_choix_lit($id_etu)
{
    global $connexion;
    $requeteAffectEtu = " DELETE FROM codif_affectation WHERE `id_etu`=$id_etu";
    $inforequeteAffectEtu = $connexion->query($requeteAffectEtu);
    return $inforequeteAffectEtu;
}

function getTotauxFacturesEtPaiements($filtre, $connexion, $dateDebut = null, $dateFin = null)
{
    // Dates
    $dateCondition = '';
    if ($dateDebut && $dateFin) {
        $dateCondition = "AND p.dateTime_paie BETWEEN '$dateDebut' AND '$dateFin'";
    } elseif ($dateDebut) {
        $dateCondition = "AND p.dateTime_paie >= '$dateDebut'";
    } elseif ($dateFin) {
        $dateCondition = "AND p.dateTime_paie <= '$dateFin'";
    }

    // Campus ou global
    $conditionFiltre = '';
    if ($filtre !== 'global') {
        $filtre = mysqli_real_escape_string($connexion, $filtre);
        $conditionFiltre = "AND l.campus = '$filtre'";
    }

    // Requête principale : on récupère toutes les affectations filtrées
    $sql = "
        SELECT 
            e.id_etu,
            e.num_etu,
            l.indiv AS type_chambre
        FROM codif_lit l
        JOIN codif_affectation a ON l.id_lit = a.id_lit
        JOIN codif_etudiant e ON a.id_etu = e.id_etu
        JOIN codif_validation v ON a.id_aff = v.id_aff
        WHERE 1=1
        $conditionFiltre
    ";

    $result = $connexion->query($sql);

    // Initialisation
    $total_facture_loyer = 0;
    $total_facture_caution = 0;
    $total_loyer_paye = 0;
    $total_caution_payee = 0;

    while ($row = $result->fetch_assoc()) {
        $etudiantId = $row['id_etu'];
        $num_etu = $row['num_etu'];
        $type_chambre = $row['type_chambre'];

        // Nombre de mois de facturation
        $nbMois = getNbreMois2($num_etu);

        // Prix du lit par mois
        $prixLit = getMontant($type_chambre);

        // Facturation
        $loyerFacture = $nbMois * $prixLit;
        $cautionFacture = verifCaution($etudiantId) ? 5000 : 0;

        $total_facture_loyer += $loyerFacture;
        $total_facture_caution += $cautionFacture;

        // Paiements
        $paiementsSql = "
            SELECT 
                SUM(CASE WHEN p.libelle LIKE '%caution%' THEN p.montant ELSE 0 END) AS caution,
                SUM(CASE WHEN p.libelle NOT LIKE '%caution%' THEN p.montant ELSE 0 END) AS loyer
            FROM codif_paiement p
            JOIN codif_validation v ON p.id_val = v.id_val
            JOIN codif_affectation a ON v.id_aff = a.id_aff
            WHERE a.id_etu = '$etudiantId'
            $dateCondition
        ";
        $resPaiements = $connexion->query($paiementsSql);
        $paiement = $resPaiements->fetch_assoc();

        $total_loyer_paye += $paiement['loyer'] ?? 0;
        $total_caution_payee += $paiement['caution'] ?? 0;
    }

    // Calculs des restes
    $total_facture = $total_facture_loyer + $total_facture_caution;
    $total_paye = $total_loyer_paye + $total_caution_payee;
    $reste_loyer = $total_facture_loyer - $total_loyer_paye;
    $reste_caution = $total_facture_caution - $total_caution_payee;
    $reste_total = $reste_loyer + $reste_caution;

    return [
        'total_facture_loyer' => $total_facture_loyer,
        'total_facture_caution' => $total_facture_caution,
        'total_facture' => $total_facture,
        'total_loyer_paye' => $total_loyer_paye,
        'total_caution_payee' => $total_caution_payee,
        'total_paye' => $total_paye,
        'reste_loyer' => $reste_loyer,
        'reste_caution' => $reste_caution,
        'reste_total' => $reste_total,
    ];
}

function info_statu($num_etu)
{
    $info = studentConnect2($num_etu);
    $sexe = $info['sexe'];
    $niveauFormation = $info['niveauFormation'];
    $quota = getQuotaClasse($niveauFormation, $sexe)['COUNT(*)'];
    $status_1 = getAllDatastudentStatus($quota, $niveauFormation, $sexe);
    for ($i = 0; $i < count($status_1); $i++) {
        if ($status_1[$i]['num_etu'] == $num_etu) {
            $rang = $status_1[$i]['rang'];
        }
    }
    $status_finale = getAllDatastudentStatus_2($quota, $niveauFormation, $sexe, $rang);
    return $status_finale['statut'];
}

function getPaymentDetailsByPavillon1($pavillonDonne, $connexion, $dateDebut = null, $dateFin = null)
{
    // Construction de la condition de date si les paramètres sont fournis
    $dateCondition = '';
    if ($dateDebut && $dateFin) {
        $dateCondition = "AND p.dateTime_paie BETWEEN '$dateDebut' AND '$dateFin'";
    } elseif ($dateDebut) {
        $dateCondition = "AND p.dateTime_paie >= '$dateDebut'";
    } elseif ($dateFin) {
        $dateCondition = "AND p.dateTime_paie <= '$dateFin'";
    }

    $sql = "
    SELECT 
        l.pavillon,
        l.chambre,
        l.lit,
        e.id_etu AS etudiant_id,
        e.num_etu AS num_etu,
        e.nom AS etudiant_nom,
        e.prenoms AS etudiant_prenoms,
        e.etablissement,
        l.indiv AS type_chambre,
        lg.id_log AS log_id,
        lg.id_val AS validation_id,
        lg.id_paie AS paiement_id,
        lg.username_user AS utilisateur,
        a.rappel_envoye,
        lg.datetime_loger AS date_log,

        COALESCE((
            SELECT SUM(p.montant)
            FROM codif_paiement p
            WHERE p.id_val = v.id_val $dateCondition
        ), 0) AS montant_paye_total,

        COALESCE((
            SELECT 
                CASE 
                    WHEN EXISTS (
                        SELECT 1 FROM codif_paiement p 
                        WHERE p.id_val = v.id_val $dateCondition
                        AND (p.libelle LIKE '%CAUTION%' OR p.libelle LIKE '%caution%')
                    ) THEN 5000 
                    ELSE 0 
                END
        ), 0) AS caution_payee,

        COALESCE((
            SELECT SUM(CASE 
                WHEN p.libelle NOT LIKE '%CAUTION%' THEN p.montant 
                ELSE 0 
            END)
            FROM codif_paiement p
            WHERE p.id_val = v.id_val $dateCondition
        ), 0) AS loyer_paye

    FROM 
        codif_lit l
    LEFT JOIN 
        codif_affectation a ON l.id_lit = a.id_lit
    LEFT JOIN 
        codif_etudiant e ON a.id_etu = e.id_etu
    LEFT JOIN 
        codif_validation v ON a.id_aff = v.id_aff
    LEFT JOIN 
        codif_loger lg ON lg.id_etu = e.id_etu

    WHERE 
         
        l.pavillon = '$pavillonDonne' and (a.statut='Attributaire' or a.id_aff IS NULL )

    GROUP BY 
        l.pavillon, l.chambre, l.lit, e.id_etu, lg.id_log

    ORDER BY 
        CAST(SUBSTRING_INDEX(l.pavillon, '(', 1) AS UNSIGNED), 
        IF(LOCATE('(', l.pavillon) > 0, 
            SUBSTRING(l.pavillon, LOCATE('(', l.pavillon) + 1, LOCATE(')', l.pavillon) - LOCATE('(', l.pavillon) - 1), 
            ''
        ),
        CAST(SUBSTRING_INDEX(l.chambre, '(', 1) AS UNSIGNED),  
        IF(LOCATE('(', l.chambre) > 0, 
            SUBSTRING(l.chambre, LOCATE('(', l.chambre) + 1, LOCATE(')', l.chambre) - LOCATE('(', l.chambre) - 1), 
            ''
        ),
        CAST(SUBSTRING_INDEX(l.lit, '_', -1) AS UNSIGNED)
    ";

    $stmt = $connexion->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $etudiantId = $row['etudiant_id'];
        $etudiant_num = $row['num_etu'];

        if ($etudiantId) {
            $nombreMois = getNbreMois2($etudiant_num);
            $prixLit = getMontant($row['type_chambre']);

            $montantLoyerFacture = $nombreMois * $prixLit;
            $montantCautionFacture = verifCaution($etudiantId) ? 5000 : 0;

            /** Arriérés de l'étudiant */
            $montantArrierer = getMontantArrierer($connexion, $etudiant_num);

            /** Total facturé */
            $montantFactureTotal = $montantLoyerFacture + $montantCautionFacture + $montantArrierer;

            /** Paiements effectués */
            $montantPayeTotal = $row['montant_paye_total'] ?? 0;
            $cautionPayee = $row['caution_payee'] ?? 0;
            $loyerPaye = $montantPayeTotal - $cautionPayee;

            /** Restes à payer */
            $resteLoyer = max(0, $montantLoyerFacture - $loyerPaye);
            $resteCaution = max(0, $montantCautionFacture - $cautionPayee);

            // $resteAPayerTotal = $resteLoyer + $resteCaution+ $montantArrierer;
            $resteAPayerTotal = $montantFactureTotal - $montantPayeTotal;
        } else {
            $montantLoyerFacture = 0;
            $montantCautionFacture = 0;
            $montantFactureTotal = 0;
            $montantPayeTotal = 0;
            $cautionPayee = 0;
            $loyerPaye = 0;
            $montantArrierer = 0;
            $resteLoyer = 0;
            $resteCaution = 0;
            $resteAPayerTotal = 0;
        }

        $data[] = [
            'pavillon' => $row['pavillon'],
            'chambre' => $row['chambre'],
            'lit' => $row['lit'],
            'etudiant_id' => $etudiantId,
            'etudiant_nom' => $row['etudiant_nom'],
            'etudiant_prenoms' => $row['etudiant_prenoms'],
            'etablissement' => $row['etablissement'],
            'num_etu' => $etudiant_num,
            'type_chambre' => $row['type_chambre'],
            'montant_facture_total' => $montantFactureTotal,
            'montant_loyer_facture' => $montantLoyerFacture,
            'montant_caution_facture' => $montantCautionFacture,
            'montant_paye_total' => $montantPayeTotal,
            'loyer_paye' => $loyerPaye,
            'caution_payee' => $cautionPayee,
            'reste_loyer' => $resteLoyer,
            'reste_caution' => $resteCaution,
            'reste_a_payer_total' => $resteAPayerTotal,
            'montant_arrierer' => $montantArrierer,
            'log_id' => $row['log_id'],
            'validation_id' => $row['validation_id'],
            'paiement_id' => $row['paiement_id'],
            'utilisateur' => $row['utilisateur'],
            'rappel_envoye' => $row['rappel_envoye'],
            'date_log' => $row['date_log'],
            'etat_lit' => $etudiantId ? 'Occupé' : 'Libre'
        ];
    }

    $stmt->close();
    return $data;
}

// ********POUR OBTENIR LE NOMBRE EXACTES DES QUOTA DEJA ATTRIBUER A UNE FORMATION PAR SEXE*****
function getNombreLitParFormationEtSexe($connexion, $niveauFormation, $sexe)
{
    // Préparer la requête SQL
    $sql = 'SELECT COUNT(*) AS total_lits
            FROM codif_quota cq
            INNER JOIN codif_lit cl ON cq.id_lit_q = cl.id_lit
            WHERE cq.niveauFormation = ? 
              AND cl.sexe = ?';

    // Préparer la requête
    $stmt = $connexion->prepare($sql);
    $stmt->bind_param('ss', $niveauFormation, $sexe);  // deux paramètres string

    // Exécuter
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    // Retourner le total
    return $row['total_lits'] ?? 0;
}

function getLitParChambre($link, $chambre)
{
    $sql = 'SELECT 
                l.id_lit, 
                l.lit, 
                af.niveauFormation,
                e.nom,
                e.prenoms,
                e.num_etu,
                cc.statut,

                v.dateTime_val,
                paie.dateTime_paie,
                lg.dateTime_loger

            FROM codif_lit l

            LEFT JOIN codif_quota af 
                ON l.id_lit = af.id_lit_q

            LEFT JOIN codif_affectation cc 
                ON cc.id_lit = l.id_lit 

            LEFT JOIN codif_etudiant e 
                ON cc.id_etu = e.id_etu

            /*  DERNIÈRE VALIDATION */
            LEFT JOIN codif_validation v 
                ON v.id_aff = cc.id_aff
                AND v.dateTime_val = (
                    SELECT MAX(v2.dateTime_val)
                    FROM codif_validation v2
                    WHERE v2.id_aff = cc.id_aff
                )

            /*  DERNIER PAIEMENT */
            LEFT JOIN codif_paiement paie 
                ON paie.id_val = v.id_val
                AND paie.dateTime_paie = (
                    SELECT MAX(p2.dateTime_paie)
                    FROM codif_paiement p2
                    WHERE p2.id_val = v.id_val
                )

            /*  DERNIER LOGEMENT */
            LEFT JOIN codif_loger lg 
                ON (
                    lg.id_val = v.id_val 
                    OR lg.id_paie = paie.id_paie
                )
                AND lg.dateTime_loger = (
                    SELECT MAX(l2.dateTime_loger)
                    FROM codif_loger l2
                    WHERE 
                        l2.id_val = v.id_val 
                        OR l2.id_paie = paie.id_paie
                )

            WHERE l.chambre = ?
        ';

    $stmt = $link->prepare($sql);
    $stmt->bind_param('s', $chambre);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    return $data;
}

function getLitParChambre_2($link, $chambre)
{
    $sql = "

    /* ================== ETUDIANTS AFFECTÉS ================== */
    SELECT 
        l.id_lit, 
        l.lit, 
        af.niveauFormation,
        e.nom,
        e.prenoms,
        e.num_etu,
        cc.statut,

        v.dateTime_val,
        paie.dateTime_paie,

        /* LOGEMENT UNIFIÉ (IMPORTANT) */
        lg.dateTime_loger

    FROM codif_lit l

    LEFT JOIN codif_quota af 
        ON l.id_lit = af.id_lit_q

    LEFT JOIN codif_affectation cc 
        ON cc.id_lit = l.id_lit 

    LEFT JOIN codif_etudiant e 
        ON cc.id_etu = e.id_etu

    /* DERNIÈRE VALIDATION */
    LEFT JOIN codif_validation v 
        ON v.id_aff = cc.id_aff
        AND v.dateTime_val = (
            SELECT MAX(v2.dateTime_val)
            FROM codif_validation v2
            WHERE v2.id_aff = cc.id_aff
        )

    /* DERNIER PAIEMENT */
    LEFT JOIN codif_paiement paie 
        ON paie.id_val = v.id_val
        AND paie.dateTime_paie = (
            SELECT MAX(p2.dateTime_paie)
            FROM codif_paiement p2
            WHERE p2.id_val = v.id_val
        )

        /* DERNIER LOGEMENT DE L'ÉTUDIANT */
    LEFT JOIN codif_loger lg 
        ON lg.id_etu = e.id_etu
        AND lg.dateTime_loger = (
            SELECT MAX(l2.dateTime_loger)
            FROM codif_loger l2
            WHERE l2.id_etu = e.id_etu
        )

    WHERE l.chambre = ?

    UNION

    /* ================== ETUDIANTS HÉBERGÉS ================== */
    SELECT 
        l3.id_lit,
        l3.lit,
        e2.niveauFormation,
        e2.nom,
        e2.prenoms,
        e2.num_etu,
        lg2.statut,

        NULL AS dateTime_val,
        NULL AS dateTime_paie,

        lg2.dateTime_loger

    FROM codif_loger lg2

    JOIN codif_etudiant e2 
        ON lg2.id_etu = e2.id_etu

    JOIN codif_paiement p3
        ON lg2.id_paie = p3.id_paie

    JOIN codif_validation v3
        ON p3.id_val = v3.id_val

    JOIN codif_affectation a3
        ON v3.id_aff = a3.id_aff

    JOIN codif_lit l3
        ON a3.id_lit = l3.id_lit

    WHERE l3.chambre = ?

    /* éviter doublon attributaire */
    AND NOT EXISTS (
        SELECT 1
        FROM codif_affectation a
        WHERE a.id_etu = e2.id_etu
    )

    ORDER BY 
        FIELD(statut, 'Attributaire', 'Suppleant(e)', 'Clando'),
        lit ASC

    ";

    $stmt = $link->prepare($sql);
    $stmt->bind_param('ss', $chambre, $chambre);
    $stmt->execute();

    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    return $data;
}

/*
 * function getLitParChambre($link, $chambre) {
 *     // Requête SQL avec jointure gauche (LEFT JOIN)
 *     $sql = "SELECT
 *                 l.id_lit,
 *                 l.lit,
 *                 af.niveauFormation,
 *                 e.nom,
 *                 e.prenoms,
 *                 e.num_etu
 *             FROM codif_lit l
 *             LEFT JOIN codif_quota af ON l.id_lit = af.id_lit_q
 *             LEFT JOIN codif_affectation cc ON  cc.id_lit = l.id_lit
 *             LEFT JOIN codif_etudiant e ON  cc.id_etu = e.id_etu
 *             WHERE l.chambre = ?";
 *
 *     $stmt = $link->prepare($sql);
 *     $stmt->bind_param("s", $chambre);
 *     $stmt->execute();
 *     $result = $stmt->get_result();
 *
 *     // Tableau des résultats
 *     $data = [];
 *     while ($row = $result->fetch_assoc()) {
 *         $data[] = $row;
 *     }
 *     return $data;
 * }
 */

// RECUPERER LES LITS ET TITULAIRES AYANT VALIDER DONT LEURS SUPPLEANTS N'ONT PAS VALIDER
function getLitValAndNotSuppByFac($fac)
{
    global $connexion;

    $sql = "SELECT lit.lit, lit.indiv, etu.*
            FROM codif_lit lit
            JOIN codif_affectation aff ON aff.id_lit = lit.id_lit
            JOIN codif_etudiant etu ON etu.id_etu = aff.id_etu
            JOIN codif_validation val ON val.id_aff = aff.id_aff
            WHERE etu.etablissement = ?  and (lit.pavillon!='N' and lit.pavillon!='X')
            GROUP BY lit.id_lit
            HAVING COUNT(aff.id_lit) = 1
               AND lit.indiv = 0";  // lit NON individuel (modifiable selon ton besoin)

    $stmt = $connexion->prepare($sql);
    $stmt->bind_param('s', $fac);
    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getTitulaireAndSuppleantByFac($fac)
{
    // 1. récupérer les titulaires avec leurs lits
    $titulaireRows = getLitValAndNotSuppByFac($fac);

    $result = [];

    foreach ($titulaireRows as $row) {
        $num_etu = $row['num_etu'];
        $classe = $row['niveauFormation'];
        $sexe = $row['sexe'];

        // 2. récupérer le quota pour cette classe/sexe
        $quotaRow = getQuotaClasse($classe, $sexe);
        $quota = intval($quotaRow['COUNT(*)']);

        // 3. récupérer le statut + rang du titulaire
        $dataStatut = getOnestudentStatus($quota, $classe, $sexe, $num_etu);

        if (!$dataStatut) {
            // étudiant introuvable dans le classement
            continue;
        }

        $rangTitulaire = intval($dataStatut['rang']);

        // 4. trouver le suppléant correspondant
        $suppleant = getOneSuppleantByTitulaire($quota, $classe, $sexe, $rangTitulaire);

        // 5. construire la ligne finale
        $result[] = [
            'lit' => $row['lit'],
            'indiv' => $row['indiv'],
            // titulaire
            'titulaire' => [
                'num_etu' => $row['num_etu'],
                'nom' => $row['nom'],
                'prenom' => $row['prenoms'],
                'classe' => $classe,
                'sexe' => $sexe,
                'rang' => $rangTitulaire,
            ],
            // suppléant trouvé
            'suppleant' => $suppleant ? [
                'num_etu' => $suppleant['num_etu'],
                'nom' => $suppleant['nom'],
                'prenom' => $suppleant['prenoms'],
                'classe' => $classe,
                'sexe' => $sexe,
                'rang' => $suppleant['rang']
            ] : null
        ];
    }

    return $result;
}

function getLitNonVal($fac)
{
    global $connexion;

    $sql = "SELECT 
    l.id_lit,
    e.*,
    l.lit,
    l.sexe,
    l.chambre,
    l.pavillon
FROM codif_lit l
JOIN codif_quota q ON q.id_lit_q = l.id_lit
JOIN codif_affectation a ON a.id_lit = l.id_lit
JOIN codif_etudiant e ON e.id_etu = a.id_etu
WHERE l.id_lit NOT IN (
    SELECT cl.id_lit
    FROM codif_lit cl
    JOIN codif_affectation ca ON ca.id_lit = cl.id_lit
    INNER JOIN codif_validation cv ON cv.id_aff = ca.id_aff
    INNER JOIN codif_paiement cp ON cp.id_val = cv.id_val
)
AND e.etablissement=? AND l.pavillon != 'N' AND l.pavillon != 'X'
GROUP BY l.id_lit
ORDER BY e.etablissement;";  // lit NON individuel (modifiable selon ton besoin)

    $stmt = $connexion->prepare($sql);
    $stmt->bind_param('s', $fac);
    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getTitulaireAndSuppleantByFac2($fac)
{
    // 1. récupérer les titulaires avec leurs lits
    $titulaireRows = getLitNonVal($fac);

    $result = [];

    foreach ($titulaireRows as $row) {
        $num_etu = $row['num_etu'];
        $classe = $row['niveauFormation'];
        $sexe = $row['sexe'];

        // 2. récupérer le quota pour cette classe/sexe
        $quotaRow = getQuotaClasse($classe, $sexe);
        $quota = intval($quotaRow['COUNT(*)']);

        // 3. récupérer le statut + rang du titulaire
        $dataStatut = getOnestudentStatus($quota, $classe, $sexe, $num_etu);

        if (!$dataStatut) {
            // étudiant introuvable dans le classement
            continue;
        }

        $rangTitulaire = intval($dataStatut['rang']);

        // 4. trouver le suppléant correspondant
        $suppleant = getOneSuppleantByTitulaire($quota, $classe, $sexe, $rangTitulaire);

        // 5. construire la ligne finale
        $result[] = [
            'lit' => $row['lit'],
            // titulaire
            'titulaire' => [
                'num_etu' => $row['num_etu'],
                'nom' => $row['nom'],
                'prenom' => $row['prenoms'],
                'classe' => $classe,
                'sexe' => $sexe,
                'rang' => $rangTitulaire,
            ],
            // suppléant trouvé
            'suppleant' => $suppleant ? [
                'num_etu' => $suppleant['num_etu'],
                'nom' => $suppleant['nom'],
                'prenom' => $suppleant['prenoms'],
                'classe' => $classe,
                'sexe' => $sexe,
                'rang' => $suppleant['rang']
            ] : null
        ];
    }

    return $result;
}

function getAttributaireAndSuppleantByFac($fac)
{
    global $connexion;

    $result = [];

    $etuRows = getEtuNonAffByFac($fac);

    foreach ($etuRows as $etu) {
        $id_etu = $etu['id_etu'];
        $num_etu = $etu['num_etu'];
        $classe = $etu['niveauQuota'];
        $sexe = $etu['sexe'];

        // 3. récupérer quota classe + sexe
        $quotaRow = getQuotaClasse($classe, $sexe);
        $quota = intval($quotaRow['COUNT(*)']);

        // 4. statut + rang
        $dataStatut = getOnestudentStatus($quota, $classe, $sexe, $num_etu);
        if (!$dataStatut)
            continue;

        if ($dataStatut['statut'] !== 'Attributaire') {
            continue;
        }

        $rangTitulaire = intval($dataStatut['rang']);

        // 5. trouver suppléant
        $suppleant = getOneSuppleantByTitulaire($quota, $classe, $sexe, $rangTitulaire);

        // 6. push dans tableau
        $result[] = [
            'lit' => null,
            'indiv' => null,
            'titulaire' => [
                'num_etu' => $etu['num_etu'],
                'id_etu' => $etu['id_etu'],
                'nom' => $etu['nom'],
                'prenom' => $etu['prenoms'],
                'classe' => $classe,
                'sexe' => $sexe,
                'rang' => $rangTitulaire
            ],
            'suppleant' => $suppleant ? [
                'num_etu' => $suppleant['num_etu'],
                'id_etu' => $suppleant['id_etu'],
                'nom' => $suppleant['nom'],
                'prenom' => $suppleant['prenoms'],
                'classe' => $classe,
                'sexe' => $sexe,
                'rang' => $suppleant['rang']
            ] : null
        ];
    }

    //  TRIER PAR RANG DU TITULAIRE
    usort($result, function ($a, $b) {
        return $a['titulaire']['rang'] <=> $b['titulaire']['rang'];
    });

    return $result;
}

function getEtuNonAffByFac($fac)
{
    global $connexion;

    $sql = 'SELECT DISTINCT
        NULL AS chambre,
        NULL AS lit,
        q.niveauFormation AS niveauQuota,
        e.num_etu,
        e.nom,
        e.id_etu,
        e.sexe,
        e.prenoms,
        e.etablissement
    FROM codif_etudiant e
    JOIN codif_quota q 
        ON q.niveauFormation = e.niveauFormation
    WHERE e.id_etu NOT IN (SELECT id_etu FROM codif_affectation)
      AND q.id_lit_q NOT IN (SELECT id_lit FROM codif_affectation)
      AND e.etablissement = ? ;';

    $stmt = $connexion->prepare($sql);
    $stmt->bind_param('s', $fac);
    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

/*
 * function getLitNonAffByFac($fac) {
 *     global $connexion;
 *
 *     $sql = "SELECT
 *         lit.chambre,
 *         lit.lit,
 *         lit.sexe,
 *         q.niveauFormation AS niveauQuota,
 *         NULL AS num_etu,
 *         NULL AS nom,
 *         NULL AS prenoms,
 *
 *         -- Établissement filtré ici
 *         (
 *             SELECT etu.etablissement
 *             FROM codif_etudiant etu
 *             WHERE etu.niveauFormation = q.niveauFormation
 *               AND etu.etablissement = ?   -- FILTRE ÉTABLISSEMENT
 *             LIMIT 1
 *         ) AS etablissement
 *
 *     FROM codif_lit lit
 *     JOIN codif_quota q
 *         ON q.id_lit_q = lit.id_lit
 *     WHERE lit.id_lit NOT IN (SELECT id_lit FROM codif_affectation)
 *       AND EXISTS (
 *             SELECT 1
 *             FROM codif_etudiant etu
 *             WHERE etu.niveauFormation = q.niveauFormation
 *               AND etu.etablissement = ?   -- GARANTIR MÊME FILTRE
 *         );";   // lit NON individuel (modifiable selon ton besoin)
 *
 *     $stmt = $connexion->prepare($sql);
 *     $stmt->bind_param("ss", $fac,$fac);
 *     $stmt->execute();
 *
 *     return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
 * }
 */

function getLitNonAffByFac($fac)
{
    global $connexion;

    $sql = 'SELECT
        lit.chambre,
        lit.id_lit,
        lit.lit,
        lit.sexe,
        q.niveauFormation AS niveauQuota,
        NULL AS num_etu,
        NULL AS nom,
        NULL AS prenoms,

        -- Établissement filtré ici
        (
            SELECT etu.etablissement
            FROM codif_etudiant etu
            WHERE etu.niveauFormation = q.niveauFormation
              AND etu.etablissement = ?   -- FILTRE ÉTABLISSEMENT
            LIMIT 1
        ) AS etablissement
        
    FROM codif_lit lit
    JOIN codif_quota q 
        ON q.id_lit_q = lit.id_lit
    WHERE lit.id_lit NOT IN (SELECT DISTINCT id_lit FROM codif_affectation)
      AND EXISTS (
            SELECT 1
            FROM codif_etudiant etu
            WHERE etu.niveauFormation = q.niveauFormation
              AND etu.etablissement = ?   -- GARANTIR MÊME FILTRE
        );';  // lit NON individuel (modifiable selon ton besoin)

    $stmt = $connexion->prepare($sql);
    $stmt->bind_param('ss', $fac, $fac);
    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getBlack_list($connexion)
{
    $query = 'SELECT * FROM black_list';
    $result = mysqli_query($connexion, $query);

    if (!$result) {
        die("Erreur lors de l'exécution de la requête : " . mysqli_error($connexion));
    }

    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }

    return $rows;
}

function getBlackListInfo($num_etu, $connexion)
{
    $sql = 'SELECT reste_a_payer, annee, lit FROM black_list WHERE num_etu = ?';
    $stmt = $connexion->prepare($sql);
    $stmt->bind_param('s', $num_etu);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        return [
            'is_blacklisted' => true,
            'reste_a_payer' => floatval($row['reste_a_payer']),
            'annee' => $row['annee'],
            'lit' => $row['lit']
        ];
    }

    return [
        'is_blacklisted' => false,
        'reste_a_payer' => 0,
        'annee' => '',
        'lit' => ''
    ];
}

function getPaymentDetailsByDepartement($departementDonne, $connexion)
{
    $sql = "
        SELECT 
            e.departement,
            e.id_etu AS etudiant_id,
            e.num_etu AS num_etu,
            e.nom AS etudiant_nom,
            e.prenoms AS etudiant_prenoms,
            e.niveauFormation,
            e.telephone,
            l.indiv AS type_chambre,
            l.chambre,
            l.lit,
            lg.id_log AS log_id,
            lg.id_val AS validation_id,
            lg.id_paie AS paiement_id,
            lg.username_user AS utilisateur,
            a.rappel_envoye,
            lg.datetime_loger AS date_log,
            COALESCE((
                SELECT SUM(p.montant)
                FROM codif_paiement p
                WHERE p.id_val = v.id_val
            ), 0) AS montant_paye_total
        FROM 
            codif_etudiant e
        JOIN 
            codif_affectation a ON a.id_etu = e.id_etu
        JOIN 
            codif_validation v ON a.id_aff = v.id_aff
        JOIN 
            codif_lit l ON a.id_lit = l.id_lit
        LEFT JOIN 
            codif_loger lg ON lg.id_etu = e.id_etu  
        WHERE 
            e.departement = ? 
            AND (a.statut = 'Attributaire' OR a.id_aff IS NULL)
        GROUP BY 
            e.id_etu, l.chambre, l.lit, lg.id_log
        ORDER BY 
            e.nom, e.prenoms
    ";

    $stmt = $connexion->prepare($sql);
    $stmt->bind_param('s', $departementDonne);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];

    while ($row = $result->fetch_assoc()) {
        $etudiantId = $row['etudiant_id'];
        $etudiant_num = $row['num_etu'];

        // Obtenir le nombre de mois pour l’étudiant
        $nombreMois = getNbreMois2($etudiant_num);

        // Déterminer le prix du lit selon le type de chambre
        $prixLit = getMontant($row['type_chambre']);

        // Calcul du montant facturé (+5000 si caution)
        if (verifCaution($etudiantId)) {
            $montantFacture = ($nombreMois * $prixLit) + 5000;
        } else {
            $montantFacture = $nombreMois * $prixLit;
        }

        $montantPaye = $row['montant_paye_total'] ?? 0;
        $resteAPayer = $montantFacture - $montantPaye;

        //  On garde uniquement ceux qui doivent encore de l’argent
        if ($resteAPayer > 0) {
            $data[] = [
                'departement' => $row['departement'],
                'etudiant_id' => $row['etudiant_id'],
                'num_etu' => $row['num_etu'],
                'etudiant_nom' => $row['etudiant_nom'],
                'niveauFormation' => $row['niveauFormation'],
                'telephone' => $row['telephone'],
                'etudiant_prenoms' => $row['etudiant_prenoms'],
                'chambre' => $row['chambre'],
                'lit' => $row['lit'],
                'montant_facture' => $montantFacture,
                'montant_paye' => $montantPaye,
                'reste_a_payer' => $resteAPayer,
                'log_id' => $row['log_id'],
                'validation_id' => $row['validation_id'],
                'paiement_id' => $row['paiement_id'],
                'utilisateur' => $row['utilisateur'],
                'rappel_envoye' => $row['rappel_envoye'],
                'date_log' => $row['date_log']
            ];
        }
    }

    $stmt->close();
    return $data;
}

function getPaymentDetailsByFaculter($faculteDonnee, $connexion)
{
    $sql = "
        SELECT 
            e.departement,
            e.etablissement,
            e.id_etu AS etudiant_id,
            e.num_etu AS num_etu,
            e.nom AS etudiant_nom,
            e.prenoms AS etudiant_prenoms,
            e.niveauFormation,
            e.telephone,
            l.indiv AS type_chambre,
            l.chambre,
            l.lit,
            lg.id_log AS log_id,
            lg.id_val AS validation_id,
            lg.id_paie AS paiement_id,
            lg.username_user AS utilisateur,
            a.rappel_envoye,
            lg.datetime_loger AS date_log,
            COALESCE((
                SELECT SUM(p.montant)
                FROM codif_paiement p
                WHERE p.id_val = v.id_val
            ), 0) AS montant_paye_total
        FROM 
            codif_etudiant e
        JOIN 
            codif_affectation a ON a.id_etu = e.id_etu
        JOIN 
            codif_validation v ON a.id_aff = v.id_aff
        JOIN 
            codif_lit l ON a.id_lit = l.id_lit
        LEFT JOIN 
            codif_loger lg ON lg.id_etu = e.id_etu  
        WHERE 
            e.etablissement = ? 
            AND (a.statut = 'Attributaire' OR a.id_aff IS NULL) AND e.num_etu NOT IN (
    SELECT b.num_etu COLLATE utf8mb4_unicode_ci
    FROM black_list b
)
        GROUP BY 
            e.id_etu, l.chambre, l.lit, lg.id_log
        ORDER BY 
            e.nom, e.prenoms
    ";

    $stmt = $connexion->prepare($sql);
    $stmt->bind_param('s', $faculteDonnee);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];

    while ($row = $result->fetch_assoc()) {
        $etudiantId = $row['etudiant_id'];
        $etudiant_num = $row['num_etu'];

        // Obtenir le nombre de mois pour l’étudiant
        $nombreMois = getNbreMois2($etudiant_num);

        // Déterminer le prix du lit selon le type de chambre
        $prixLit = getMontant($row['type_chambre']);

        // Calcul du montant facturé (+5000 si caution)
        if (verifCaution($etudiantId)) {
            $montantFacture = ($nombreMois * $prixLit) + 5000;
        } else {
            $montantFacture = $nombreMois * $prixLit;
        }

        $montantPaye = $row['montant_paye_total'] ?? 0;
        $resteAPayer = $montantFacture - $montantPaye;

        //  On garde uniquement ceux qui doivent encore de l’argent
        if ($resteAPayer > 0) {
            $data[] = [
                'departement' => $row['departement'],
                'etablissement' => $row['etablissement'],
                'etudiant_id' => $row['etudiant_id'],
                'num_etu' => $row['num_etu'],
                'etudiant_nom' => $row['etudiant_nom'],
                'niveauFormation' => $row['niveauFormation'],
                'telephone' => $row['telephone'],
                'etudiant_prenoms' => $row['etudiant_prenoms'],
                'chambre' => $row['chambre'],
                'lit' => $row['lit'],
                'montant_facture' => $montantFacture,
                'montant_paye' => $montantPaye,
                'reste_a_payer' => $resteAPayer,
                'log_id' => $row['log_id'],
                'validation_id' => $row['validation_id'],
                'paiement_id' => $row['paiement_id'],
                'utilisateur' => $row['utilisateur'],
                'rappel_envoye' => $row['rappel_envoye'],
                'date_log' => $row['date_log']
            ];
        }
    }

    $stmt->close();
    return $data;
}

function getDepartements()
{
    global $connexion;

    $sql = "
        SELECT DISTINCT(departement) AS dep
        FROM codif_etudiant
        WHERE departement IS NOT NULL
          AND departement <> ''
          AND departement COLLATE utf8mb4_unicode_ci NOT IN (
              SELECT DISTINCT(departement) COLLATE utf8mb4_unicode_ci
              FROM black_list
          )
        ORDER BY departement
    ";

    $resultat = mysqli_query($connexion, $sql);

    $departements = [];
    if ($resultat && mysqli_num_rows($resultat) > 0) {
        while ($row = mysqli_fetch_assoc($resultat)) {
            $departements[] = $row['dep'];
        }
    }

    return $departements;
}

function getCampusByPavillon($connexion, $pavillon)
{
    // Échapper la valeur pour éviter l'injection SQL
    $pavillon = mysqli_real_escape_string($connexion, $pavillon);

    $query = "SELECT DISTINCT campus FROM codif_lit_complet WHERE pavillon='$pavillon' LIMIT 1";
    $result = mysqli_query($connexion, $query);

    if (!$result) {
        die("Erreur lors de l'exécution de la requête : " . mysqli_error($connexion));
    }

    // Récupérer le campus
    $row = mysqli_fetch_assoc($result);
    return $row['campus'] ?? null;  // Retourne le campus ou null si aucun résultat
}

function getLitByPcs($fac)
{
    global $connexion;

    $sql = "
        SELECT l.lit
        FROM codif_lit AS l
        JOIN codif_quota q ON q.id_lit_q = l.id_lit
        WHERE q.niveauFormation LIKE ? COLLATE utf8mb4_unicode_ci
        AND q.niveauFormation COLLATE utf8mb4_unicode_ci 
            LIKE (
                SELECT CONCAT(niveauFormation, '%') COLLATE utf8mb4_unicode_ci
                FROM codif_demarrage
                WHERE niveauFormation LIKE ? COLLATE utf8mb4_unicode_ci
                LIMIT 1
            )
    ";

    $stmt = $connexion->prepare($sql);

    // Exemple : FSLH/SOCIALE%
    $param = $fac . '%';

    $stmt->bind_param('ss', $param, $param);
    $stmt->execute();

    $result = $stmt->get_result();

    $lits = [];
    while ($row = $result->fetch_assoc()) {
        $lits[] = $row['lit'];
    }

    return $lits;
}

function isLitIndividuel($lit)
{
    global $connexion;
    $query = "SELECT indiv FROM codif_lit WHERE lit = '$lit' LIMIT 1";
    $result = mysqli_query($connexion, $query);

    if ($row = mysqli_fetch_assoc($result)) {
        return (int) $row['indiv'] === 1;
    }

    return false;  // si lit introuvable ou indiv = 0
}

function getSexeLit($lit)
{
    global $connexion;
    $query = "SELECT sexe FROM codif_lit WHERE lit = '$lit' LIMIT 1";
    $result = mysqli_query($connexion, $query);

    if ($row = mysqli_fetch_assoc($result)) {
        return $row['sexe'];
    }

    return 'NaN';
}

function getIdByNumCarte($num_carte)
{
    global $connexion;

    $sql = 'SELECT id_etu FROM codif_etudiant WHERE num_etu = ?';
    $stmt = $connexion->prepare($sql);
    $stmt->bind_param('s', $num_carte);
    $stmt->execute();

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    return $row ? $row['id_etu'] : null;
}

function getIdByLit($lit)
{
    global $connexion;

    $sql = 'SELECT id_lit FROM codif_lit WHERE lit = ?';
    $stmt = $connexion->prepare($sql);
    $stmt->bind_param('s', $lit);
    $stmt->execute();

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    return $row ? $row['id_lit'] : null;
}

function isAffecte($idEtu)
{
    global $connexion;
    $sql = 'SELECT id_aff FROM codif_affectation WHERE id_etu = ? LIMIT 1';
    $stmt = mysqli_prepare($connexion, $sql);

    if (!$stmt)
        return false;

    mysqli_stmt_bind_param($stmt, 'i', $idEtu);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    return mysqli_num_rows($result) > 0;
}

function getAffectationByLit($lit)
{
    global $connexion;
    $sql = '
        SELECT 
            a.id_lit,
            a.id_etu,
            l.lit,
            e.num_etu,
            e.prenoms,
            e.nom,
            e.sexe,
            e.telephone,
            e.etablissement as faculte,
            e.departement
        FROM codif_affectation a
        JOIN codif_lit l ON l.id_lit = a.id_lit
        LEFT JOIN codif_etudiant e ON e.id_etu = a.id_etu
        WHERE l.lit = ?
    ';

    $stmt = $connexion->prepare($sql);
    $stmt->execute([$lit]);
    return $stmt->fetch();
}

// -> À placer dans traitement/fonction.php
// Utilise l'objet mysqli $connexion attendu dans ton projet.

function getOccupantsByLitName($litName)
{
    global $connexion;
    $sql = '
        SELECT e.id_etu AS id_etu, e.num_etu, e.prenoms, e.nom
        FROM codif_affectation a
        INNER JOIN codif_lit l ON l.id_lit = a.id_lit
        LEFT JOIN codif_etudiant e ON e.id_etu = a.id_etu
        WHERE l.lit = ?
    ';
    $stmt = mysqli_prepare($connexion, $sql);
    if (!$stmt)
        return [];
    mysqli_stmt_bind_param($stmt, 's', $litName);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $rows = [];
    while ($r = mysqli_fetch_assoc($res))
        $rows[] = $r;
    return $rows;  // array vide si aucun occupant
}

function getOccupantsCountByLitName($litName)
{
    global $connexion;
    $sql = '
        SELECT COUNT(*) AS total
        FROM codif_affectation a
        INNER JOIN codif_lit l ON l.id = a.id_lit
        WHERE l.lit = ?
        /* AND (a.actif IS NULL OR a.actif = 1) */
    ';
    $stmt = mysqli_prepare($connexion, $sql);
    if (!$stmt)
        return 0;
    mysqli_stmt_bind_param($stmt, 's', $litName);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($res);
    return isset($row['total']) ? (int) $row['total'] : 0;
}

function getFirstOccupantByLitName($litName)
{
    $list = getOccupantsByLitName($litName);
    return count($list) ? $list[0] : null;
}

function getOccupantsByLit($lit)
{
    global $connexion;

    $sql = '
        SELECT 
            a.id_aff,
            a.id_etu,
            e.num_etu,
            e.prenoms,
            e.nom,
            e.sexe,
            e.telephone,
            e.etablissement AS faculte,
            e.departement,
            e.NiveauFormation
        FROM codif_affectation a
        JOIN codif_lit l ON l.id_lit = a.id_lit
        LEFT JOIN codif_etudiant e ON e.id_etu = a.id_etu
        WHERE l.lit = ?
        ORDER BY a.id_aff ASC
    ';

    $stmt = $connexion->prepare($sql);
    $stmt->bind_param('s', $lit);  // ici mysqli utilise bind_param
    $stmt->execute();

    $result = $stmt->get_result();  // récupérer le résultat

    if (!$result) {
        return [];
    }

    return $result->fetch_all(MYSQLI_ASSOC);  // récupérer toutes les lignes en tableau associatif
}

function isAffecteDansUnAutreLit($idEtu, $lit)
{
    global $connexion;

    $sql = '
        SELECT 1 
        FROM codif_affectation a
        JOIN codif_lit l ON l.id_lit = a.id_lit
        WHERE a.id_etu = ?
          AND l.lit != ?
        LIMIT 1
    ';

    $stmt = mysqli_prepare($connexion, $sql);
    if (!$stmt)
        return false;

    mysqli_stmt_bind_param($stmt, 'is', $idEtu, $lit);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    return mysqli_stmt_num_rows($stmt) > 0;
}

function getAffectationById($id_aff)
{
    global $connexion;
    $sql = 'SELECT * FROM codif_affectation WHERE id_aff=?';
    $stmt = $connexion->prepare($sql);
    $stmt->bind_param('i', $id_aff);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function deleteAffectation2($id_aff)
{
    global $connexion;
    $sql = 'DELETE FROM codif_affectation WHERE id_aff=?';
    $stmt = $connexion->prepare($sql);
    $stmt->bind_param('i', $id_aff);
    $stmt->execute();
}

function resetEtudiantNiveauFormation($idEtu)
{
    global $connexion;
    // remettre à vide ou NULL
    $sql = 'UPDATE codif_etudiant SET niveauFormation=NULL WHERE id_etu=?';
    $stmt = $connexion->prepare($sql);
    $stmt->bind_param('i', $idEtu);
    $stmt->execute();
}

function updatequota($connexion, $id_lit, $niveauFormation)
{
    $sql = 'UPDATE codif_quota SET niveauFormation=? WHERE id_lit_q=?';
    $stmt = mysqli_prepare($connexion, $sql);
    mysqli_stmt_bind_param($stmt, 'si', $niveauFormation, $id_lit);
    mysqli_stmt_execute($stmt);
}

function isAffectationValidee($id_aff)
{
    global $connexion;

    $sql = 'SELECT COUNT(*) AS total FROM codif_validation WHERE id_aff = ?';
    $stmt = $connexion->prepare($sql);
    $stmt->bind_param('i', $id_aff);
    $stmt->execute();

    $result = $stmt->get_result();
    if (!$result) {
        return false;
    }

    $row = $result->fetch_assoc();
    return ($row['total'] > 0);
}

function supprimerPaiement($connexion, $id_paie, $deleted_by)
{
    // Sécurisation
    $id_paie = mysqli_real_escape_string($connexion, $id_paie);
    $deleted_by = mysqli_real_escape_string($connexion, $deleted_by);

    // 1️ Récupérer le paiement avant suppression
    $sqlSelect = "SELECT * FROM codif_paiement WHERE id_paie = $id_paie";
    $result = mysqli_query($connexion, $sqlSelect);

    if ($row = mysqli_fetch_assoc($result)) {
        // Échapper les données
        $id_val = mysqli_real_escape_string($connexion, $row['id_val']);
        $montant = mysqli_real_escape_string($connexion, $row['montant']);
        $libelle = mysqli_real_escape_string($connexion, $row['libelle']);
        $username_user = mysqli_real_escape_string($connexion, $row['username_user']);
        $quittance = mysqli_real_escape_string($connexion, $row['quittance']);
        $an = mysqli_real_escape_string($connexion, $row['an']);
        $num_ordre_user = mysqli_real_escape_string($connexion, $row['num_ordre_user']);
        $dateTime_paie = mysqli_real_escape_string($connexion, $row['dateTime_paie']);

        // 2️ Archiver le paiement
        $sqlArchive = "INSERT INTO codif_archiv_acp_suppr
            (id_paie, id_val, montant, libelle, username_user, quittance, an, num_ordre_user, dateTime_paie, deleted_at, deleted_by)
            VALUES 
            ($id_paie, '$id_val', '$montant', '$libelle', '$username_user', '$quittance', '$an', '$num_ordre_user', '$dateTime_paie', NOW(), '$deleted_by')";

        if (!mysqli_query($connexion, $sqlArchive)) {
            return 'Erreur lors de l’archivage du paiement.';
        }

        // 3️ Supprimer le paiement
        $sqlDelete = "DELETE FROM codif_paiement WHERE id_paie = $id_paie";
        if (mysqli_query($connexion, $sqlDelete)) {
            return 'Paiement supprimé avec succès.';
        } else {
            return 'Erreur lors de la suppression du paiement.';
        }
    } else {
        return 'Paiement introuvable.';
    }
}

/*
 * function getLitEtudiant($link, $lit)
 * {
 *     // Requête SQL avec jointure gauche (LEFT JOIN)
 *     $sql = 'SELECT
 *                 l.id_lit,
 *                 l.lit,
 *                 af.niveauFormation,
 *                 e.nom,
 *                 e.prenoms,
 *                 e.num_etu,
 *                 e.id_etu
 *             FROM codif_lit l
 *             LEFT JOIN codif_quota af ON l.id_lit = af.id_lit_q
 *             LEFT JOIN codif_affectation cc ON  cc.id_lit = l.id_lit
 *             LEFT JOIN codif_etudiant e ON  cc.id_etu = e.id_etu
 *             WHERE l.lit = ?';
 *
 *     $stmt = $link->prepare($sql);
 *     $stmt->bind_param('s', $lit);
 *     $stmt->execute();
 *     $result = $stmt->get_result();
 *
 *     // Tableau des résultats
 *     $data = [];
 *     while ($row = $result->fetch_assoc()) {
 *         $data[] = $row;
 *     }
 *     return $data;
 * }
 */

function getLitEtudiant($link, $lit)
{
    $sql = '
        SELECT 
            l.id_lit,
            l.lit,
            af.niveauFormation,
            e.id_etu,
            e.nom,
            e.sexe,
            e.telephone,
            e.prenoms,
            e.num_etu,
            cc.statut,
            cc.id_aff
        FROM codif_lit l
        JOIN codif_affectation cc 
            ON cc.id_lit = l.id_lit
        JOIN codif_etudiant e 
            ON e.id_etu = cc.id_etu
        LEFT JOIN codif_quota af 
            ON af.id_lit_q = l.id_lit
        WHERE l.lit = ?
          AND NOT EXISTS (
              SELECT 1
              FROM codif_validation v
              WHERE v.id_aff = cc.id_aff
          )
    ';

    $stmt = $link->prepare($sql);
    $stmt->bind_param('s', $lit);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    if (!empty($data)) {
        $etudiant = $data[0];

        $quotaRow = getQuotaClasse($etudiant['niveauFormation'], $etudiant['sexe']);
        $quota = (int) ($quotaRow['COUNT(*)'] ?? 0);

        $statut = getOnestudentStatus(
            $quota,
            $etudiant['niveauFormation'],
            $etudiant['sexe'],
            $etudiant['num_etu']
        );

        $rang = (int) ($statut['rang'] ?? 0);

        $data[0]['suppleant'] = ($rang > 0)
            ? getOneSuppleantByTitulaire(
                $quota,
                $etudiant['niveauFormation'],
                $etudiant['sexe'],
                $rang
            )
            : null;
    }

    return $data;
}

function getPaiementWithDateInterval_3_old($date_debut, $date_fin, $username, $libelle = '', $page = 1, $limit = 20)
{
    global $connexion;

    // Sécuriser les entrées
    $date_debut = !empty($date_debut) ? mysqli_real_escape_string($connexion, $date_debut) : '2025-01-01';
    $date_fin = !empty($date_fin) ? mysqli_real_escape_string($connexion, $date_fin) : date('Y-m-d');
    $username = !empty($username) ? mysqli_real_escape_string($connexion, $username) : '';
    $libelleFilter = $libelle;
    $libelle = !empty($libelle) ? '%' . mysqli_real_escape_string($connexion, $libelle) . '%' : '';

    // Calcul de l'offset
    $page = max(1, (int) $page);
    $limit = max(1, (int) $limit);
    $offset = ($page - 1) * $limit;

    // Construire la requête SQL principale
    $sql = "SELECT ce.num_etu, ce.nom, ce.prenoms, pc.id_paie, pc.dateTime_paie, pc.montant, pc.an, 
                   pc.id_val, pc.quittance, pc.username_user, pc.libelle 
            FROM codif_etudiant ce 
            JOIN codif_affectation a ON ce.id_etu = a.id_etu 
            JOIN codif_validation vl ON a.id_aff = vl.id_aff 
            JOIN codif_paiement pc ON pc.id_val = vl.id_val 
            WHERE pc.dateTime_paie >= '$date_debut' AND pc.dateTime_paie <= '$date_fin'";

    if (!empty($username)) {
        $sql .= " AND pc.username_user = '$username'";
    }

    if (!empty($libelle)) {
        if ($libelleFilter === 'LOYER') {
            $sql .= " AND pc.libelle != 'CAUTION'";
        } else {
            $sql .= " AND pc.libelle LIKE '$libelle'";
        }
    }

    $sql .= ' ORDER BY pc.dateTime_paie DESC, pc.quittance DESC, ce.nom ASC';
    $sql .= " LIMIT $limit OFFSET $offset";

    $result = $connexion->query($sql);
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    // Calcul du montant total
    $totalMontant = 0;

    if (empty($libelleFilter)) {
        $sqlTotal = "SELECT SUM(pc.montant) AS montantTotal 
                     FROM codif_paiement pc
                     JOIN codif_validation vl ON pc.id_val = vl.id_val
                     WHERE pc.dateTime_paie >= '$date_debut' AND pc.dateTime_paie <= '$date_fin'";
        if (!empty($username)) {
            $sqlTotal .= " AND pc.username_user = '$username'";
        }
    } elseif ($libelleFilter === 'CAUTION') {
        $sqlTotal = "SELECT COUNT(pc.montant) AS countPayments 
                     FROM codif_paiement pc
                     JOIN codif_validation vl ON pc.id_val = vl.id_val
                     WHERE pc.dateTime_paie >= '$date_debut' AND pc.dateTime_paie <= '$date_fin'";
        if (!empty($username)) {
            $sqlTotal .= " AND pc.username_user = '$username'";
        }
        $sqlTotal .= " AND pc.libelle LIKE '%CAUTION%'";
    } elseif ($libelleFilter === 'LOYER') {
        $sqlTotal = "SELECT SUM(
                        CASE 
                        WHEN pc.libelle LIKE '%CAUTION%' 
                        THEN pc.montant - 5000 
                        ELSE pc.montant 
                        END
                     ) AS montantTotal
                     FROM codif_paiement pc
                     JOIN codif_validation vl ON pc.id_val = vl.id_val
                     WHERE pc.dateTime_paie >= '$date_debut' AND pc.dateTime_paie <= '$date_fin'";
        if (!empty($username)) {
            $sqlTotal .= " AND pc.username_user = '$username'";
        }
        $sqlTotal .= " AND pc.libelle NOT LIKE 'CAUTION'";
    }

    $resultTotal = $connexion->query($sqlTotal);
    if ($rowTotal = $resultTotal->fetch_assoc()) {
        $totalMontant = isset($rowTotal['montantTotal'])
            ? $rowTotal['montantTotal']
            : (isset($rowTotal['countPayments']) ? $rowTotal['countPayments'] * 5000 : 0);
    }

    // Calcul du nombre total de lignes pour pagination
    $sqlCount = "SELECT COUNT(*) AS totalRows 
                 FROM codif_paiement pc
                 JOIN codif_validation vl ON pc.id_val = vl.id_val
                 WHERE pc.dateTime_paie >= '$date_debut' AND pc.dateTime_paie <= '$date_fin'";

    if (!empty($username)) {
        $sqlCount .= " AND pc.username_user = '$username'";
    }

    if (!empty($libelle)) {
        if ($libelleFilter === 'LOYER') {
            $sqlCount .= " AND pc.libelle != 'CAUTION'";
        } else {
            $sqlCount .= " AND pc.libelle LIKE '$libelle'";
        }
    }

    $resultCount = $connexion->query($sqlCount);
    $totalRows = $resultCount->fetch_assoc()['totalRows'];
    $totalPages = ceil($totalRows / $limit);

    return [
        'data' => $data,
        'totalMontant' => $totalMontant,
        'page' => $page,
        'limit' => $limit,
        'totalRows' => $totalRows,
        'totalPages' => $totalPages
    ];
}

function getPaiementWithDateInterval_3($date_debut, $date_fin, $username, $libelle = '', $page = 1, $limit = 20)
{
    global $connexion;

    $date_debut = !empty($date_debut)
        ? mysqli_real_escape_string($connexion, $date_debut)
        : '2025-01-01';

    $date_fin = !empty($date_fin)
        ? mysqli_real_escape_string($connexion, $date_fin)
        : date('Y-m-d');

    $username = !empty($username)
        ? mysqli_real_escape_string($connexion, $username)
        : '';

    $libelleFilter = $libelle;

    $libelle = !empty($libelle)
        ? '%' . mysqli_real_escape_string($connexion, $libelle) . '%'
        : '';

    $page = max(1, (int)$page);
    $limit = max(1, (int)$limit);
    $offset = ($page - 1) * $limit;

    $baseQuery = "
    (
        SELECT
            ce.num_etu,
            ce.nom,
            ce.prenoms,
            pc.id_paie,
            pc.dateTime_paie,
            pc.montant,
            pc.an,
            pc.id_val,
            pc.quittance,
            pc.username_user,
            pc.libelle
        FROM codif_etudiant ce
        INNER JOIN codif_affectation a
            ON ce.id_etu = a.id_etu
        INNER JOIN codif_validation vl
            ON a.id_aff = vl.id_aff
        INNER JOIN codif_paiement pc
            ON pc.id_val = vl.id_val

        UNION ALL

        SELECT
            ce.num_etu,
            ce.nom,
            ce.prenoms,
            pc.id_paie,
            pc.dateTime_paie,
            pc.montant,
            pc.an,
            pc.id_val,
            pc.quittance,
            pc.username_user,
            pc.libelle
        FROM codif_etudiant ce
        INNER JOIN codif_paiement pc
            ON pc.id_etu = ce.id_etu
        WHERE pc.id_val IS NULL
           OR pc.id_val = 0
    ) p
    ";

    // =========================
    // LISTE DES PAIEMENTS
    // =========================

    $sql = "
    SELECT *
    FROM $baseQuery
    WHERE dateTime_paie >= '$date_debut'
      AND dateTime_paie <= '$date_fin'
    ";

    if (!empty($username)) {
        $sql .= " AND username_user = '$username'";
    }

    if (!empty($libelle)) {
        if ($libelleFilter === 'LOYER') {
            $sql .= " AND libelle <> 'CAUTION'";
        } else {
            $sql .= " AND libelle LIKE '$libelle'";
        }
    }

    $sql .= "
    ORDER BY dateTime_paie DESC,
             quittance DESC,
             nom ASC
    LIMIT $limit OFFSET $offset
    ";

    $result = $connexion->query($sql);

    $data = [];

    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    // =========================
    // TOTAL MONTANT
    // =========================

    $totalMontant = 0;

    if (empty($libelleFilter)) {

        $sqlTotal = "
        SELECT SUM(montant) AS montantTotal
        FROM $baseQuery
        WHERE dateTime_paie >= '$date_debut'
          AND dateTime_paie <= '$date_fin'
        ";

        if (!empty($username)) {
            $sqlTotal .= " AND username_user = '$username'";
        }

    } elseif ($libelleFilter === 'CAUTION') {

        $sqlTotal = "
        SELECT COUNT(*) AS countPayments
        FROM $baseQuery
        WHERE dateTime_paie >= '$date_debut'
          AND dateTime_paie <= '$date_fin'
        ";

        if (!empty($username)) {
            $sqlTotal .= " AND username_user = '$username'";
        }

        $sqlTotal .= " AND libelle LIKE '%CAUTION%'";

    } elseif ($libelleFilter === 'LOYER') {

        $sqlTotal = "
        SELECT
        SUM(
            CASE
                WHEN libelle LIKE '%CAUTION%'
                THEN montant - 5000
                ELSE montant
            END
        ) AS montantTotal
        FROM $baseQuery
        WHERE dateTime_paie >= '$date_debut'
          AND dateTime_paie <= '$date_fin'
        ";

        if (!empty($username)) {
            $sqlTotal .= " AND username_user = '$username'";
        }

        $sqlTotal .= " AND libelle <> 'CAUTION'";
    }

    $resultTotal = $connexion->query($sqlTotal);

    if ($rowTotal = $resultTotal->fetch_assoc()) {

        if (isset($rowTotal['montantTotal'])) {
            $totalMontant = $rowTotal['montantTotal'];
        } elseif (isset($rowTotal['countPayments'])) {
            $totalMontant = $rowTotal['countPayments'] * 5000;
        }
    }

    // =========================
    // TOTAL LIGNES
    // =========================

    $sqlCount = "
    SELECT COUNT(*) AS totalRows
    FROM $baseQuery
    WHERE dateTime_paie >= '$date_debut'
      AND dateTime_paie <= '$date_fin'
    ";

    if (!empty($username)) {
        $sqlCount .= " AND username_user = '$username'";
    }

    if (!empty($libelle)) {

        if ($libelleFilter === 'LOYER') {
            $sqlCount .= " AND libelle <> 'CAUTION'";
        } else {
            $sqlCount .= " AND libelle LIKE '$libelle'";
        }
    }

    $resultCount = $connexion->query($sqlCount);

    $totalRows = $resultCount->fetch_assoc()['totalRows'];

    $totalPages = ceil($totalRows / $limit);

    return [
        'data' => $data,
        'totalMontant' => $totalMontant,
        'page' => $page,
        'limit' => $limit,
        'totalRows' => $totalRows,
        'totalPages' => $totalPages
    ];
}

function getEtuNonAffByFac_3($fac)
{
    global $connexion;

    $sql = '
    SELECT 
        NULL AS chambre,
        NULL AS lit,

        e.niveauFormation AS niveauQuota,
        e.num_etu,
        e.nom,
        e.id_etu,
        e.sexe,
        e.prenoms,
        e.etablissement,

        rq.nombre,
        r.rang,

        /* ===== STATUT ===== */
        CASE 
            WHEN rq.nombre IS NULL OR rq.nombre = 0 THEN "Non Defini"
            WHEN cf.id_etu IS NOT NULL THEN "Forclos(e)"
            WHEN r.rang <= rq.nombre THEN "Attributaire"
            WHEN r.rang <= rq.nombre * 2 THEN "Suppleant(e)"
            ELSE "Non Attributaire"
        END AS statut,

        /* ===== SUPPLEANT ===== */
        s.id_etu AS id_suppleant,
        s.nom AS nom_suppleant,
        s.num_etu AS num_suppleant,
        s.prenoms AS prenom_suppleant,
        s.niveauFormation AS niveau_suppleant,
        r2.rang AS rang_suppleant

    FROM codif_etudiant e

    /* ===== QUOTA PAR NIVEAU + SEXE ===== */
    LEFT JOIN (
        SELECT 
            q.niveauFormation,
            l.sexe,
            COUNT(*) AS nombre
        FROM codif_quota q
        JOIN codif_lit l ON l.id_lit = q.id_lit_q
        GROUP BY q.niveauFormation, l.sexe
    ) rq 
        ON rq.niveauFormation = e.niveauFormation
        AND rq.sexe = e.sexe

    /* ===== CLASSEMENT UNIQUE ===== */
    LEFT JOIN (
        SELECT 
            id_etu,
            niveauFormation,
            sexe,
            ROW_NUMBER() OVER (
                PARTITION BY niveauFormation, sexe
                ORDER BY sessionId ASC, moyenne DESC, id_etu ASC, dateNaissance ASC
            ) AS rang
        FROM codif_etudiant
        WHERE NOT EXISTS (
            SELECT 1 
            FROM codif_forclusion f 
            WHERE f.id_etu = codif_etudiant.id_etu
        )
    ) r 
        ON e.id_etu = r.id_etu

    /* ===== SUPPLEANT = MEME GROUPE + DECALAGE QUOTA ===== */
    LEFT JOIN (
        SELECT 
            id_etu,
            niveauFormation,
            sexe,
            ROW_NUMBER() OVER (
                PARTITION BY niveauFormation, sexe
                ORDER BY sessionId ASC, moyenne DESC, id_etu ASC, dateNaissance ASC
            ) AS rang
        FROM codif_etudiant
        WHERE NOT EXISTS (
            SELECT 1 
            FROM codif_forclusion f 
            WHERE f.id_etu = codif_etudiant.id_etu
        )
    ) r2 
        ON r2.niveauFormation = r.niveauFormation
        AND r2.sexe = r.sexe
        AND r2.rang = r.rang + rq.nombre

    /* ===== INFOS SUPPLEANT ===== */
    LEFT JOIN codif_etudiant s 
        ON s.id_etu = r2.id_etu

    /* ===== FORCLUSION ===== */
    LEFT JOIN codif_forclusion cf 
        ON e.id_etu = cf.id_etu

    /* ===== FILTRES ===== */
    WHERE NOT EXISTS (
        SELECT 1 
        FROM codif_affectation a 
        WHERE a.id_etu = e.id_etu
    )
    AND e.etablissement = ?
    AND cf.id_etu IS NULL
    AND r.rang <= rq.nombre

    ORDER BY r.rang ASC;
    ';

    $stmt = $connexion->prepare($sql);
    $stmt->bind_param('s', $fac);
    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getStatsByFacAndNiveau($fac, $sexe)
{
    global $connexion;

    $sql = '
    SELECT 
        e.niveauFormation,
        e.sexe,
        /* Total étudiants */
        COUNT(DISTINCT e.id_etu) AS total_etudiants,
        /* Quota */
        COALESCE(q.quota,0) AS quota,
        /* Affectés */
        COALESCE(a.nb_affecte,0) AS nb_affecte,
        /* Validés */
        COALESCE(v.nb_valide,0) AS nb_valide,
        /* Payés */
        COALESCE(p.nb_paye,0) AS nb_paye,
        /* Logés attributaires */
        COALESCE(la.nb_loge_attr,0) AS nb_loge_attributaire,
        /* Logés suppléants */
        COALESCE(ls.nb_loge_supp,0) AS nb_loge_suppleant,
        /* Total logés */
        COALESCE(la.nb_loge_attr,0) + COALESCE(ls.nb_loge_supp,0) AS total_loge,
        /* Taux logés */
        ROUND(
            (COALESCE(la.nb_loge_attr,0) + COALESCE(ls.nb_loge_supp,0)) / NULLIF(COUNT(DISTINCT e.id_etu),0) * 100,
            2
        ) AS taux_loge
    FROM codif_etudiant e

    /* Quota */
    LEFT JOIN (
        SELECT 
            q.niveauFormation,
            l.sexe,
            COUNT(*) AS quota
        FROM codif_quota q
        JOIN codif_lit l ON l.id_lit = q.id_lit_q
        GROUP BY q.niveauFormation, l.sexe
    ) q ON q.niveauFormation = e.niveauFormation AND q.sexe = e.sexe

    /* Affectés */
    LEFT JOIN (
        SELECT e.niveauFormation, e.sexe, COUNT(DISTINCT a.id_aff) AS nb_affecte
        FROM codif_affectation a
        JOIN codif_etudiant e ON e.id_etu = a.id_etu
        GROUP BY e.niveauFormation, e.sexe
    ) a ON a.niveauFormation = e.niveauFormation AND a.sexe = e.sexe

    /* Validés */
    LEFT JOIN (
        SELECT e.niveauFormation, e.sexe, COUNT(DISTINCT v.id_val) AS nb_valide
        FROM codif_validation v
        JOIN codif_affectation a ON a.id_aff = v.id_aff
        JOIN codif_etudiant e ON e.id_etu = a.id_etu
        GROUP BY e.niveauFormation, e.sexe
    ) v ON v.niveauFormation = e.niveauFormation AND v.sexe = e.sexe

    /* Payés */
    LEFT JOIN (
        SELECT e.niveauFormation, e.sexe, COUNT(DISTINCT p.id_paie) AS nb_paye
        FROM codif_paiement p
        JOIN codif_validation v ON v.id_val = p.id_val
        JOIN codif_affectation a ON a.id_aff = v.id_aff
        JOIN codif_etudiant e ON e.id_etu = a.id_etu
        GROUP BY e.niveauFormation, e.sexe
    ) p ON p.niveauFormation = e.niveauFormation AND p.sexe = e.sexe

    /* Logés attributaires */
    LEFT JOIN (
        SELECT e.niveauFormation, e.sexe, COUNT(DISTINCT l.id_log) AS nb_loge_attr
        FROM codif_loger l
        JOIN codif_paiement p ON l.id_paie = p.id_paie
        JOIN codif_validation v ON v.id_val = p.id_val
        JOIN codif_affectation a ON a.id_aff = v.id_aff
        JOIN codif_etudiant e ON e.id_etu = a.id_etu
        GROUP BY e.niveauFormation, e.sexe
    ) la ON la.niveauFormation = e.niveauFormation AND la.sexe = e.sexe

    /* Logés suppléants */
    LEFT JOIN (
        SELECT e.niveauFormation, e.sexe, COUNT(DISTINCT l.id_log) AS nb_loge_supp
        FROM codif_loger l
        JOIN codif_validation v ON l.id_val = v.id_val
        JOIN codif_affectation a ON a.id_aff = v.id_aff
        JOIN codif_etudiant e ON e.id_etu = a.id_etu
        GROUP BY e.niveauFormation, e.sexe
    ) ls ON ls.niveauFormation = e.niveauFormation AND ls.sexe = e.sexe

    WHERE e.etablissement = ? AND e.sexe = ?
    GROUP BY e.niveauFormation, e.sexe
    ORDER BY e.niveauFormation ASC
    ';

    $stmt = $connexion->prepare($sql);
    $stmt->bind_param('ss', $fac, $sexe);
    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getEtudiantsModal($fac, $niveau, $sexe)
{
    global $connexion;

    $sql = "
    SELECT 
        e.id_etu,
        e.num_etu,
        e.nom,
        e.prenoms,
        e.niveauFormation,
        e.sexe,
        e.etablissement,
        lit.lit,
        MAX(a.id_aff) AS id_aff,
        MAX(v.id_val) AS id_val,
        MAX(p.id_paie) AS id_paie,
        MAX(l.id_log) AS id_log,

        /* ===== QUOTA ===== */
        rq.nombre AS quota,

        /* ===== RANG ===== */
        r.rang,

        /* ===== STATUT ===== */
        CASE 
            WHEN cf.id_etu IS NOT NULL THEN 'Forclos(e)'
            WHEN cl.statut IS NOT NULL THEN cl.statut
            WHEN rq.nombre IS NULL OR rq.nombre = 0 THEN 'Non Defini'
            WHEN r.rang <= rq.nombre THEN 'Attributaire'
            WHEN r.rang <= rq.nombre * 2 THEN 'Suppleant(e)'
            ELSE 'Non Attributaire'
        END AS statut

    FROM codif_etudiant e

    /* ===== QUOTA ===== */
    LEFT JOIN (
        SELECT 
            q.niveauFormation,
            l.sexe,
            COUNT(*) AS nombre
        FROM codif_quota q
        JOIN codif_lit l ON l.id_lit = q.id_lit_q
        GROUP BY q.niveauFormation, l.sexe
    ) rq ON rq.niveauFormation = e.niveauFormation AND rq.sexe = e.sexe

    /* ===== RANG UNIQUE (hors forclusion) ===== */
    LEFT JOIN (
        SELECT 
            id_etu,
            niveauFormation,
            sexe,
            ROW_NUMBER() OVER (
                PARTITION BY niveauFormation, sexe
                ORDER BY sessionId ASC, moyenne DESC, id_etu ASC, dateNaissance ASC
            ) AS rang
        FROM codif_etudiant
        WHERE NOT EXISTS (
            SELECT 1 FROM codif_forclusion f WHERE f.id_etu = codif_etudiant.id_etu
        )
    ) r ON e.id_etu = r.id_etu

    /* ===== FORCLUSION ===== */
    LEFT JOIN codif_forclusion cf ON cf.id_etu = e.id_etu

    /* ===== AFFECTATION, VALIDATION, PAIEMENT, LOGEMENT ===== */
    LEFT JOIN codif_affectation a ON a.id_etu = e.id_etu
    LEFT JOIN codif_lit lit ON lit.id_lit = a.id_lit
    LEFT JOIN codif_validation v ON v.id_aff = a.id_aff
    LEFT JOIN codif_paiement p ON p.id_val = v.id_val
    LEFT JOIN codif_loger l ON l.id_val = v.id_val OR l.id_paie = p.id_paie
    LEFT JOIN codif_loger cl ON e.id_etu = cl.id_etu

    /* ===== FILTRES ===== */
    WHERE e.etablissement = ?
      AND e.niveauFormation = ?
      AND e.sexe = ?

    GROUP BY e.id_etu, e.num_etu, e.nom, e.prenoms, e.niveauFormation, e.sexe, e.etablissement, rq.nombre, r.rang, cf.id_etu

    /* ===== TRI : forclo puis rang ===== */
    ORDER BY
        CASE WHEN cf.id_etu IS NOT NULL THEN 0 ELSE 1 END,
        r.rang ASC
    ";

    $stmt = $connexion->prepare($sql);
    $stmt->bind_param('sss', $fac, $niveau, $sexe);
    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getDeuxEtudiantsFull($link, $num_etu1, $num_etu2)
{
    $sql = '
        SELECT 
            e.id_etu,
            e.num_etu,
            e.nom,
            e.prenoms,
            e.sexe,
            e.telephone,
            e.niveauFormation,
            e.etablissement,

            cc.id_aff,
            cc.statut AS statut_affectation,

            l.id_lit,
            l.lit,

            q.niveauFormation AS niveau_quota

        FROM codif_etudiant e

        LEFT JOIN codif_affectation cc 
            ON cc.id_etu = e.id_etu

        LEFT JOIN codif_lit l 
            ON l.id_lit = cc.id_lit

        LEFT JOIN codif_quota q 
            ON q.id_lit_q = l.id_lit

        WHERE e.num_etu IN (?, ?)
    ';

    $stmt = $link->prepare($sql);
    $stmt->bind_param('ss', $num_etu1, $num_etu2);
    $stmt->execute();

    $result = $stmt->get_result();

    $data = [];

    while ($row = $result->fetch_assoc()) {
        $data[$row['num_etu']] = $row;
    }

    return $data;
}

function getStatistiquesToutesFacultes()
{
    global $connexion;

    $sql = "
    SELECT 
        e.etablissement,

        /* ================= AFFECTÉS ================= */
        COUNT(DISTINCT CASE WHEN e.sexe = 'M' THEN a.id_etu END) AS affecte_garcon,
        COUNT(DISTINCT CASE WHEN e.sexe = 'F' THEN a.id_etu END) AS affecte_fille,

        /* ================= VALIDÉS ================= */
        COUNT(DISTINCT CASE WHEN v.id_val IS NOT NULL AND e.sexe = 'M' THEN a.id_etu END) AS valide_garcon,
        COUNT(DISTINCT CASE WHEN v.id_val IS NOT NULL AND e.sexe = 'F' THEN a.id_etu END) AS valide_fille,

        /* ================= LOGÉS ================= */
        COUNT(DISTINCT CASE WHEN p.id_paie IS NOT NULL AND e.sexe = 'M' THEN a.id_etu END) AS loge_garcon,
        COUNT(DISTINCT CASE WHEN p.id_paie IS NOT NULL AND e.sexe = 'F' THEN a.id_etu END) AS loge_fille

    FROM codif_etudiant e

    LEFT JOIN codif_affectation a ON a.id_etu = e.id_etu
    LEFT JOIN codif_validation v ON v.id_aff = a.id_aff
    LEFT JOIN codif_paiement p ON p.id_val = v.id_val

    GROUP BY e.etablissement
    ORDER BY e.etablissement ASC
    ";

    $stmt = $connexion->prepare($sql);
    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getStatistiquesToutesFacultes_2()
{
    global $connexion;

    $sql = "
    SELECT 
        e.etablissement,

        /* STRUCTURE */
        COUNT(DISTINCT l.chambre) AS nb_chambres,
        COUNT(DISTINCT l.id_lit) AS nb_lits,

        /* AFFECTÉS */
        COUNT(DISTINCT CASE WHEN e.sexe = 'G' THEN a.id_etu END) AS affecte_garcon,
        COUNT(DISTINCT CASE WHEN e.sexe = 'F' THEN a.id_etu END) AS affecte_fille,

        /* VALIDÉS */
        COUNT(DISTINCT CASE WHEN v.id_val IS NOT NULL AND e.sexe = 'G' THEN a.id_etu END) AS valide_garcon,
        COUNT(DISTINCT CASE WHEN v.id_val IS NOT NULL AND e.sexe = 'F' THEN a.id_etu END) AS valide_fille,

        /* LOGÉS */
        COUNT(DISTINCT CASE WHEN p.id_paie IS NOT NULL AND e.sexe = 'G' THEN a.id_etu END) AS loge_garcon,
        COUNT(DISTINCT CASE WHEN p.id_paie IS NOT NULL AND e.sexe = 'F' THEN a.id_etu END) AS loge_fille

    FROM codif_etudiant e

    LEFT JOIN codif_affectation a ON a.id_etu = e.id_etu
    LEFT JOIN codif_lit l ON l.id_lit = a.id_lit
    LEFT JOIN codif_validation v ON v.id_aff = a.id_aff
    LEFT JOIN codif_paiement p ON p.id_val = v.id_val

    GROUP BY e.etablissement
    ORDER BY e.etablissement
    ";

    $stmt = $connexion->prepare($sql);
    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getStatsCampus()
{
    global $connexion;

    $sql = "
    SELECT 
    campus,

    COUNT(DISTINCT pavillon) AS nb_pavillons,
    COUNT(DISTINCT chambre) AS nb_chambres,
    COUNT(DISTINCT id_lit) AS nb_lits,

    /* Lits par sexe */
    COUNT(CASE WHEN sexe = 'G' THEN 1 END) AS lits_garcons,
    COUNT(CASE WHEN sexe = 'F' THEN 1 END) AS lits_filles

    FROM codif_lit

    GROUP BY campus
    ORDER BY campus
    ";

    $stmt = $connexion->prepare($sql);
    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function faculteExisteDansDelai($connexion, $faculte)
{
    $sql = 'SELECT COUNT(*) as total FROM codif_delai WHERE faculte = ?';
    $stmt = mysqli_prepare($connexion, $sql);
    mysqli_stmt_bind_param($stmt, 's', $faculte);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    return $row['total'] > 0;
}

function etudiantAPaye($connexion, $numEtudiant)
{
    $sql = '
        SELECT p.id_paie
        FROM codif_etudiant e
        JOIN codif_affectation a ON a.id_etu = e.id_etu
        JOIN codif_validation v ON v.id_aff = a.id_aff
        JOIN codif_paiement p ON p.id_val = v.id_val
        WHERE e.num_etu = ?
        LIMIT 1
    ';

    $stmt = mysqli_prepare($connexion, $sql);
    mysqli_stmt_bind_param($stmt, 's', $numEtudiant);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) > 0) {
        return true;  // paiement existe
    } else {
        return false;  // aucun paiement
    }
}

function sommePaiementArrieres($connexion, $numEtudiant)
{
    $sql = "
        SELECT COALESCE(SUM(p.montant), 0) AS total_arrieres
        FROM codif_etudiant e
        JOIN codif_affectation a ON a.id_etu = e.id_etu
        JOIN codif_validation v ON v.id_aff = a.id_aff
        JOIN codif_paiement p ON p.id_val = v.id_val
        WHERE e.num_etu = ?
        AND p.libelle = 'ARRIERES'
    ";

    $stmt = mysqli_prepare($connexion, $sql);
    mysqli_stmt_bind_param($stmt, 's', $numEtudiant);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    return $row ? (float) $row['total_arrieres'] : 0;
}

function codificationAnneeSuivanteExiste($connexion, $faculte, $annee)
{
    try {
        // Exemple : 2024_2025 => 2025_2026
        $annees = explode('_', $annee);
        $anneeSuivante = ($annees[0] + 1) . '_' . ($annees[1] + 1);

        // Nom de la base suivante
        $base = 'campuscoud';

        // Paramètres de connexion
        $serveur = 'localhost';
        $user = 'root';
        $pw = '';

        // Connexion
        $connexionCheck = mysqli_connect($serveur, $user, $pw);

        if (!$connexionCheck) {
            return false;
        }

        // Vérifier si la base existe
        $checkDb = mysqli_query(
            $connexionCheck,
            "SHOW DATABASES LIKE '" . mysqli_real_escape_string($connexionCheck, $base) . "'"
        );

        if (!$checkDb || mysqli_num_rows($checkDb) == 0) {
            mysqli_close($connexionCheck);
            return false;
        }

        // Sélection de la base
        if (!mysqli_select_db($connexionCheck, $base)) {
            mysqli_close($connexionCheck);
            return false;
        }

        // Vérification de la codification
        $sql = "
            SELECT id_delai
            FROM codif_delai
            WHERE LOWER(nature)='depart'
            AND faculte='" . mysqli_real_escape_string($connexionCheck, $faculte) . "'
            LIMIT 1
        ";

        $result = mysqli_query($connexionCheck, $sql);

        if (!$result) {
            mysqli_close($connexionCheck);
            return false;
        }

        $existe = mysqli_num_rows($result) > 0;

        mysqli_close($connexionCheck);

        return $existe;
    } catch (mysqli_sql_exception $e) {
        // Mot de passe incorrect, utilisateur incorrect,
        // serveur inaccessible, etc.
        return false;
    } catch (Exception $e) {
        return false;
    }
}

function EtuAnneeSuivanteExiste($connexion, $num_etu, $annee)
{
    try {
        // Exemple : 2024_2025 => 2025_2026
        $annees = explode('_', $annee);
        $anneeSuivante = ($annees[0] + 1) . '_' . ($annees[1] + 1);

        // Nom de la base suivante
        $base = 'campuscoud';

        // Paramètres de connexion
        $serveur = 'localhost';
        $user = 'root';
        $pw = '';

        // Connexion
        $connexionCheck = mysqli_connect($serveur, $user, $pw);

        if (!$connexionCheck) {
            return false;
        }

        // Vérifier si la base existe
        $checkDb = mysqli_query(
            $connexionCheck,
            "SHOW DATABASES LIKE '" . mysqli_real_escape_string($connexionCheck, $base) . "'"
        );

        if (!$checkDb || mysqli_num_rows($checkDb) == 0) {
            mysqli_close($connexionCheck);
            return false;
        }

        // Sélection de la base
        if (!mysqli_select_db($connexionCheck, $base)) {
            mysqli_close($connexionCheck);
            return false;
        }

        // Vérification de la codification
        $sql = "
            SELECT num_etu
            FROM blacklist
            WHERE num_etu='" . mysqli_real_escape_string($connexionCheck, $num_etu) . "'
            LIMIT 1
        ";

        $result = mysqli_query($connexionCheck, $sql);

        if (!$result) {
            mysqli_close($connexionCheck);
            return false;
        }

        $existe = mysqli_num_rows($result) > 0;

        mysqli_close($connexionCheck);

        return $existe;
    } catch (mysqli_sql_exception $e) {
        // Mot de passe incorrect, utilisateur incorrect,
        // serveur inaccessible, etc.
        return false;
    } catch (Exception $e) {
        return false;
    }
}

function getEtudiantsAttributairesNonAffectes($connexion, $faculte)
{
    $sql = '
        SELECT e.*
        FROM codif_etudiant e
        WHERE e.etablissement = ?
        AND NOT EXISTS (
            SELECT 1
            FROM codif_affectation a
            WHERE a.id_etu = e.id_etu
        )
        ORDER BY e.nom, e.prenoms
    ';

    $stmt = mysqli_prepare($connexion, $sql);

    if (!$stmt) {
        return [];
    }

    mysqli_stmt_bind_param($stmt, 's', $faculte);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    $etudiants = [];

    // Cache pour éviter de recalculer plusieurs fois
    $cacheStatuts = [];

    while ($etudiant = mysqli_fetch_assoc($result)) {
        $classe = $etudiant['niveauFormation'];
        $sexe = $etudiant['sexe'];

        $cle = $classe . '_' . $sexe;

        if (!isset($cacheStatuts[$cle])) {
            $quotaData = getQuotaClasse($classe, $sexe);

            $quota = isset($quotaData['COUNT(*)'])
                ? (int) $quotaData['COUNT(*)']
                : 0;

            $listeStatuts = getStatutStudentByQuota(
                $quota,
                $classe,
                $sexe
            );

            foreach ($listeStatuts as $student) {
                $cacheStatuts[$cle]['students'][$student['num_etu']] = $student;
            }

            $cacheStatuts[$cle]['quota'] = $quota;
        }

        if (
            isset(
                $cacheStatuts[$cle]['students'][$etudiant['num_etu']]
            )
        ) {
            $studentStatus =
                $cacheStatuts[$cle]['students'][$etudiant['num_etu']];

            if ($studentStatus['statut'] === 'Attributaire') {
                $etudiant['statut'] = 'Attributaire';
                $etudiant['rang'] = $studentStatus['rang'];
                $etudiant['quota'] = $cacheStatuts[$cle]['quota'];

                $etudiants[] = $etudiant;
            }
        }
    }

    mysqli_stmt_close($stmt);

    return $etudiants;
}

function getMontantArrierer($connexion, $numEtudiant)
{
    // Montant restant dans black_list
    $sqlBlackList = 'SELECT reste_a_payer FROM black_list WHERE num_etu = ?';
    $stmt = $connexion->prepare($sqlBlackList);
    $stmt->bind_param('s', $numEtudiant);
    $stmt->execute();
    $result = $stmt->get_result();

    $montantRestant = 0;

    if ($row = $result->fetch_assoc()) {
        $montantRestant = (float) $row['reste_a_payer'];
    }

    $stmt->close();

    // Somme des paiements effectués
    $sqlArrierer = '
        SELECT COALESCE(SUM(montant_paye), 0) AS total_paye
        FROM arrierer
        WHERE num_etu = ?
    ';

    $stmt = $connexion->prepare($sqlArrierer);
    $stmt->bind_param('s', $numEtudiant);
    $stmt->execute();
    $result = $stmt->get_result();

    $totalPaye = 0;

    if ($row = $result->fetch_assoc()) {
        $totalPaye = (float) $row['total_paye'];
    }

    $stmt->close();

    return $montantRestant + $totalPaye;
}

function getIsBlack_list($num_etu)
{
    global $connexion;
    $studentValidate = "
        SELECT 
        black_list.*,
        etu.*,
        lit.*,
        v.id_val AS valide,
        aff.id_aff AS affect

        FROM black_list
        JOIN codif_etudiant etu
        ON etu.num_etu COLLATE utf8mb4_unicode_ci = black_list.num_etu COLLATE utf8mb4_unicode_ci
        LEFT JOIN codif_affectation aff ON etu.id_etu = aff.id_etu
        LEFT JOIN codif_validation v ON v.id_aff = aff.id_aff
        LEFT JOIN codif_lit lit ON lit.id_lit = aff.id_lit
        WHERE black_list.num_etu COLLATE utf8mb4_unicode_ci =
            '$num_etu'
        ";
    $infoValite = mysqli_query($connexion, $studentValidate);
    $data = $infoValite->fetch_assoc();
    return $data;
}

function getNbrsReduction($num_etu)
{
    global $connexion;

    $sql = "SELECT nbr_mois 
            FROM codif_reduction 
            WHERE num_etu = ?";

    $stmt = $connexion->prepare($sql);
    $stmt->bind_param("s", $num_etu);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        return $row['nbr_mois'];
    }

    return 0;
}
?>