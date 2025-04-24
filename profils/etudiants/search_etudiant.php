<?php
require_once('../../traitement/fonction.php');
if (isset($_POST['search'])) {
    $num_etu = $_POST['search'];
    if ($dataStudentConnect = studentConnect($num_etu)) {
        $dataStudentConnect_classe = $dataStudentConnect['niveauFormation'];
        $dataStudentConnect_sexe = $dataStudentConnect['sexe'];
        $dataStudentConnect_quota = getQuotaClasse($dataStudentConnect_classe, $dataStudentConnect_sexe)['COUNT(*)'];
        $dataStudentConnect_statut = getOnestudentStatus($dataStudentConnect_quota, $dataStudentConnect_classe, $dataStudentConnect_sexe, $num_etu);
        $data_search = http_build_query(['data' => $dataStudentConnect_statut]);
        header('Location: resultat.php?'. $data_search);
        exit();
    } else {
        header('Location: resultat.php?erreurNum_etu=Etudiant non trouvé !!!');
        exit();
    }
}
