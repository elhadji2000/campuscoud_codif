<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COUD: CODIFICATION</title>
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="../../assets/css/styles.css">
    <script src="../../assets/js/modernizr.js"></script>
    <script src="../../assets/js/pace.min.js"></script>
    <link rel="stylesheet" href="../../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/bootstrap/js/bootstrap.min.js">
    <link rel="stylesheet" href="../../assets/bootstrap/js/bootstrap.bundle.min.js">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Select CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css" rel="stylesheet">
    <style>
        .form-check {
            display: flex;
            align-items: center;
        }

        input[type=checkbox] {
            position: relative;
            right: -4%;
        }

        .form-check-label {
            margin-left: 15px;
            margin-top: 15px;
        }

        form {
            /* box-shadow: 0 0 3px rgba(0, 0, 0, 0.2); */
            padding: 20px;
        }
    </style>
</head>


<body>

<?php include('../../head.php');


// Vérifier si des messages sont présents dans la session et les afficher dans la modale
if (isset($_SESSION['all_messages']) && !empty($_SESSION['all_messages'])) {
    // Joindre tous les messages sous forme d'une seule chaîne HTML
    $messagesContent = '';
    foreach ($_SESSION['all_messages'] as $message) {
        $messagesContent .= "<p>$message</p>"; // Ajouter chaque message dans un paragraphe
    }

    // Vider les messages après les avoir récupérés
    unset($_SESSION['all_messages']);
}
//include('../../traitement/fonction.php');?>
    <div class="container">



        <form action="traitement_add_delai.php" method="POST">
            <div class="row">
                <div class="col-md-4">
                    <select name="nature" id="" class="form-select" required="required">
                        <option selected disabled>CHOISIR NATURE</option>
                        <option value="depart">Depart</option>
                        <option value="choix">Foclusion Choix Lit</option>
                        <option value="validation">Forclusion Validation Lit</option>
                        <option value="paiement">Forclusion Paiement Caution</option>
                        <option value="fermeture">Fermeture</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="date" name="date" class="form-control" placeholder="Last name">
                </div>
                <div class="col-md-4">
                    <select name="faculte[]" id="" multiple class="selectpicker form-control" data-live-search="true" placeholder="RENSIGGNER LA FACULTE" required>
                        <option disabled>CHOISIR FACULTE</option>
                    <?php
                         $dataEtablissement = getAllEtablissement();
                        while ($rowNiv = mysqli_fetch_array($dataEtablissement)) { ?>
                             <option value="<?= $rowNiv['etablissement']; ?>"><?= $rowNiv['etablissement']; ?></option> 
                        <?php } ?>
                    </select>
                </div>
            </div><br>
            <button type="submit" class="btn btn-outline-success">ENREGISTERER</button>
        </form>
    </div>
	    </div>

    
<!-- Modal -->
<div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title" id="messageModalLabel">messages d'erreur</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="modalMessageContent">
        <!-- Les messages seront affichés ici -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
      </div>
    </div>
  </div>
</div>
<script>
    // Vérifie si des messages sont présents (en vérifiant le contenu de #modalMessageContent)
    document.addEventListener('DOMContentLoaded', function () {
        var messagesContent = <?php echo json_encode($messagesContent ?? ''); ?>;
        if (messagesContent) {
            document.getElementById('modalMessageContent').classList.add('large-text');

            // Affiche le contenu des messages dans la modale
            document.getElementById('modalMessageContent').innerHTML = messagesContent;
            // Affiche la modale
            var myModal = new bootstrap.Modal(document.getElementById('messageModal'));
            myModal.show();
        }
    });
</script>

</body>
<script src="../../../assets/js/jquery-3.2.1.min.js"></script>
<script src="../../../assets/js/plugins.js"></script>
<script src="../../../assets/js/main.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<!-- Bootstrap Select JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>

</html>