<?php
session_start();

require_once __DIR__ . '/vendor/autoload.php';
require('../../../traitement/fonction.php');
ini_set('memory_limit', '512M');
ini_set('max_execution_time', '300');
ini_set("pcre.backtrack_limit", "5000000");

$mpdf = new \Mpdf\Mpdf([
    'tempDir' => __DIR__ . '/tmp'
]);

// paramètres
$date_debut = $_POST['date_debut'] ?? $_GET['date_debut'] ?? '';
$date_fin   = $_POST['date_fin'] ?? $_GET['date_fin'] ?? '';
$username   = $_POST['regisseur'] ?? $_GET['regisseur'] ?? '';
$libelle    = $_POST['libelle'] ?? $_GET['libelle'] ?? '';

$timeD = $date_debut ? strtotime($date_debut) : strtotime('2026-01-01');
$timeF = $date_fin ? strtotime($date_fin) : strtotime(date('Y-m-d'));

$dateD = date('d/m/Y', $timeD);
$dateF = date('d/m/Y', $timeF);

$usernameDisplay = $username ?: 'Tous';

// récupération données
$page = 1;
$limit = 1000;

$data = getPaiementWithDateInterval_3($date_debut, $date_fin, $username, $libelle, $page, $limit);

$tabPaiment   = $data['data'] ?? [];
$totalMontant = $data['totalMontant'] ?? 0;

$html = '
<style>
table {width:100%;border-collapse:collapse;font-size:12px;}
th,td {border:1px solid #ddd;padding:6px;font-size:8px;}
th {background:#f2f2f2;font-size:10px;}
</style>

<p>
Republique du Sénégal<br>
Ministére de l\'Enseignement supérieur<br>
de la Recherche et de l\'Innovation<br>
________________________<br>
<b>Centre des Œuvres universitaires de Dakar</b><br>
________________________<br>
<b>Agence Comptable</b>
</p>

<h3 style="text-align:center">ETAT DES ENCAISSEMENTS</h3>

<p>
DU <b>'.$dateD.'</b> AU <b>'.$dateF.'</b><br>
Regisseur : <b>'.$usernameDisplay.'</b>
</p>

<table>

<thead>
<tr>
<th>Quittance</th>
<th>Date</th>
<th>Libelle</th>
<th>Num Étudiant</th>
<th>Prenom et NOM</th>
<th>Montant</th>
</tr>
</thead>

<tbody>
';

$mpdf->WriteHTML($html);


// ---------- CHUNK METHOD ----------

$chunkSize = 50;
$chunkHtml = '';
$count = 0;

foreach ($tabPaiment as $row) {

    $datePaiement = date('d/m/Y', strtotime($row['dateTime_paie']));

    if (isset($_SESSION['libelle']) && strtoupper(trim($_SESSION['libelle'])) === 'CAUTION') {
        $libelleValue = 'CAUTION';
    } else {

        $parts = explode(',', $row['libelle']);

        $filtered = array_filter($parts, function ($p) {
            return strtoupper(trim($p)) !== 'CAUTION';
        });

        $libelleValue = htmlspecialchars(implode(', ', $filtered));
    }

    $montant = $row['montant'];

    if (isset($_SESSION['libelle'])) {

        $sessionLibelle = strtoupper(trim($_SESSION['libelle']));

        if ($sessionLibelle === 'CAUTION') {
            $montant = 5000;
        }

        elseif ($sessionLibelle === 'LOYER') {

            if (stripos($row['libelle'], 'CAUTION') !== false) {
                $montant -= 5000;
            }

        }
    }

    $chunkHtml .= '
    <tr>
        <td>'.htmlspecialchars($row['quittance']).'</td>
        <td>'.$datePaiement.'</td>
        <td>'.$libelleValue.'</td>
        <td>'.htmlspecialchars($row['num_etu']).'</td>
        <td>'.htmlspecialchars($row['prenoms'].' '.$row['nom']).'</td>
        <td>'.number_format($montant,0,","," ").'</td>
    </tr>
    ';

    $count++;

    if ($count % $chunkSize == 0) {

        $mpdf->WriteHTML($chunkHtml);
        $chunkHtml = '';

    }
}

// dernier chunk
if ($chunkHtml != '') {
    $mpdf->WriteHTML($chunkHtml);
}

// footer

$footer = '

</tbody>

<tfoot>

<tr>
<td colspan="5" align="right"><b>TOTAL DE LA PERIODE</b></td>
<td><b>'.number_format($totalMontant,0,","," ").'</b></td>
</tr>

</tfoot>

</table>

';

$mpdf->WriteHTML($footer);

$mpdf->Output('etat_encaissement.pdf','I');