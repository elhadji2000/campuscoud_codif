<?php session_start();
if (empty($_SESSION['username']) && empty($_SESSION['mdp'])) {
    header('Location: /campuscoud.com/');
    exit();
}

include('../../traitement/fonction.php');




if (isset($_POST['numEtudiant'])) {
    $num_etu = $_POST['numEtudiant'];
    $etudiantVerifie = studentConnect($num_etu);
    $data = isEtudiantForclus_2($num_etu);
    if ($data == null) {
        $queryString = http_build_query(['data' => $etudiantVerifie]);
        header('Location: forclore.php?' . $queryString);
        exit();
    } else {
        $queryString = http_build_query(['data' => $data]);
        header('Location: forclore.php?statut=forclu&' . $queryString);
        exit();
    }
}


//echo "Hello";

if (isset($_POST['num_etu']) && isset($_POST['motif'])) {
    //try {
        $num_student = $_POST['num_etu'];
        $motif_for = $_POST['motif'];
		

    // Les informations de l'etudiant forclos       
    $info_studentsForclu = info($num_student);
    $info_studentsForclu_sexe = $info_studentsForclu[11];
    $info_studentsForclu_niv = $info_studentsForclu[7];
    $info_student_quota = getQuotaClasse($info_studentsForclu_niv, $info_studentsForclu_sexe)['COUNT(*)'];

    // Les informations de l'etudiant heritier (le non attributaire le mieux placé)
    $total_forclu = getAllForclu_manuel($info_studentsForclu_niv, $info_studentsForclu_sexe)->num_rows;
    $rang_studentHeritier = (($info_student_quota * 2) + 1);
    $info_heritier = getAllDatastudentStatus_2($info_student_quota, $info_studentsForclu_niv, $info_studentsForclu_sexe, $rang_studentHeritier);
    if($info_heritier){
        $num_etu_heritier=$info_heritier['num_etu'];
        
    }else{
        header('Location: forclore.php?erreurValider=FORCLUSION IMPOSSIBLE CAR CETTE CLASSE N\'A PAS DE Non Attributaire,  VEUILLEZ FAIRE UNE SUBSTITUTION MANUELLE !!!');
        exit();
    }

    // Les informations du suppleant (New Attributaire)

    $all_students = getStatutStudentByQuota($info_student_quota, $info_studentsForclu_niv, $info_studentsForclu_sexe);
    for ($i = 0; $i < count($all_students); $i++) {
        if ($all_students[$i]['num_etu'] == $num_student) {
            $rang = $all_students[$i]['rang'];
            // RECUPERATION DES INFORMATION DU SUPPLEANT
            $rang_suppleant = ($rang + $info_student_quota);
            $info_suppleant = getAllDatastudentStatus_2($info_student_quota, $info_studentsForclu_niv, $info_studentsForclu_sexe, $rang_suppleant);
            $id_suppleant = $info_suppleant['id_etu'];
            $num_suppleant = $info_suppleant['num_etu'];
            $naissance_suppleant = $info_suppleant['dateNaissance'];
            $moyenne_suppleant = $info_suppleant['moyenne'];
            $session_suppleant = $info_suppleant['sessionId'];
            /**ICI LA FONCTION SEND MESSAGES AU SUPPLEANT
             * ******************************************
            FIN **/
            // FIN DE RECUPERATION DES INFORMATIONS DU SUPPLEANT
        }
    }
    
    
    

    // Les informations de l'etudiant heritier (le non attributaire le mieux placé)
//$total_forclu = getAllForclu_manuel($info_studentsForclu_niv, $info_studentsForclu_sexe)->num_rows;                  	
//$num_studentHeritier = (($info_student_quota*2) + ($total_forclu + 1));
//$info_heritier = info4($num_studentHeritier);
//$info_heritier_id = $info_heritier[0];

		
        //Executer la Forclusion 
		
		$requete = addForcloreManuel($num_student, $motif_for, $_SESSION['username']);
		
	
        if ($requete == 1) {
			
//Envoi SMS au nouvel Attributaire
sms_nv_attributaire($num_suppleant); 

//Envoi SMS au nouveau Suppleant
sms_nv_suppleant($num_etu_heritier); 
			
            header('Location: forclore.php?successValider=Etudiant(e) forclos(e) avec success !!!');
        }
    //} catch (mysqli_sql_exception $e) {
       // header('Location: forclore.php?erreurValider=Forclusion impossible pour cet(te) etudiant(e) !!!');
	 //  print_r ($e->getMessage());
    //}
}

