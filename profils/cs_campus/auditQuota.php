<?php
session_start();
if (empty($_SESSION['username']) && empty($_SESSION['mdp'])) {
    header('Location: /coud_app_codification_final/');
    exit();
}
if (empty($_SESSION['classe'])) {
    header('location: /coud_app_codification_final/profils/personnels/niveau2.php');
    exit();
}

include('../../traitement/fonction.php');
connexionBD();
include('../../traitement/requete2.php');
$data_all_quota_F = getQuotaClasse_2($_SESSION['classe'], $_SESSION['sexe_agent']);

// Regrouper les données par pavillon -> chambre -> lits (avec id_lit_q)
$structure = [];
while ($row = mysqli_fetch_assoc($data_all_quota_F)) {
    $pavillon = $row['pavillon'];
    $chambre = $row['chambre'];
    $structure[$pavillon][$chambre][] = [
        'lit' => $row['lit'],
        'id_lit_q' => $row['id_lit_q']
    ];
}

if (isset($_GET['message'])) {
    $_SESSION['message'] = $_GET['message'];
} else {
    $_SESSION['message'] = '';
}
if (isset($_GET['message2'])) {
    $_SESSION['message2'] = $_GET['message2'];
} else {
    $_SESSION['message2'] = '';
}
if (isset($_GET['message3'])) {
    $_SESSION['message3'] = $_GET['message3'];
} else {
    $_SESSION['message3'] = '';
}
$bool = isDemarre($_SESSION['classe'], $_SESSION['sexe_agent']);
$isQuota = getQuotaClasse($_SESSION['classe'], $_SESSION['sexe_agent'])['COUNT(*)'];
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COUD: CODIFICATION</title>
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="../../assets/css/styles.css">
    <link rel="stylesheet" href="../../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <?php include('../../head.php'); ?>
    <div class="container mt-4">
        <div class="row" style="justify-content: center;">
            <?php if ($_SESSION['message']) { ?>
                <div class="col-md-6">
                    <div class="alert alert-success" role="alert">
                        <?= $_SESSION['message']; ?>
                    </div>
                </div>
            <?php } else if ($_SESSION['message2']) { ?>
                <div class="col-md-6">
                    <div class="alert alert-success" role="alert">
                        <?= $_SESSION['message2']; ?>
                    </div>
                </div>
            <?php } else if ($_SESSION['message3']) { ?>
                <div class="col-md-6">
                    <div class="alert alert-danger" role="alert">
                        <?= $_SESSION['message3']; ?>
                    </div>
                </div>
            <?php } ?>
        </div>
        <h2><?= $_SESSION['classe'] ?> (<?= $_SESSION['sexe_agent'] ?>)</h2>
        <table class="table table-bordered" style="font-size: 16px;">
            <thead class="table" style="background-color:#3777b0; color: white;">
                <tr>
                    <th>Pavillon</th>
                    <th>Chambre</th>
                    <th>Lit</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($structure as $pavillon => $chambres): ?>
                    <?php $rowspanPavillon = array_sum(array_map('count', $chambres)); ?>
                    <?php $firstPavillon = true; ?>
                    <?php foreach ($chambres as $chambre => $lits): ?>
                        <?php $rowspanChambre = count($lits); ?>
                        <?php $firstChambre = true; ?>
                        <?php foreach ($lits as $info_lit): ?>
                            <tr>
                                <?php if ($firstPavillon): ?>
                                    <td rowspan="<?= $rowspanPavillon ?>" style="vertical-align: middle; text-align: center;"><?= $pavillon ?></td>
                                    <?php $firstPavillon = false; ?>
                                <?php endif; ?>
                                <?php if ($firstChambre): ?>
                                    <td rowspan="<?= $rowspanChambre ?>" style="vertical-align: middle; text-align: center;"><?= $chambre ?></td>
                                    <?php $firstChambre = false; ?>
                                <?php endif; ?>
                                <td style="text-align: center;"><?= $info_lit['lit'] ?></td>
                                <td style="text-align: center;">
                                    <?php if (!$bool) { ?>
                                        <a class="btn btn-danger btn-sm" type="button" data-toggle="modal" data-target="#exampleModal1">
                                            <i class="fa fa-trash"></i> Supprimer
                                        </a>
                                    <?php } else { ?>
                                        <a class="btn btn-dark btn-sm" data-toggle="modal" data-target="#exampleModal1" style="pointer-events: none; color: gray; cursor: not-allowed; text-decoration: none;">
                                            <i class="fa fa-trash"></i> Supprimer
                                        </a>
                                    <?php } ?>
                                </td>
                            </tr>

                            <!-- Modal -->
                            <div class="modal fade" id="exampleModal1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">CONFIRMATION DE SUPPRESSION</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            ETES-VOUS SUR DE VOULOIR CONTINUER ?
                                        </div>
                                        <div class="modal-footer">
                                            <a type="button" class="btn btn-secondary" data-dismiss="modal">ANNULER</a>
                                            <a href="requestAuditQuota.php?id_lit_q=<?= $info_lit['id_lit_q'] ?>" class="btn btn-danger">SUPPRIMER</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        <?php endforeach; ?>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </tbody>
        </table>


        <!-- Button trigger modal -->
        <!-- <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
  Launch demo modal
</button> -->

        <!-- Modal -->
        <!-- <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div> -->


        <form action="requestAuditQuota.php" method="post">
            <input type="text" name="niveauFormation" value="<?= $_SESSION['classe'] ?>" style="visibility: hidden;">
            <!-- Button trigger modal -->
            <?php if ((!$bool) && $isQuota>0) { ?>
                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#exampleModal">
                    VALIDER
                </button>
            <?php } else { ?>
                <a href="niveau2.php" class="btn btn-secondary">
                    RETOUR
                </a>
            <?php } ?>

            <!-- Modal -->
            <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">CONFIRMATION</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            VOUS ETES SUR DE VOULOIR CONTINUER ?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">ANNULER</button>
                            <button type="submit" class="btn btn-success">VALIDER</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</body>
<script src="../../assets/js/jquery-3.2.1.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="../../assets/js/script2.js"></script>

</html>