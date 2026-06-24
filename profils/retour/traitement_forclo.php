<?php

session_start();
include('../../traitement/fonction.php');
$fac = $_POST["fac"];
if (isset($_POST['etudiants']) && !empty($_POST['etudiants'])) {

    foreach ($_POST['etudiants'] as $num_etu) {
        $num_student = $num_etu;
        $motif_for = 'Forclusion auto test!';

        // ==========================
        // Informations étudiant forclos
        // ==========================
        $info_studentsForclu = info($num_student);

        if (!$info_studentsForclu) {
            continue;
        }

        $info_studentsForclu_sexe = $info_studentsForclu[11];
        $info_studentsForclu_niv  = $info_studentsForclu[7];

        $quotaData = getQuotaClasse(
            $info_studentsForclu_niv,
            $info_studentsForclu_sexe
        );

        $info_student_quota = $quotaData['COUNT(*)'];

        // ==========================
        // Recherche héritier
        // ==========================
        $rang_studentHeritier = (($info_student_quota * 2) + 1);

        $info_heritier = getAllDatastudentStatus_2(
            $info_student_quota,
            $info_studentsForclu_niv,
            $info_studentsForclu_sexe,
            $rang_studentHeritier
        );
//FORCLUSION IMPOSSIBLE CAR CETTE CLASSE N\'A PAS DE Non Attributaire
        if (!$info_heritier) {
            continue;
        }

        $num_etu_heritier = $info_heritier['num_etu'];

        // ==========================
        // Recherche suppléant
        // ==========================
        $all_students = getStatutStudentByQuota(
            $info_student_quota,
            $info_studentsForclu_niv,
            $info_studentsForclu_sexe
        );

        $num_suppleant = null;

        foreach ($all_students as $student) {

            if ($student['num_etu'] == $num_student) {

                $rang = $student['rang'];

                $rang_suppleant = $rang + $info_student_quota;

                $info_suppleant = getAllDatastudentStatus_2(
                    $info_student_quota,
                    $info_studentsForclu_niv,
                    $info_studentsForclu_sexe,
                    $rang_suppleant
                );

                if ($info_suppleant) {
                    $num_suppleant = $info_suppleant['num_etu'];
                }

                break;
            }
        }

        // ==========================
        // Exécution forclusion
        // ==========================
        $requete = addForcloreManuel(
            $num_student,
            $motif_for,
            $_SESSION['username']
        );

        if ($requete == 1) {

            if (!empty($num_suppleant)) {
                sms_nv_attributaire($num_suppleant);
            }

            if (!empty($num_etu_heritier)) {
                sms_nv_suppleant($num_etu_heritier);
            }
        }
    }
}

header("Location: forclo.php?success=1&fac=$fac");
exit;