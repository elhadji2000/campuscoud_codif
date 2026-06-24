<?php
session_start();
include('../../traitement/fonction.php');

$id = intval($_POST['id']);

if ($id > 0) {
    mysqli_query($connexion, "UPDATE codif_messagerie SET lu=1 WHERE id=$id");
}
