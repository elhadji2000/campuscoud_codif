<?php session_start();
if (empty($_SESSION['username']) && empty($_SESSION['mdp'])) {
    header('Location: /campuscoud.com/');
    exit();
}

include('../../traitement/fonction.php');




if (isset($_POST['numEtudiant'])) {
    $num_etu = $_POST['numEtudiant'];
    $etudiantVerifie = studentConnect($num_etu);
    $data = isEtudiantForclus($etudiantVerifie['id_etu']);
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
$info_studentsForclu = info4($num_student);
$info_studentsForclu_sexe = $info_studentsForclu[13];
$info_studentsForclu_niv = $info_studentsForclu[9];
$info_student_quota = getQuotaClasse($info_studentsForclu_niv, $info_studentsForclu_sexe)['COUNT(*)'];

    // Les informations de l'etudiant heritier (le non attributaire le mieux placé)
$total_forclu = getAllForclu_manuel($info_studentsForclu_niv, $info_studentsForclu_sexe)->num_rows;                  	
$num_studentHeritier = (($info_student_quota*2) + ($total_forclu + 1));
$info_heritier = info4($num_studentHeritier);
$info_heritier_id = $info_heritier[0];

		
        //Executer la Forclusion 
		
		$requete = addForcloreManuel($num_student, $motif_for, $_SESSION['username']);
		
	
        if ($requete == 1) {
			
//Envoi SMS au nouvel Attributaire
//sms_heritier($info_heritier_id); 			
			
            header('Location: forclore.php?successValider=Etudiant(e) forclos(e) avec success !!!');
        }
    //} catch (mysqli_sql_exception $e) {
       // header('Location: forclore.php?erreurValider=Forclusion impossible pour cet(te) etudiant(e) !!!');
	 //  print_r ($e->getMessage());
    //}
}

