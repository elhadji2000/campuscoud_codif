<?php session_start();

if (empty($_SESSION['username']) && empty($_SESSION['mdp'])) {
    header('Location: /campuscoud.com/');
    exit();
}

include('../../traitement/fonction.php');

$datesys0=date("Y-m-d");
$datesys = strtotime($datesys0);
$an0 = date('Y', $datesys);
$an = substr($an0,2,2);

if (isset($_POST['numEtudiant'])) {
    $num_etu = $_POST['numEtudiant'];
    $_SESSION['num_etu'] = $_POST['numEtudiant'];
    if (getIsForclu($num_etu)) {
        $queryString = http_build_query(['data' => getIsForclu($num_etu)]);
        header('Location: paiement.php?erreurForclo=ETUDIANT FORCLOS(E) !!!&statut=forclos(e)&' . $queryString);
    } else {
        if ($dataStudentConnect = studentConnect($num_etu)) {
            $dataStudentConnect_classe = $dataStudentConnect['niveauFormation'];
            $dataStudentConnect_sexe = $dataStudentConnect['sexe'];
            $dataStudentConnect_quota = getQuotaClasse($dataStudentConnect_classe, $dataStudentConnect_sexe)['COUNT(*)'];
            $dataStudentConnect_statut = getOnestudentStatus($dataStudentConnect_quota, $dataStudentConnect_classe, $dataStudentConnect_sexe, $num_etu);
            if ($dataStudentConnect_statut['statut'] == 'Attributaire') {
                $data = getOneByValidate($num_etu);
                if (mysqli_num_rows($data) > 0) {
                    while ($row = mysqli_fetch_array($data)) {
                        $array = $row;
                    }

                    $dernier_mois_paye = explode(" ", trim($array['libelle']));
                    $dernier_mois_paye = $dernier_mois_paye[count($dernier_mois_paye) - 1];
                    $dernier_mois_paye = getMois($dernier_mois_paye);
                    $date_sys = dateFromat(date("Y-m-d"));
                   // if (date("Y-m", strtotime($dernier_mois_paye)) == date("Y-m", strtotime($date_sys))) {
                       // $queryString = http_build_query(['data' => $array]);
                        //header('Location: paiement.php?erreurValider=ETUDIANT DEJA PAYER !!!&' . $queryString);
                       // exit();
                   // } else {
                        $queryString = http_build_query(['data' => $array]);
                        header("location: paiement.php?" . $queryString);
                        exit();
                   // }
                } else {
                    header("location: paiement.php?erreurNonTrouver=VOUS N'AVEZ PAS ENCORE VALIDER VOTRE LIT !!!");
                }
                mysqli_free_result($data);
            } else if ($dataStudentConnect_statut['statut'] == 'Suppleant(e)') {
                header("location: paiement.php?erreurNonTrouver=VOUS ETES SUPPLEANT, C'EST VOTRE TITULAIRE QUI DOIT PAYER LA CAUTION !!!");
            } else {
                header("location: paiement.php?erreurNonTrouver=VOUS N'ETES PAS ATTRIBUTAIRE DE LIT !!!");
            }
        } else {
            header("location: paiement.php?erreurNonTrouver=ETUDIANT INTROUVABLE: Veuillez vous approcher du Departement informatique du COUD");
        }
    }
}

if (isset($_POST['valide'])) {
    $i = 0;
    $libelle = "";
    try {
        //$id_etu = $_POST['id_etu'];
        $id_val = $_POST['valide'];
        $user = $_SESSION['username'];
        $montant_recu = $_POST['montant_recu'];
        $libelle = [];
        foreach ($_POST['libelle'] as $mois_caution => $value) {
            try {
                $libelle[$i] = $value;
                $i++;
            } catch (Exception $e) {
                header('Location: paiement.php?erreurValider=VEUILLER SELECTIONNER LES MOIS OU LA CAUTION !!!');
                exit();
            }
        }
        $chaine_libelle = json_encode($libelle);
        $chaine_libelle = str_replace(['[', ']', '"'], ' ', $chaine_libelle);
        $tableau_situation_paye = getAllSituation($_SESSION['num_etu']);
        $compt = 0;
        while ($situation = mysqli_fetch_array($tableau_situation_paye)) {
            $motsA = explode(' ', $chaine_libelle);
            $motsA = str_replace(' ', '', $motsA);
            foreach ($motsA as $mot) {
                if (strlen($mot) > 2) {
                    if (strpos($situation['libelle'], $mot) !== false) {
                        $compt++;
                        $queryString = http_build_query(['data' => $situation]);
                        header('Location: paiement.php?erreurMois=' . $mot.'&'.$queryString);
                        exit();
                    }
                }
            }
        }
        if ($compt == 0) {
$user=$_SESSION['username'];
$accronyme=accronyme($user); 		//echo $user;
$link = connexionBD();
$ins00 = "select max(num_ordre_user) as numauto from codif_paiement where an='$an0' and username_user='$user'"; //echo $ins00;
$exx00 = mysqli_query($link, $ins00); $n_rows0 = mysqli_fetch_assoc($exx00); 	
$ordre=$n_rows0['numauto']+1;  $quittance=$an."-".$accronyme."-".$ordre;		
			//echo $chaine_libelle." ".$quittance;die;
            $requete = setPaiement($id_val, $user,$montant_recu, $chaine_libelle,$quittance,$an0,$ordre);
            if ($requete == 1) {
				
/*$ins0 = "select max(id_paie) as numauto from codif_paiement where id_val='$id_val'";var_dump($ins0); 
$exx0 = mysqli_query($link, $ins0); $n_rows = mysqli_fetch_assoc($exx0); */
//$num_recu=$n_rows['numauto']; 

$telephone=getTelephoneEtudiant($_SESSION['num_etu']);

//Envoi
sms_paiement_etudiant($montant_recu,$_SESSION['num_etu'],$quittance) ;

//Stockage				
enreg_sms($_SESSION['num_etu'], $telephone, 'paiement_chambre');


                header('Location: paiement.php?successValider=PAIEMENT REUSSI: SMS ENVOYE au '.$telephone.' !');
            }
        }
    } catch (Exception $e) {
        header('Location: paiement.php?erreurValider=ERREUR !'); echo $e;
    }
}
