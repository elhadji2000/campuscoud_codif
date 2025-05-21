<?php
session_start();
require '../../excel/vendor/autoload.php'; // Inclure PhpSpreadsheet

include('../../traitement/fonction.php');
verif_type_mdp_2($_SESSION['username']);

// Récupérer les données
$pavillonDonne = isset($_GET["pavillon"]) ? $_GET["pavillon"] : '';
$result = getPaymentDetailsByPavillon($pavillonDonne, $connexion);

// Initialisation de PhpSpreadsheet
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Titre du document
$sheet->setCellValue('A1', 'Détails des recouvrements - Pavillon ' . $pavillonDonne);
$sheet->mergeCells('A1:J1');
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
$sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// En-tête du fichier Excel
$headers = [
    '#', 'Chambre', 'Lit', 'Num Étudiant', 'Étudiant', 
    'Montant Facturé', 'Montant Payé', 'Caution', 'Loyer', 'Reste à Payer'
];

$sheet->fromArray($headers, null, 'A3');

// Style pour les en-têtes
$headerStyle = [
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1A66B1']],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
];
$sheet->getStyle('A3:J3')->applyFromArray($headerStyle);

// Remplir les données
$rowNum = 4;
$totalFacture = 0;
$totalPaye = 0;
$totalCaution = 0;
$totalLoyer = 0;
$totalRestant = 0;

foreach ($result as $index => $data) {
    $sheet->setCellValue('A' . $rowNum, $index + 1);
    $sheet->setCellValue('B' . $rowNum, $data['chambre']);
    $sheet->setCellValue('C' . $rowNum, $data['lit']);
    $sheet->setCellValue('D' . $rowNum, $data['num_etu']);
    $sheet->setCellValue('E' . $rowNum, $data['etudiant_prenoms'] . ' ' . $data['etudiant_nom']);
    $sheet->setCellValue('F' . $rowNum, $data['montant_facture_total']);
    $sheet->setCellValue('G' . $rowNum, $data['montant_paye_total']);
    $sheet->setCellValue('H' . $rowNum, $data['montant_caution_facture']);
    $sheet->setCellValue('I' . $rowNum, $data['loyer_paye']);
    $sheet->setCellValue('J' . $rowNum, $data['reste_a_payer_total']);
    
    // Format numérique avec séparateur de milliers
    foreach (['F', 'G', 'H', 'I', 'J'] as $col) {
        $sheet->getStyle($col . $rowNum)
              ->getNumberFormat()
              ->setFormatCode('#,##0');
    }
    
    // Style pour les lignes
    $sheet->getStyle('A' . $rowNum . ':J' . $rowNum)->applyFromArray([
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
    ]);
    
    // Alignement à droite pour les colonnes numériques
    foreach (['F', 'G', 'H', 'I', 'J'] as $col) {
        $sheet->getStyle($col . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    }
    
    // Couleur pour le reste à payer
    if ($data['reste_a_payer_total'] > 0) {
        $sheet->getStyle('J' . $rowNum)->getFont()->getColor()->setRGB('FF0000');
    } else {
        $sheet->getStyle('J' . $rowNum)->getFont()->getColor()->setRGB('00AA00');
    }
    
    $totalFacture += $data['montant_facture_total'];
    $totalPaye += $data['montant_paye_total'];
    $totalCaution += $data['montant_caution_facture'];
    $totalLoyer += $data['loyer_paye'];
    $totalRestant += $data['reste_a_payer_total'];
    
    $rowNum++;
}

// Ajouter la ligne des totaux
$sheet->setCellValue('E' . $rowNum, 'TOTAUX');
$sheet->setCellValue('F' . $rowNum, $totalFacture);
$sheet->setCellValue('G' . $rowNum, $totalPaye);
$sheet->setCellValue('H' . $rowNum, $totalCaution);
$sheet->setCellValue('I' . $rowNum, $totalLoyer);
$sheet->setCellValue('J' . $rowNum, $totalRestant);

// Style pour la ligne des totaux
$totalStyle = [
    'font' => ['bold' => true],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F9CA24']],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
];
$sheet->getStyle('E' . $rowNum . ':J' . $rowNum)->applyFromArray($totalStyle);

// Format numérique pour les totaux
foreach (['F', 'G', 'H', 'I', 'J'] as $col) {
    $sheet->getStyle($col . $rowNum)
          ->getNumberFormat()
          ->setFormatCode('#,##0');
}

// Alignement à droite pour les totaux
foreach (['F', 'G', 'H', 'I', 'J'] as $col) {
    $sheet->getStyle($col . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
}

// Couleur pour le total restant
if ($totalRestant > 0) {
    $sheet->getStyle('J' . $rowNum)->getFont()->getColor()->setRGB('FF0000');
} else {
    $sheet->getStyle('J' . $rowNum)->getFont()->getColor()->setRGB('00AA00');
}

// Ajuster la largeur des colonnes
foreach (range('A', 'J') as $column) {
    $sheet->getColumnDimension($column)->setAutoSize(true);
}

// Date et heure de génération
$sheet->setCellValue('A' . ($rowNum + 2), 'Généré le ' . date('d/m/Y à H:i'));
$sheet->getStyle('A' . ($rowNum + 2))->getFont()->setItalic(true);

// Télécharger le fichier Excel
$filename = 'recouvrements_' . $pavillonDonne . '_' . date('Ymd_His') . '.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit();