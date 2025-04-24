<?php
include('traitement/fonction.php');
$error = ""; 

 
        /**************************************************************************
     * Traitement de la forclusion automatique
     * **************************************************************** */
    $dataNiveauFormation = getAllNiveauFormationByQuota();
    $dataEtablissement = getDelai();
    $niveauFormation = [];
    $etablissement = [];
    $k = 0;
    $c = 0;
    while ($row = mysqli_fetch_array($dataNiveauFormation)) {
        $niveauFormation[$k] = $row;
        $k++;
    }
    while ($row = mysqli_fetch_array($dataEtablissement)) {
        $etablissement[$c] = $row;
        $c++;
    }
    if (count($niveauFormation) != 0) {
        for ($j = 0; $j < count($etablissement); $j++) {
            for ($i = 0; $i < count($niveauFormation); $i++) {
                // Combiner les résultats
                $quota_fille = getQuotaClasse($niveauFormation[$i][0], 'F')['COUNT(*)'];
                $quota_garcon = getQuotaClasse($niveauFormation[$i][0], 'G')['COUNT(*)'];

                // Combiner les résultats
                $tableau_data_etudiant_fille = getAllDatastudentStatus($quota_fille, $niveauFormation[$i][0], 'F');
                $tableau_data_etudiant_gargon = getAllDatastudentStatus($quota_garcon, $niveauFormation[$i][0], 'G');
                $tableau_data_etudiant[$i] = array_merge($tableau_data_etudiant_fille, $tableau_data_etudiant_gargon);
            }
        }
        $all_delai = getDelai();
        if ($all_delai->num_rows != 0) {
            for ($j = 0; $j < count($etablissement); $j++) {
                if ($all_delai1 = getAllDelai('depart', $etablissement[$j][0])) {
                    if ($all_delai2 = getAllDelai('choix', $etablissement[$j][0])) {
                        if ($all_delai3 = getAllDelai('validation', $etablissement[$j][0])) {
                            if ($all_delai4 = getAllDelai('paiement', $etablissement[$j][0])) {
                                $listeDelai1[$j] = getAllDelai('choix', $etablissement[$j][0]);
                                $listeDelai2[$j] = getAllDelai('validation', $etablissement[$j][0]);
                                $listeDelai3[$j] = getAllDelai('paiement', $etablissement[$j][0]);
                                $date_limite_choix[$j] = dateFromat($listeDelai1[$j]['data_limite']);
                                $date_limite_val[$j] = dateFromat($listeDelai2[$j]['data_limite']);
                                $date_limite_paye[$j] = dateFromat($listeDelai3[$j]['data_limite']);

                                $date_sys = dateFromat(date('Y-m-d'));
                                if ($date_sys > $date_limite_choix[$j]) {
                                    $nivFormationAndSexe = getNiveauFormationAndSexeLitByQuota();
                                    $tabNivSexe = [];
                                    $i = 0;
                                    while ($row = mysqli_fetch_array($nivFormationAndSexe)) {
                                        $tabNivSexe[$i] = $row;
                                        $i++;
                                    }
                                    for ($m = 0; $m < count($tabNivSexe); $m++) {
                                        if ($date_sys < $date_limite_val[$j]) {
                                            $compt = 0;
                                            $forclos_fille = getAllForclu($tabNivSexe[$m]['NiveauFormation'], "F");
                                            if ($forclos_fille->num_rows != 0) {
                                                while ($row = mysqli_fetch_array($forclos_fille)) {
                                                    if ($row['niveauFormation'] == $tabNivSexe[$m]['NiveauFormation']) {
                                                        if ($row['sexe'] == 'F') {
                                                            $dateFor = dateFromat($row['dateTime_for']);
                                                            if (($row['type'] == 'auto')) {
                                                                if ($dateFor >= $date_limite_choix[$j]) {
                                                                    if ($row['nature'] == 'choix') {
                                                                        $compt++;
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                                if ($compt == 0) {
                                                    for ($k = 0; $k < count($tableau_data_etudiant); $k++) {
                                                        for ($i = 0; $i < count($tableau_data_etudiant[$k]); $i++) {
                                                            if ($tableau_data_etudiant[$k][$i]['niveauFormation'] == $tabNivSexe[$m]['NiveauFormation']) {
                                                                if ($tableau_data_etudiant[$k][$i]['sexe'] == 'F') {
                                                                    if ($tableau_data_etudiant[$k][$i]['etablissement'] == $etablissement[$j][0]) {
                                                                        if ($tableau_data_etudiant[$k][$i]['statut'] == 'Attributaire') {
                                                                            $choix_lit = getChoixLitByStudent($tableau_data_etudiant[$k][$i]['num_etu']);
                                                                            if ($choix_lit == "Cliquer <a href='/profils/etudiants/codifier'>ICI</a> pour choisir un lit.") {


//Recuperation Infos pour Envoi SMS au nouvel Attributaire	
    // Les informations de l'etudiant forclos
	$info_studentsForclu = info4($tableau_data_etudiant[$k][$i]['id_etu']);
    $info_studentsForclu_sexe = $info_studentsForclu[13];
    $info_studentsForclu_niv = $info_studentsForclu[9];
    $info_student_quota = getQuotaClasse($info_studentsForclu_niv, $info_studentsForclu_sexe)['COUNT(*)'];
    // Les informations de l'etudiant heritier (le non attributaire le mieux placer)
    $total_forclu = getAllForclu_manuel($info_studentsForclu_niv, $info_studentsForclu_sexe)->num_rows;                  	
    $id_studentHeritier = (($info_student_quota*2) + ($total_forclu + 1));
    $info_heritier = info4($id_studentHeritier);
	$info_heritier_id = $info_heritier[0];																				
																			
																			//Forclusion
                                                                                addForclu($tableau_data_etudiant[$k][$i]['id_etu'], $listeDelai1[$j]['id_delai']);
																				
	//Envoi SMS au nouvel Attributaire
	sms_heritier($info_heritier_id); 
																				
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            } else {
                                                for ($k = 0; $k < count($tableau_data_etudiant); $k++) {
                                                    for ($i = 0; $i < count($tableau_data_etudiant[$k]); $i++) {
                                                        if ($tableau_data_etudiant[$k][$i]['niveauFormation'] == $tabNivSexe[$m]['NiveauFormation']) {
                                                            if ($tableau_data_etudiant[$k][$i]['sexe'] == 'F') {
                                                                if ($tableau_data_etudiant[$k][$i]['etablissement'] == $etablissement[$j][0]) {
                                                                    if ($tableau_data_etudiant[$k][$i]['statut'] == 'Attributaire') {
                                                                        $choix_lit = getChoixLitByStudent($tableau_data_etudiant[$k][$i]['num_etu']);
                                                                        if ($choix_lit == "Cliquer <a href='/profils/etudiants/codifier'>ICI</a> pour choisir un lit.") {

//Recuperation Infos pour Envoi SMS au nouvel Attributaire	
    // Les informations de l'etudiant forclos
	$info_studentsForclu = info4($tableau_data_etudiant[$k][$i]['id_etu']);
    $info_studentsForclu_sexe = $info_studentsForclu[13];
    $info_studentsForclu_niv = $info_studentsForclu[9];
    $info_student_quota = getQuotaClasse($info_studentsForclu_niv, $info_studentsForclu_sexe)['COUNT(*)'];
    // Les informations de l'etudiant heritier (le non attributaire le mieux placer)
    $total_forclu = getAllForclu_manuel($info_studentsForclu_niv, $info_studentsForclu_sexe)->num_rows;                  	
    $id_studentHeritier = (($info_student_quota*2) + ($total_forclu + 1));
    $info_heritier = info4($id_studentHeritier);
	$info_heritier_id = $info_heritier[0];																				
																			
																			//Forclusion
                                                                            addForclu($tableau_data_etudiant[$k][$i]['id_etu'], $listeDelai1[$j]['id_delai']);
																			
	//Envoi SMS au nouvel Attributaire
	sms_heritier($info_heritier_id); 
																			
																			
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                            $forclos_garcon = getAllForclu($tabNivSexe[$m]['NiveauFormation'], "G");
                                            if ($forclos_garcon->num_rows != 0) {
                                                while ($row = mysqli_fetch_array($forclos_garcon)) {
                                                    if ($row['niveauFormation'] == $tabNivSexe[$m]['NiveauFormation']) {
                                                        if ($row['sexe'] == 'G') {
                                                            $dateFor = dateFromat($row['dateTime_for']);
                                                            if (($row['type'] == 'auto')) {
                                                                if ($dateFor >= $date_limite_choix[$j]) {
                                                                    if ($row['nature'] == 'choix') {
                                                                        $compt++;
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                                if ($compt == 0) {
                                                    for ($k = 0; $k < count($tableau_data_etudiant); $k++) {
                                                        for ($i = 0; $i < count($tableau_data_etudiant[$k]); $i++) {
                                                            if ($tableau_data_etudiant[$k][$i]['niveauFormation'] == $tabNivSexe[$m]['NiveauFormation']) {
                                                                if ($tableau_data_etudiant[$k][$i]['sexe'] == 'G') {
                                                                    if ($tableau_data_etudiant[$k][$i]['etablissement'] == $etablissement[$j][0]) {
                                                                        if ($tableau_data_etudiant[$k][$i]['statut'] == 'Attributaire') {
                                                                            $choix_lit = getChoixLitByStudent($tableau_data_etudiant[$k][$i]['num_etu']);
                                                                            if ($choix_lit == "Cliquer <a href='/profils/etudiants/codifier'>ICI</a> pour choisir un lit.") {
																				
//Recuperation Infos pour Envoi SMS au nouvel Attributaire	
    // Les informations de l'etudiant forclos
	$info_studentsForclu = info4($tableau_data_etudiant[$k][$i]['id_etu']);
    $info_studentsForclu_sexe = $info_studentsForclu[13];
    $info_studentsForclu_niv = $info_studentsForclu[9];
    $info_student_quota = getQuotaClasse($info_studentsForclu_niv, $info_studentsForclu_sexe)['COUNT(*)'];
    // Les informations de l'etudiant heritier (le non attributaire le mieux placer)
    $total_forclu = getAllForclu_manuel($info_studentsForclu_niv, $info_studentsForclu_sexe)->num_rows;                  	
    $id_studentHeritier = (($info_student_quota*2) + ($total_forclu + 1));
    $info_heritier = info4($id_studentHeritier);
	$info_heritier_id = $info_heritier[0];																				
																			
																			//Forclusion
                                                                                addForclu($tableau_data_etudiant[$k][$i]['id_etu'], $listeDelai1[$j]['id_delai']);
																				
	//Envoi SMS au nouvel Attributaire
	sms_heritier($info_heritier_id);																				
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            } else {
                                                for ($k = 0; $k < count($tableau_data_etudiant); $k++) {
                                                    for ($i = 0; $i < count($tableau_data_etudiant[$k]); $i++) {
                                                        if ($tableau_data_etudiant[$k][$i]['niveauFormation'] == $tabNivSexe[$m]['NiveauFormation']) {
                                                            if ($tableau_data_etudiant[$k][$i]['sexe'] == 'G') {
                                                                if ($tableau_data_etudiant[$k][$i]['etablissement'] == $etablissement[$j][0]) {
                                                                    if ($tableau_data_etudiant[$k][$i]['statut'] == 'Attributaire') {
                                                                        $choix_lit = getChoixLitByStudent($tableau_data_etudiant[$k][$i]['num_etu']);
                                                                        if ($choix_lit == "Cliquer <a href='/profils/etudiants/codifier'>ICI</a> pour choisir un lit.") {
																			
//Recuperation Infos pour Envoi SMS au nouvel Attributaire	
    // Les informations de l'etudiant forclos
	$info_studentsForclu = info4($tableau_data_etudiant[$k][$i]['id_etu']);
    $info_studentsForclu_sexe = $info_studentsForclu[13];
    $info_studentsForclu_niv = $info_studentsForclu[9];
    $info_student_quota = getQuotaClasse($info_studentsForclu_niv, $info_studentsForclu_sexe)['COUNT(*)'];
    // Les informations de l'etudiant heritier (le non attributaire le mieux placer)
    $total_forclu = getAllForclu_manuel($info_studentsForclu_niv, $info_studentsForclu_sexe)->num_rows;                  	
    $id_studentHeritier = (($info_student_quota*2) + ($total_forclu + 1));
    $info_heritier = info4($id_studentHeritier);
	$info_heritier_id = $info_heritier[0];																				
																			
																			//Forclusion
                                                                            addForclu($tableau_data_etudiant[$k][$i]['id_etu'], $listeDelai1[$j]['id_delai']);
																			
	//Envoi SMS au nouvel Attributaire
	sms_heritier($info_heritier_id);																			
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    if ($date_sys > $date_limite_val[$j]) {
                                        $nivFormationAndSexe = getNiveauFormationAndSexeLitByQuota();
                                        $tabNivSexe = [];
                                        $i = 0;
                                        while ($row = mysqli_fetch_array($nivFormationAndSexe)) {
                                            $tabNivSexe[$i] = $row;
                                            $i++;
                                        }
                                        for ($m = 0; $m < count($tabNivSexe); $m++) {
                                            if ($date_sys < $date_limite_paye[$j]) {
                                                $forclos_validation_fille = getAllForclu($tabNivSexe[$m]['NiveauFormation'], 'F');
                                                if ($forclos_validation_fille->num_rows != 0) {
                                                    $compt = 0;
                                                    while ($row = mysqli_fetch_array($forclos_validation_fille)) {
                                                        $dateFor = dateFromat($row['dateTime_for']);
                                                        if ($row['sexe'] == 'F') {
                                                            if (($row['type'] == 'auto')) {
                                                                if ($dateFor >= $date_limite_choix[$j]) {
                                                                    if ($row['nature'] == 'validation') {
                                                                        $compt++;
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                    if ($compt == 0) {
                                                        for ($k = 0; $k < count($tableau_data_etudiant); $k++) {
                                                            for ($i = 0; $i < count($tableau_data_etudiant[$k]); $i++) {
                                                                if ($tableau_data_etudiant[$k][$i]['niveauFormation'] == $tabNivSexe[$m]['NiveauFormation']) {
                                                                    if ($tableau_data_etudiant[$k][$i]['sexe'] == 'F') {
                                                                        if ($tableau_data_etudiant[$k][$i]['etablissement'] == $etablissement[$j][0]) {
                                                                            if ($tableau_data_etudiant[$k][$i]['statut'] == 'Attributaire') {
                                                                                $choix_lit = getValidateLitByStudent2($tableau_data_etudiant[$k][$i]['num_etu']);
                                                                                if ($choix_lit == "Presentez-vous au service Hebergement pour valider votre codification!") {
																					
					//Recuperation Infos pour Envoi SMS au nouvel Attributaire	
    // Les informations de l'etudiant forclos
	$info_studentsForclu = info4($tableau_data_etudiant[$k][$i]['id_etu']);
    $info_studentsForclu_sexe = $info_studentsForclu[13];
    $info_studentsForclu_niv = $info_studentsForclu[9];
    $info_student_quota = getQuotaClasse($info_studentsForclu_niv, $info_studentsForclu_sexe)['COUNT(*)'];
    // Les informations de l'etudiant heritier (le non attributaire le mieux placer)
    $total_forclu = getAllForclu_manuel($info_studentsForclu_niv, $info_studentsForclu_sexe)->num_rows;                  	
    $id_studentHeritier = (($info_student_quota*2) + ($total_forclu + 1));
    $info_heritier = info4($id_studentHeritier);
	$info_heritier_id = $info_heritier[0];																				
																			
																			//Forclusion																
                                                                                    addForclu($tableau_data_etudiant[$k][$i]['id_etu'], $listeDelai2[$j]['id_delai']);
																					
		//Envoi SMS au nouvel Attributaire
	sms_heritier($info_heritier_id);																				
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                } else {
                                                    for ($k = 0; $k < count($tableau_data_etudiant); $k++) {
                                                        for ($i = 0; $i < count($tableau_data_etudiant[$k]); $i++) {
                                                            if ($tableau_data_etudiant[$k][$i]['niveauFormation'] == $tabNivSexe[$m]['NiveauFormation']) {
                                                                if ($tableau_data_etudiant[$k][$i]['sexe'] == 'F') {
                                                                    if ($tableau_data_etudiant[$k][$i]['etablissement'] == $etablissement[$j][0]) {
                                                                        if ($tableau_data_etudiant[$k][$i]['statut'] == 'Attributaire') {
                                                                            $choix_lit = getValidateLitByStudent2($tableau_data_etudiant[$k][$i]['num_etu']);
                                                                            if ($choix_lit == "Presentez-vous au service Hebergement pour valider votre codification!") {
																				
//Recuperation Infos pour Envoi SMS au nouvel Attributaire	
    // Les informations de l'etudiant forclos
	$info_studentsForclu = info4($tableau_data_etudiant[$k][$i]['id_etu']);
    $info_studentsForclu_sexe = $info_studentsForclu[13];
    $info_studentsForclu_niv = $info_studentsForclu[9];
    $info_student_quota = getQuotaClasse($info_studentsForclu_niv, $info_studentsForclu_sexe)['COUNT(*)'];
    // Les informations de l'etudiant heritier (le non attributaire le mieux placer)
    $total_forclu = getAllForclu_manuel($info_studentsForclu_niv, $info_studentsForclu_sexe)->num_rows;                  	
    $id_studentHeritier = (($info_student_quota*2) + ($total_forclu + 1));
    $info_heritier = info4($id_studentHeritier);
	$info_heritier_id = $info_heritier[0];																				
																			
																			//Forclusion																				
                                                                                addForclu($tableau_data_etudiant[$k][$i]['id_etu'], $listeDelai2[$j]['id_delai']);
																				
	//Envoi SMS au nouvel Attributaire
	sms_heritier($info_heritier_id);																				
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                                $forclos_validation_garcon = getAllForclu($tabNivSexe[$m]['NiveauFormation'], 'G');
                                                if ($forclos_validation_garcon->num_rows != 0) {
                                                    $compt = 0;
                                                    while ($row = mysqli_fetch_array($forclos_validation_garcon)) {
                                                        $dateFor = dateFromat($row['dateTime_for']);
                                                        if ($row['sexe'] == 'G') {
                                                            if (($row['type'] == 'auto')) {
                                                                if ($dateFor >= $date_limite_choix[$j]) {
                                                                    if ($row['nature'] == 'validation') {
                                                                        $compt++;
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                    if ($compt == 0) {
                                                        for ($k = 0; $k < count($tableau_data_etudiant); $k++) {
                                                            for ($i = 0; $i < count($tableau_data_etudiant[$k]); $i++) {
                                                                if ($tableau_data_etudiant[$k][$i]['niveauFormation'] == $tabNivSexe[$m]['NiveauFormation']) {
                                                                    if ($tableau_data_etudiant[$k][$i]['sexe'] == 'G') {
                                                                        if ($tableau_data_etudiant[$k][$i]['etablissement'] == $etablissement[$j][0]) {
                                                                            if ($tableau_data_etudiant[$k][$i]['statut'] == 'Attributaire') {
                                                                                $choix_lit = getValidateLitByStudent2($tableau_data_etudiant[$k][$i]['num_etu']);
                                                                                if ($choix_lit == "Presentez-vous au service Hebergement pour valider votre codification!") {
																					
//Recuperation Infos pour Envoi SMS au nouvel Attributaire	
    // Les informations de l'etudiant forclos
	$info_studentsForclu = info4($tableau_data_etudiant[$k][$i]['id_etu']);
    $info_studentsForclu_sexe = $info_studentsForclu[13];
    $info_studentsForclu_niv = $info_studentsForclu[9];
    $info_student_quota = getQuotaClasse($info_studentsForclu_niv, $info_studentsForclu_sexe)['COUNT(*)'];
    // Les informations de l'etudiant heritier (le non attributaire le mieux placer)
    $total_forclu = getAllForclu_manuel($info_studentsForclu_niv, $info_studentsForclu_sexe)->num_rows;                  	
    $id_studentHeritier = (($info_student_quota*2) + ($total_forclu + 1));
    $info_heritier = info4($id_studentHeritier);
	$info_heritier_id = $info_heritier[0];																				
																			
																			//Forclusion																					
                                                                                    addForclu($tableau_data_etudiant[$k][$i]['id_etu'], $listeDelai2[$j]['id_delai']);
																					
	//Envoi SMS au nouvel Attributaire
	sms_heritier($info_heritier_id);																					
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                } else {
                                                    for ($k = 0; $k < count($tableau_data_etudiant); $k++) {
                                                        for ($i = 0; $i < count($tableau_data_etudiant[$k]); $i++) {
                                                            if ($tableau_data_etudiant[$k][$i]['niveauFormation'] == $tabNivSexe[$m]['NiveauFormation']) {
                                                                if ($tableau_data_etudiant[$k][$i]['sexe'] == 'G') {
                                                                    if ($tableau_data_etudiant[$k][$i]['etablissement'] == $etablissement[$j][0]) {
                                                                        if ($tableau_data_etudiant[$k][$i]['statut'] == 'Attributaire') {
                                                                            $choix_lit = getValidateLitByStudent2($tableau_data_etudiant[$k][$i]['num_etu']);
                                                                            if ($choix_lit == "Presentez-vous au service Hebergement pour valider votre codification!") {
																				
//Recuperation Infos pour Envoi SMS au nouvel Attributaire	
    // Les informations de l'etudiant forclos
	$info_studentsForclu = info4($tableau_data_etudiant[$k][$i]['id_etu']);
    $info_studentsForclu_sexe = $info_studentsForclu[13];
    $info_studentsForclu_niv = $info_studentsForclu[9];
    $info_student_quota = getQuotaClasse($info_studentsForclu_niv, $info_studentsForclu_sexe)['COUNT(*)'];
    // Les informations de l'etudiant heritier (le non attributaire le mieux placer)
    $total_forclu = getAllForclu_manuel($info_studentsForclu_niv, $info_studentsForclu_sexe)->num_rows;                  	
    $id_studentHeritier = (($info_student_quota*2) + ($total_forclu + 1));
    $info_heritier = info4($id_studentHeritier);
	$info_heritier_id = $info_heritier[0];																				
																			
																			//Forclusion																				
                                                                                addForclu($tableau_data_etudiant[$k][$i]['id_etu'], $listeDelai2[$j]['id_delai']);
																				
	//Envoi SMS au nouvel Attributaire
	sms_heritier($info_heritier_id);																				
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        if ($date_sys > $date_limite_paye[$j]) {
                                            $nivFormationAndSexe = getNiveauFormationAndSexeLitByQuota();
                                            $tabNivSexe = [];
                                            $i = 0;
                                            while ($row = mysqli_fetch_array($nivFormationAndSexe)) {
                                                $tabNivSexe[$i] = $row;
                                                $i++;
                                            }
                                            for ($m = 0; $m < count($tabNivSexe); $m++) {
                                                // for ($k = 0; $k < count($tableau_data_etudiant); $k++) {
                                                $forclos_paiement_fille = getAllForclu($tabNivSexe[$m]['NiveauFormation'], 'F');
                                                if ($forclos_paiement_fille->num_rows != 0) {
                                                    $compt = 0;
                                                    while ($row = mysqli_fetch_array($forclos_paiement_fille)) {
                                                        $dateFor_paiement = dateFromat($row['dateTime_for']);
                                                        if ($row['sexe'] == 'F') {
                                                            if (($row['type'] == 'auto')) {
                                                                if ($dateFor_paiement >= $date_limite_paye[$j]) {
                                                                    if ($row['nature'] == 'paiement') {
                                                                        $compt++;
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                    if ($compt == 0) {
                                                        for ($k = 0; $k < count($tableau_data_etudiant); $k++) {
                                                            for ($i = 0; $i < count($tableau_data_etudiant[$k]); $i++) {
                                                                if ($tableau_data_etudiant[$k][$i]['niveauFormation'] == $tabNivSexe[$m]['NiveauFormation']) {
                                                                    if ($tableau_data_etudiant[$k][$i]['sexe'] == 'F') {
                                                                        if ($tableau_data_etudiant[$k][$i]['etablissement'] == $etablissement[$j][0]) {
                                                                            if ($tableau_data_etudiant[$k][$i]['statut'] == 'Attributaire') {
                                                                                $choix_lit = getValidatePaiementLitBySuppleant($tableau_data_etudiant[$k][$i]['num_etu']);
                                                                                if (!$choix_lit) {
																					
//Recuperation Infos pour Envoi SMS au nouvel Attributaire	
    // Les informations de l'etudiant forclos
	$info_studentsForclu = info4($tableau_data_etudiant[$k][$i]['id_etu']);
    $info_studentsForclu_sexe = $info_studentsForclu[13];
    $info_studentsForclu_niv = $info_studentsForclu[9];
    $info_student_quota = getQuotaClasse($info_studentsForclu_niv, $info_studentsForclu_sexe)['COUNT(*)'];
    // Les informations de l'etudiant heritier (le non attributaire le mieux placer)
    $total_forclu = getAllForclu_manuel($info_studentsForclu_niv, $info_studentsForclu_sexe)->num_rows;                  	
    $id_studentHeritier = (($info_student_quota*2) + ($total_forclu + 1));
    $info_heritier = info4($id_studentHeritier);
	$info_heritier_id = $info_heritier[0];																				
																			
																			//Forclusion																					
                                                                                    addForclu($tableau_data_etudiant[$k][$i]['id_etu'], $listeDelai3[$j]['id_delai']);
																					
		//Envoi SMS au nouvel Attributaire
	sms_heritier($info_heritier_id);																				
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                } else {
                                                    for ($k = 0; $k < count($tableau_data_etudiant); $k++) {
                                                        for ($i = 0; $i < count($tableau_data_etudiant[$k]); $i++) {
                                                            if ($tableau_data_etudiant[$k][$i]['niveauFormation'] == $tabNivSexe[$m]['NiveauFormation']) {
                                                                if ($tableau_data_etudiant[$k][$i]['sexe'] == 'F') {
                                                                    if ($tableau_data_etudiant[$k][$i]['etablissement'] == $etablissement[$j][0]) {
                                                                        if ($tableau_data_etudiant[$k][$i]['statut'] == 'Attributaire') {
                                                                            $choix_lit = getValidatePaiementLitBySuppleant($tableau_data_etudiant[$k][$i]['num_etu']);
                                                                            if (!$choix_lit) {
																				
//Recuperation Infos pour Envoi SMS au nouvel Attributaire	
    // Les informations de l'etudiant forclos
	$info_studentsForclu = info4($tableau_data_etudiant[$k][$i]['id_etu']);
    $info_studentsForclu_sexe = $info_studentsForclu[13];
    $info_studentsForclu_niv = $info_studentsForclu[9];
    $info_student_quota = getQuotaClasse($info_studentsForclu_niv, $info_studentsForclu_sexe)['COUNT(*)'];
    // Les informations de l'etudiant heritier (le non attributaire le mieux placer)
    $total_forclu = getAllForclu_manuel($info_studentsForclu_niv, $info_studentsForclu_sexe)->num_rows;                  	
    $id_studentHeritier = (($info_student_quota*2) + ($total_forclu + 1));
    $info_heritier = info4($id_studentHeritier);
	$info_heritier_id = $info_heritier[0];																				
																			
																			//Forclusion																				
                                                                                addForclu($tableau_data_etudiant[$k][$i]['id_etu'], $listeDelai3[$j]['id_delai']);
																				
	//Envoi SMS au nouvel Attributaire
	sms_heritier($info_heritier_id);																				
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                                $forclos_paiement_garcon = getAllForclu($tabNivSexe[$m]['NiveauFormation'], 'G');
                                                if ($forclos_paiement_garcon->num_rows != 0) {
                                                    $compt = 0;
                                                    while ($row = mysqli_fetch_array($forclos_paiement_garcon)) {
                                                        $dateFor_paiement = dateFromat($row['dateTime_for']);
                                                        if ($row['sexe'] == 'G') {
                                                            if (($row['type'] == 'auto')) {
                                                                if ($dateFor_paiement >= $date_limite_paye[$j]) {
                                                                    if ($row['nature'] == 'paiement') {
                                                                        $compt++;
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                    if ($compt == 0) {
                                                        for ($k = 0; $k < count($tableau_data_etudiant); $k++) {
                                                            for ($i = 0; $i < count($tableau_data_etudiant[$k]); $i++) {
                                                                if ($tableau_data_etudiant[$k][$i]['niveauFormation'] == $tabNivSexe[$m]['NiveauFormation']) {
                                                                    if ($tableau_data_etudiant[$k][$i]['sexe'] == 'G') {
                                                                        if ($tableau_data_etudiant[$k][$i]['etablissement'] == $etablissement[$j][0]) {
                                                                            if ($tableau_data_etudiant[$k][$i]['statut'] == 'Attributaire') {
                                                                                $choix_lit = getValidatePaiementLitBySuppleant($tableau_data_etudiant[$k][$i]['num_etu']);
                                                                                if (!$choix_lit) {
																					
//Recuperation Infos pour Envoi SMS au nouvel Attributaire	
    // Les informations de l'etudiant forclos
	$info_studentsForclu = info4($tableau_data_etudiant[$k][$i]['id_etu']);
    $info_studentsForclu_sexe = $info_studentsForclu[13];
    $info_studentsForclu_niv = $info_studentsForclu[9];
    $info_student_quota = getQuotaClasse($info_studentsForclu_niv, $info_studentsForclu_sexe)['COUNT(*)'];
    // Les informations de l'etudiant heritier (le non attributaire le mieux placer)
    $total_forclu = getAllForclu_manuel($info_studentsForclu_niv, $info_studentsForclu_sexe)->num_rows;                  	
    $id_studentHeritier = (($info_student_quota*2) + ($total_forclu + 1));
    $info_heritier = info4($id_studentHeritier);
	$info_heritier_id = $info_heritier[0];																				
																			
																			//Forclusion																					
                                                                                    addForclu($tableau_data_etudiant[$k][$i]['id_etu'], $listeDelai3[$j]['id_delai']);
																					
	//Envoi SMS au nouvel Attributaire
	sms_heritier($info_heritier_id);																					
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                } else {
                                                    for ($k = 0; $k < count($tableau_data_etudiant); $k++) {
                                                        for ($i = 0; $i < count($tableau_data_etudiant[$k]); $i++) {
                                                            if ($tableau_data_etudiant[$k][$i]['niveauFormation'] == $tabNivSexe[$m]['NiveauFormation']) {
                                                                if ($tableau_data_etudiant[$k][$i]['sexe'] == 'G') {
                                                                    if ($tableau_data_etudiant[$k][$i]['etablissement'] == $etablissement[$j][0]) {
                                                                        if ($tableau_data_etudiant[$k][$i]['statut'] == 'Attributaire') {
                                                                            $choix_lit = getValidatePaiementLitBySuppleant($tableau_data_etudiant[$k][$i]['num_etu']);
                                                                            if (!$choix_lit) {
																				
//Recuperation Infos pour Envoi SMS au nouvel Attributaire	
    // Les informations de l'etudiant forclos
	$info_studentsForclu = info4($tableau_data_etudiant[$k][$i]['id_etu']);
    $info_studentsForclu_sexe = $info_studentsForclu[13];
    $info_studentsForclu_niv = $info_studentsForclu[9];
    $info_student_quota = getQuotaClasse($info_studentsForclu_niv, $info_studentsForclu_sexe)['COUNT(*)'];
    // Les informations de l'etudiant heritier (le non attributaire le mieux placer)
    $total_forclu = getAllForclu_manuel($info_studentsForclu_niv, $info_studentsForclu_sexe)->num_rows;                  	
    $id_studentHeritier = (($info_student_quota*2) + ($total_forclu + 1));
    $info_heritier = info4($id_studentHeritier);
	$info_heritier_id = $info_heritier[0];																				
																			
																			//Forclusion																				
                                                                                addForclu($tableau_data_etudiant[$k][$i]['id_etu'], $listeDelai3[$j]['id_delai']);
																				
	//Envoi SMS au nouvel Attributaire
	sms_heritier($info_heritier_id);																				
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
	 ?>