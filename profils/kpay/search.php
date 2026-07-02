<?php
session_start();
if (empty($_SESSION['username']) && empty($_SESSION['mdp'])) {
    header('Location: https://campuscoud.com/');
    exit();
}

// $fac = $_SESSION['fac'];
include ('../../traitement/fonction.php');
include ('../../traitement/kpay_fonction.php');
verif_type_mdp_2($_SESSION['username']);

$filters = [
    'fromDate' => $_GET['fromDate'] ?? '',
    'status' => $_GET['status'] ?? '',
    'customerPhoneNumber' => $_GET['customerPhoneNumber'] ?? '',
    'merchantPhoneNumber' => $_GET['merchantPhoneNumber'] ?? ''
];

// $token = getKpayToken();
$token = getKpaySessionToken();

$transactions = searchPayments(
    $token,
    $filters
);
?>
<?php

$success = 0;
$pending = 0;
$refunded = 0;
$failed = 0;
$canceled = 0;
$totalMontant = 0;

foreach ($transactions as $t) {
    $totalMontant += $t['amount'];

    switch (strtolower($t['status'])) {
        case 'succeeded':
            $success++;
            break;

        case 'pending':
            $pending++;
            break;
        case 'refunded':
            $refunded++;
            break;

        case 'failed':
            $failed++;
            break;

        case 'canceled':
            $canceled++;
            break;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COUD: CODIFICATION</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="log.gif" type="image/x-icon">
    <link rel="icon" href="log.gif" type="image/x-icon">
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="../../assets/css/styles.css">
    <link rel="stylesheet" href="../../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/bootstrap/js/bootstrap.min.js">
    <link rel="stylesheet" href="../../assets/bootstrap/js/bootstrap.bundle.min.js">

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <!-- DataTables Buttons -->
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">

    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <style>
    body {
        background: #eef2f7;
        font-family: Arial, sans-serif;
    }

    .kpay-filter .form-control,
    .kpay-filter .form-select,
    .kpay-filter .btn {
        height: 40px !important;
        border-radius: 5px !important;
        font-size: 12px !important;
    }

    .kpay-filter .form-label {
        font-weight: 600 !important;
        margin-bottom: 6px !important;
    }

    .kpay-filter .btn {
        min-width: 130px !important;
    }

    table tr td {
        font-size: 12px !important;
    }

    table tr td a {
        font-size: 12px !important;
        text-decoration: underline !important;
    }
    </style>
</head>
<?php include ('../../head.php'); ?>

<body>
    <div class="container-fluid mt-4">

        <div class="card shadow border-0">

            <div class="card-header bg-primary text-white">

                <div class="d-flex justify-content-between align-items-center">

                    <h4 class="mb-0">
                        Transactions KPay
                    </h4>

                    <span class="badge bg-light text-dark">
                        <?= count($transactions) ?> transaction(s)
                    </span>

                </div>

            </div>

            <div class="card-body">

                <!-- Statistiques -->

                <div class="row mb-4">

                    <div class="col-md-2">
                        <div class="alert alert-success text-center">
                            <h5><?= $success ?></h5>
                            <small>Réussis</small>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="alert alert-warning text-center">
                            <h5><?= $pending ?></h5>
                            <small>En attente</small>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="alert alert-primary text-center">
                            <h5><?= $refunded ?></h5>
                            <small>Rembourser</small>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="alert alert-danger text-center">
                            <h5><?= $failed?></h5>
                            <small>Echecs</small>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="alert alert-secondary text-center">
                            <h5><?= $canceled ?></h5>
                            <small>Annulé</small>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="alert alert-info text-center">
                            <h5><?= number_format($totalMontant, 0, ' ', ' ') ?></h5>
                            <small>FCFA</small>
                        </div>
                    </div>

                </div>

                <div class="card mb-3 shadow-sm">
                    <div class="card-body d-flex justify-content-center">

                        <form method="GET" class="kpay-filter">

                            <div class="row g-3">

                                <div class="col-md-2">
                                    <label class="form-label">
                                        Depuis le
                                    </label>

                                    <input type="datetime-local" name="fromDate"
                                        value="<?= htmlspecialchars($_GET['fromDate'] ?? '') ?>" class="form-control">
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">
                                        Statut
                                    </label>

                                    <select name="status" class="form-select">

                                        <option value="">Tous</option>

                                        <option value="succeeded"
                                            <?= (($_GET['status'] ?? '') == 'succeeded') ? 'selected' : '' ?>>
                                            Réussi
                                        </option>

                                        <option value="pending"
                                            <?= (($_GET['status'] ?? '') == 'pending') ? 'selected' : '' ?>>
                                            En attente
                                        </option>

                                        <option value="failed"
                                            <?= (($_GET['status'] ?? '') == 'failed') ? 'selected' : '' ?>>
                                            Échoué
                                        </option>

                                        <option value="refunded"
                                            <?= (($_GET['status'] ?? '') == 'refunded') ? 'selected' : '' ?>>
                                            Rembourser
                                        </option>

                                        <option value="initiated"
                                            <?= (($_GET['status'] ?? '') == 'initiated') ? 'selected' : '' ?>>
                                            Initié
                                        </option>

                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">
                                        Tél. Client
                                    </label>

                                    <input type="text" name="customerPhoneNumber"
                                        value="<?= htmlspecialchars($_GET['customerPhoneNumber'] ?? '') ?>"
                                        class="form-control" placeholder="2217xxxxxxx">
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">
                                        Tél. Marchand
                                    </label>

                                    <input type="text" name="merchantPhoneNumber"
                                        value="<?= htmlspecialchars($_GET['merchantPhoneNumber'] ?? '') ?>"
                                        class="form-control" placeholder="2217xxxxxxx">
                                </div>

                                <div class="col-md-3 d-flex align-items-end">

                                    <button type="submit" class="btn btn-primary me-2">
                                        Rechercher
                                    </button>

                                    <a href="?" class="pt-3 btn btn-secondary">
                                        Réinitialiser
                                    </a>

                                </div>

                            </div>

                        </form>

                    </div>
                </div>

                <div class="table-responsive">
                    <!-- Messages -->
                    <?php
                    if (isset($_SESSION['error'])) {
                        echo '<div class="alert alert-danger text-center">' . $_SESSION['error'] . '</div>';
                        unset($_SESSION['error']);
                    }
                    if (isset($_SESSION['success'])) {
                        echo '<div class="alert alert-success text-center">' . $_SESSION['success'] . '</div>';
                        unset($_SESSION['success']);
                    }
                    ?>

                    <table id="tableKpay" class="table table-bordered table-hover align-middle" id="tableKpay">

                        <thead class="table-info">

                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Référence</th>
                                <th>Num_etu</th>
                                <th>Prenom(s)</th>
                                <th>Nom</th>
                                <th>Téléphone</th>
                                <th>Montant</th>
                                <th>Statut</th>
                                <th>Action</th>
                            </tr>

                        </thead>

                        <tbody>

                            <?php
                            $n = 1;
                            foreach ($transactions as $t):
                                ?>

                            <?php

                            $num = substr(strrchr($t['correlationReference'], '_'), 1);
                            $status = strtolower($t['status']);

                            $badge = 'secondary';

                            if ($status == 'succeeded') {
                                $badge = 'success';
                            }

                            if ($status == 'pending') {
                                $badge = 'warning';
                            }

                            if ($status == 'failed') {
                                $badge = 'danger';
                            }

                            if ($status == 'canceled') {
                                $badge = 'dark';
                            }

                            ?>

                            <tr>
                                <td>
                                    <?= $n++; ?>
                                </td>

                                <td>
                                    <?= date('d/m/Y H:i', strtotime($t['createdAt'])) ?>
                                </td>

                                <td>
                                    <?= $t['kpayReference'] ?>
                                </td>
                                <td>
                                    <?= $num ?? '' ?>
                                </td>

                                <td>
                                    <?= $t['customer']['firstName'] ?? '' ?>
                                </td>
                                <td>
                                    <?= $t['customer']['lastName'] ?? '' ?>
                                </td>

                                <td>
                                    <?= $t['customer']['phoneNumber'] ?? '' ?>
                                </td>

                                <td class="fw-bold">
                                    <?= number_format($t['amount'], 0, ' ', ' ') ?>
                                </td>

                                <td>
                                    <span class="text-white badge bg-<?= $badge ?>">
                                        <?= strtoupper($t['status']) ?>
                                    </span>
                                </td>

                                <td>

                                    <!-- Annuler -->
                                    <?php if ($status == 'pending'): ?>

                                    <a class="text-warning text-decoration-underline"
                                        onclick="return confirm('Annuler ce paiement ?')"
                                        href="action_paiement.php?action=cancel&reference=<?= urlencode($t['correlationReference']) ?>&kpay_reference=<?= urlencode($t['kpayReference']) ?>">
                                        Annuler
                                    </a>

                                    <?php else: ?>

                                    <a class="text-muted">
                                        Annuler
                                    </a>

                                    <?php endif; ?>

                                    |

                                    <!-- Rembourser -->
                                    <?php if ($status == 'succeeded'): ?>

                                    <a class="text-danger text-decoration-underline"
                                        onclick="return confirm('Confirmer le remboursement ?')"
                                        href="action_paiement.php?action=refund&reference=<?= urlencode($t['correlationReference']) ?>&kpay_reference=<?= urlencode($t['kpayReference']) ?>">
                                        Rembourser
                                    </a>

                                    <?php else: ?>

                                    <a class="text-muted">
                                        Rembourser
                                    </a>

                                    <?php endif; ?>

                                </td>

                            </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>
    <script>
    document
        .getElementById("searchInput")
        .addEventListener("keyup", function() {

            let value = this.value.toLowerCase();

            let rows = document.querySelectorAll("#tableKpay tbody tr");

            rows.forEach(row => {

                row.style.display =
                    row.innerText.toLowerCase().includes(value) ?
                    "" :
                    "none";

            });

        });
    </script>
    <script>
    $(document).ready(function() {

        $('#tableKpay').DataTable({

            pageLength: 100,

            lengthMenu: [100, 200, 500, 1000],

            dom: 'lBfrtip',

            buttons: [{
                extend: 'excelHtml5',
                text: 'Export Excel',
                className: 'btn btn-success btn-sm',
                title: 'kpay paiement',

                exportOptions: {
                    columns: ':not(:last-child)'
                }
            }],

            language: {
                search: "Rechercher :",
                lengthMenu: "Afficher _MENU_",
                info: "Affichage _START_ à _END_ sur _TOTAL_",
                paginate: {
                    previous: "Précédent",
                    next: "Suivant"
                }
            }

        });

    });
    </script>

</body>

</html>