<?php
session_start();
require '../../excel/vendor/autoload.php'; // Inclure PhpSpreadsheet

include('../../traitement/fonction.php');
//verif_type_mdp_2($_SESSION['username']);

// Récupérer les données
$pavillonDonne = isset($_GET["pavillon"]) ? $_GET["pavillon"] : '';
$result = getPaymentDetailsByPavillon($pavillonDonne, $connexion);

// Initialisation de PhpSpreadsheet
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// En-tête du fichier Excel
$sheet->setCellValue('A1', '#');
$sheet->setCellValue('B1', 'Chambre');
$sheet->setCellValue('C1', 'Lit');
$sheet->setCellValue('D1', 'Num Étudiant');
$sheet->setCellValue('E1', 'Nom');
$sheet->setCellValue('F1', 'Montant Facturé');
$sheet->setCellValue('G1', 'Montant Payé');
$sheet->setCellValue('H1', 'Restant');

// Remplir les données
$rowNum = 2;
foreach ($result as $index => $data) {
    $sheet->setCellValue('A' . $rowNum, $index + 1);
    $sheet->setCellValue('B' . $rowNum, $data['chambre']);
    $sheet->setCellValue('C' . $rowNum, $data['lit']);
    $sheet->setCellValue('D' . $rowNum, $data['num_etu']);
    $sheet->setCellValue('E' . $rowNum, $data['etudiant_prenoms'] . ' ' . $data['etudiant_nom']);
    $sheet->setCellValue('F' . $rowNum, $data['montant_facture']);
    $sheet->setCellValue('G' . $rowNum, $data['montant_paye']);
    $sheet->setCellValue('H' . $rowNum, $data['reste_a_payer']);
    $rowNum++;
}

// Calcul des totaux
$totalFacture = array_sum(array_column($result, 'montant_facture'));
$totalPaye = array_sum(array_column($result, 'montant_paye'));
$totalReste = array_sum(array_column($result, 'reste_a_payer'));

// Ajouter la ligne des totaux
$sheet->setCellValue('E' . $rowNum, 'Totaux');
$sheet->setCellValue('F' . $rowNum, $totalFacture);
$sheet->setCellValue('G' . $rowNum, $totalPaye);
$sheet->setCellValue('H' . $rowNum, $totalReste);

// Mettre en gras la ligne des totaux
$sheet->getStyle('E' . $rowNum . ':H' . $rowNum)->getFont()->setBold(true);

// Télécharger le fichier Excel
$filename = 'recouvrements_' . $pavillonDonne . '.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit();
