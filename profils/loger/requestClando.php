<?php
session_start();
if (empty($_SESSION['username']) && empty($_SESSION['mdp'])) {
    header('Location: /campuscoud.com/');
    exit();
}
include('../../traitement/fonction.php');
if (isset($_POST['numEtudiant'])) {
    $num_etu = $_POST['numEtudiant'];
    $_SESSION['num_etu'] = $num_etu;
    if ($is_forclu = getIsForclu($num_etu)) {
        $queryString = http_build_query(['data' => $is_forclu]);
        header('Location: clando.php?erreurForclo=Etudiant Forclos(e), donc ne peut pas clandoter !!!&statut=Forclos(e)&' . $queryString);
    } else {
        if ($dataStudentConnect = studentConnect($num_etu)) {
            $dataStudentConnect_classe = $dataStudentConnect['niveauFormation'];
            $dataStudentConnect_quota = getQuotaClasse($dataStudentConnect_classe, $dataStudentConnect['sexe'])['COUNT(*)'];
            $dataStudentConnect_statut = getOnestudentStatus($dataStudentConnect_quota, $dataStudentConnect_classe, $dataStudentConnect['sexe'], $num_etu);
            $dataStudentConnect_rang = $dataStudentConnect_statut['rang'];
            if ($dataStudentConnect_statut['statut'] == 'Attributaire') {
                $data = getOneByValidatePaiement($num_etu, $_SESSION['pavillon']);
                if (mysqli_num_rows($data) > 0) {
                    while ($row = mysqli_fetch_array($data)) {
                        $array = $row;
                    }
                    if (!isset($array[35])) {
                        $queryString = http_build_query(['data' => $array]);
                        header('Location: clando.php?erreurValider=VEUILLEZ PROCEDER AU PAIEMENT DABORD !!!&' . $queryString);
                    } else {
                        if ($array['etat_id_paie'] == 'Non migré') {
                            $queryString = http_build_query(['data' => $array]);
                            header("location: clando.php?erreurValider=ETUDIANT AYANT PAYE MAIS N'AYANT PAS ENCORE LOGE &" . $queryString);
                            exit();
                        } else {
                            $queryString = http_build_query(['data' => $array]);
                            header('Location: clando.php?erreurValider=ETUDIANT ATTRIBUTAIRE DANS CE PAVILLON, DONC PEUT CLANDOTER &' . $queryString);
                        }
                    }
                } else {
                    header("location: clando.php?erreurNonTrouver=ETUDIANT NON RESIDENT DU PAVILLON =>" . $_SESSION['pavillon'] . " !!!");
                }
                mysqli_free_result($data);
            } else {
                header('Location: clando.php?erreurNonTrouver=ETUDIANT NON ATTRIBUTAIRE, DONC NE PEUT PAS CLANDOTER !!!');
                exit();
            }
        } else {
            header('Location: clando.php?erreurNonTrouver=ETUDIANT INTROUVABLE DANS LA BASE DE DONNEES !!!');
        }
    }
}
if (isset($_POST['id_paie'])) {

    try {
		
		//Ne clandoter qu'un Etudiant de statut Non ATTRIBUTAIRE
		$num_etu=$_POST['num_etu']; //echo $num_etu;die;
		if ($dataStudentConnect = studentConnect($num_etu)) {
            $dataStudentConnect_classe = $dataStudentConnect['niveauFormation'];
            $dataStudentConnect_quota = getQuotaClasse($dataStudentConnect_classe, $dataStudentConnect['sexe'])['COUNT(*)'];
            $dataStudentConnect_statut = getOnestudentStatus($dataStudentConnect_quota, $dataStudentConnect_classe, $dataStudentConnect['sexe'], $num_etu);
			//echo $dataStudentConnect_statut['statut'] ; die;
			if($dataStudentConnect_statut['statut']!='Non Attributaire'){
			header('Location: clando.php?erreurValider=Attention: seuls les Non Attributaires peuvent etre clandotés !!!');
			exit();
			}
		}	    
		//Fin
		
		
		
        $id_etu = info($_POST['num_etu'])[15]; // echo $id_etu; exit ();
        $id_paie = $_POST['id_paie'];
        $user = $_SESSION['username'];
        $requete = setLogerClando($id_paie, $user, $id_etu);
        if ($requete == 1) {
            header('Location: clando.php?successValider=Hebergement Effectue avec success !!!');
        }
    } catch (mysqli_sql_exception $e) {
        header('Location: clando.php?erreurValider=Attention: Etudiant(e) Ayant Deja été logé(e) !!!');
    }
}
