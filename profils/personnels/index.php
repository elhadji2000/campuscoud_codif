<?php session_start();
if (empty($_SESSION['username']) && empty($_SESSION['mdp'])) {
    header('Location: /campuscoud/');
    exit();
}
unset($_SESSION['classe']);
include('../../traitement/fonction.php');
include('../../traitement/requete.php');

verif_type_mdp_2($_SESSION['username']);

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
    <style>

    .container2 {
        display: flex;
        flex-direction: column;
        /* pour les empiler verticalement */
        gap: 20px;
        /* espace entre les liens */
        background-color: #fff;
        padding: 40px 60px;
        border: 2px solid #007BFF;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        text-align: center;
    }

    .container2 a {
        text-decoration: underline;
        color: #007BFF;
        font-weight: bold;
        font-size: 18px;
        transition: 0.3s;
    }

    .container2 a:hover {
        color: #0056b3;
    }
    </style>
</head>

<body>
    <?php include('../../head.php'); ?>
    <br>
    <div class="container container2">
        <strong><a href="niveau" class="text-decoration-underline">Quota pédagogique</a></strong>
        <strong><a href="sociale_select" class="text-decoration-underline" >Quota sociale</a></strong>
    </div>
    </div>
    <script src="../../assets/js/jquery-3.2.1.min.js"></script>
    <script src="../../assets/js/plugins.js"></script>
    <script src="../../assets/js/main.js"></script>
</body>
<script src="../../assets/js/script.js"></script>

</html>