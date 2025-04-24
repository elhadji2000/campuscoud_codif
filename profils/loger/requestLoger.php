<?php session_start();
if (empty($_SESSION['username']) && empty($_SESSION['mdp'])) {
    header('Location: /campuscoud.com/');
    exit();
}
include('../../traitement/fonction.php');
if (isset($_POST['numEtudiant'])) {
    $num_etu = $_POST['numEtudiant'];
    if ($is_forclu = getIsForclu($num_etu)) {
        $queryString = http_build_query(['data' => $is_forclu]);
        header('Location: loger.php?erreurForclo=Cet etudiant est forclos(e) !!!&statut=Forclos(e)&' . $queryString);
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
                    if ($array['etat_id_paie'] == 'Non migré') {
                        $queryString = http_build_query(['data' => $array]);
                        header("location: loger.php?" . $queryString);
                        exit();
                    } else {
                        $queryString = http_build_query(['data' => $array]);
                        header('Location: loger.php?erreurValider=Etudiant déja logé !!!&' . $queryString);
                    }
                } else {
                    header("location: loger.php?erreurNonTrouver=Cet etudiant n'est pas resident du pavillon =>" . $_SESSION['pavillon'] . " !!!");
                }
                mysqli_free_result($data);
            } 
            else if ($dataStudentConnect_statut['statut'] == 'Suppleant(e)') {
                $monTitulaire = getOneTitulaireBySuppleant($dataStudentConnect_quota, $dataStudentConnect_classe, $dataStudentConnect['sexe'], $dataStudentConnect_rang);
                $monTitulaire_numEtu = $monTitulaire['num_etu'];
                if (getValidateLogerByTitulaire($monTitulaire_numEtu)) {
                    if (getValidateLitBySuppleant($num_etu)) {
                        if (getLogerSuppleant($num_etu, $_SESSION['pavillon'])) {
                            if (getValidateLogerBySuppleant($num_etu)) {
                                $queryString = http_build_query(['data' => getValidateLogerBySuppleant($num_etu)]);
                                header('Location: loger.php?statut=' . $dataStudentConnect_statut['statut'] . '&erreurValider=Suppleant déja logé !!!&' . $queryString);
                                exit();
                            } else {
                                $arrayValidateSuppleant = getValidateLitBySuppleant($num_etu);
                                if ($arrayValidateSuppleant['etat_id_val'] == 'Migré') {
                                    $queryString = http_build_query(['data' => $arrayValidateSuppleant]);
                                    header('Location: loger.php?statut=' . $dataStudentConnect_statut['statut'] . '&erreurValider=Suppleant déja logé !!!&' . $queryString);
                                    exit();
                                } else if ($arrayValidateSuppleant['etat_id_val'] == 'Non migré') {
                                    $queryString = http_build_query(['data' => $arrayValidateSuppleant]);
                                    header("location: loger.php?statut=" . $dataStudentConnect_statut['statut'] . '&' . $queryString);
                                    exit();
                                }
                            }
                        } else {
                            header('Location: loger.php?erreurValider=Suppleant, vous netes pas un habitant du pavillon => ' . $_SESSION['pavillon']);
                            exit();
                        }
                    } else {
                        header('Location: loger.php?erreurValider=Suppleant, vous n\'avez pas encore validé votre codification !!!');
                        exit();
                    }
                } else {
                    header('Location: loger.php?erreurValider=Suppleant, votre titulaire n\'a pas encore logé !!!');
                    exit();
                }
            }
			
			else if ($dataStudentConnect_statut['statut'] == 'Non Attributaire') {
				header('Location: loger.php?erreurValider= Etudiant Non Attributaire !!!');
                    exit();
			}
			// Ajouté le 07/03 à 17h pour eviter affichage page vide
			else { 
            header('Location: loger.php?erreurNonTrouver=Logement impossible pour le moment !!!');
        }
			//
			
        } else { 
            header('Location: loger.php?erreurNonTrouver=Etudiant non trouvé !!!');
        }
    }
}
//var_dump($_POST['valide']);
if (isset($_POST['valide'])) {
    try {

		$id_etu = $_POST['id_etu']; 
		
        $id_aff = $_POST['valide'];
        $user = $_SESSION['username']; //echo "idaff: ".$id_aff." user: ".$user."/".$id_etu; exit();
        $requete = setLoger($id_aff, $user, $id_etu);
        if ($requete == 1) {
            header('Location: loger.php?successValider=Logement Titulaire Effectué avec success !!!');
        }
    } catch (mysqli_sql_exception $e) {
        header('Location: loger.php?erreurValider=Attention: Attributaire déja logé !!!');
    }
}
if (isset($_POST['id_val'])) {
    try {
		
		$id_etu = $_POST['id_etu'];
		
        $id_val = $_POST['id_val'];
        $user = $_SESSION['username'];
        $requete = setLogerSuppleant($id_val, $user, $id_etu);
        if ($requete == 1) {
            header('Location: loger.php?successValider=Logement Suppleant Effectué avec success !!!');
        }
    } catch (mysqli_sql_exception $e) {
        header('Location: loger.php?erreurValider=Attention: Suppleant déja logé !!!');
    }
}
