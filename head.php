<?php
if (empty($_SESSION['username']) && empty($_SESSION['mdp'])) {
    header('Location: /campuscoud.com/');
    exit();
}

require_once (__DIR__ . '/traitement/fonction.php');

$is_valider_choix = getValidateLitByStudent_2($_SESSION['username']);

if ($_SESSION['profil'] == 'user') {
    $inforequeteAffectEtu = getStudentChoiseLit($_SESSION['id_etu']);

    $affecter = $inforequeteAffectEtu->num_rows;
    // /while ($row = $inforequeteAffectEtu->fetch_assoc()) {
    //  $affecter++;
    // }

    $quotaStudentConnect = getQuotaClasse($_SESSION['classe'], $_SESSION['sexe_etudiant'])['COUNT(*)'];  // var_dump($quotaStudentConnect); die;
    $statutStudentConnect = getOnestudentStatus(
        $quotaStudentConnect,
        $_SESSION['classe'],
        $_SESSION['sexe_etudiant'],
        $_SESSION['num_etu']
    );

    // Vérification AVANT accès au tableau
    if (!empty($statutStudentConnect) && isset($statutStudentConnect['statut'])) {
        if ($statutStudentConnect['statut'] != 'Suppleant(e)') {
            $resultatReqLitEtu = getOneLitByStudent($_SESSION['num_etu']);
        } else {
            // Vérifier aussi 'rang'
            if (isset($statutStudentConnect['rang'])) {
                $monTitulaire = getOneTitulaireBySuppleant(
                    $quotaStudentConnect,
                    $_SESSION['classe'],
                    $_SESSION['sexe_etudiant'],
                    $statutStudentConnect['rang']
                );

                // Vérifier que titulaire existe
                if (!empty($monTitulaire) && isset($monTitulaire['num_etu'])) {
                    $resultatReqLitEtu = getOneLitByStudent($monTitulaire['num_etu']);
                } else {
                    $resultatReqLitEtu = null;  // ou gérer autrement
                }
            } else {
                $resultatReqLitEtu = null;
            }
        }
    } else {
        // Cas où aucun statut trouvé
        $resultatReqLitEtu = null;
    }
}
include ('activite.php');
?>


<head>

    <!--- basic page needs
    ================================================== -->
    <meta charset="utf-8">
    <title>CAMPUSCOUD: Plateforme Numerique pour la Codification</title>
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- mobile specific metas
    ================================================== -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSS
    ================================================== -->
    <link rel="stylesheet" href="assets/css/base.css">
    <link rel="stylesheet" href="assets/css/vendor.css">
    <link rel="stylesheet" href="assets/css/main.css">

    <!-- script
    ================================================== -->
    <!--script type="text/javascript" src="http://gc.kis.v2.scr.kaspersky-labs.com/FD126C42-EBFA-4E12-B309-BB3FDD723AC1/main.js?attr=XVAeJj2H8SiGwCW5J1IPPcts3FQ0aufoBHihAk5OJxq__d0uH7HdmpAcb7IONjRf_X8CZD-oGIglx6sUchpI_HYSIuxjlIWfVnPuZbs02VmnPdHOhWp4ZYS5cesFzatCiui-dXbcxsY8piHQq6Jz-pnlufRYyuGSc6Ae4wADXh0FdQjNGEdnc483w5ZQchd-SyWJ3NFD4Cmbo2r05Z3tQA" charset="UTF-8"></script-->
    <script src="../js/modernizr.js"></script>
    <script src="assets/js/pace.min.js"></script>

    <!-- favicons
    ================================================== -->
    <link rel="shortcut icon" href="log.gif" type="../image/x-icon">
    <link rel="icon" href="log.gif" type="../image/x-icon">

</head>









<body id="top">
    <!-- header================================================== -->
    <header class="s-header">
        <div class="header-logo">

            <a class="site-logo" href="#"><img src="https://campuscoud.com/assets/images/logo.png" alt="Homepage" /></a>
            CAMPUSCOUD
        </div>
        <nav class="header-nav-wrap">
            <ul class="header-nav">

                <?php if (($_SESSION['profil'] == 'paiement')) { ?>
                <li class="nav-item">
                    <a class="nav-link" href="paiement" title="Encaissement de caution">Encaisser</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="etatPaiement" title="Changer de niveau de formation ">Etat</a>
                </li>
                <?php } ?>




                <?php if (($_SESSION['profil'] == 'audit')) { ?>
                <li class="nav-item">
                    <a class="nav-link" href="niveau2" title="Valider les quotas saisis">Changer_de_Formation</a>
                </li>

                <?php } ?>




                <?php if (($_SESSION['profil'] == 'message')) { ?>
                <li class="nav-item">
                    <a class="nav-link" href="niveau3" title="Valider les quotas saisis">Changer_de_Formation</a>
                </li>

                <?php } ?>




                <?php if (($_SESSION['profil'] == 'cs_acp')) { ?>

                <li class="nav-item">
                    <a class="nav-link" href="etatPaiement_cs" title="Etat des encaissements ">Encaissements</a>
                </li>
                <?php } ?>

                <?php if (($_SESSION['profil'] == 'pcs')) { ?>

                <li class="nav-item">
                    <a class="nav-link" href="evolution" title="Evolution de la Codification ">Evolution</a>
                </li>
                <?php } ?>

                <?php if (($_SESSION['profil'] == 'pcs2')) { ?>

                <li class="nav-item">
                    <a class="nav-link" href="evolution" title="Evolution de la Codification ">Evolution</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index" title="Evolution de la Codification ">Lits</a>
                </li>
                <?php } ?>


                <?php if (($_SESSION['profil'] == 'dba')) { ?>

                <li class="nav-item">
                    <a class="nav-link" href="affUsers" title="Affiche users ">Aff_users</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="addUser" title="Affiche users ">Add_users</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="evolution" title="Affiche evolution ">Evolution</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="etudiant_remplace" title="Remplacer ">Remplacer</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="details_ch" title="Rechercher les occupants de la chambre ">Chambre</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="permuter" title="Permuter">Permuter</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="etudiant" title="Ajouter etudiant ">Add_etudiant</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="stats" title="Permuter">Stats</a>
                </li>


                <?php } ?>

                <?php if (($_SESSION['profil'] == 'chef_service')) { ?>

                <li class="nav-item">
                    <a class="nav-link" href="sociale" title="sociale">sociale</a>
                </li>
                <?php } ?>


                <?php if (($_SESSION['profil'] == 'chef_recette')) { ?>

                <li class="nav-item">
                    <a class="nav-link" href="index" title="Suivre les recettes ">Suivi_Recettes</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="global" title="Suivre les recettes ">Suivi_Global</a>
                </li>

                <!-- li class="nav-item">
                    <a class="nav-link" href="attributaire" title="Affiche users ">Attributaires</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="suppleant" title="Affiche users ">Suppleants</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index" title="Affiche evolution ">Evolution</a>
                </li -->

                <?php } ?>


                <?php if (($_SESSION['profil'] == 'chef_campus')) { ?>


                <li class="nav-item">
                    <a class="nav-link" href="recouvr" title="Recouvrement ">Recouvrement</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index" title="Recouvrement ">Rech_Etu</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="details_ch" title="Recouvrement ">Chambre</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="loger" title="Residents ">Pavillon</a>
                </li>

                <!--li class="nav-item">
            <a class="nav-link" href="etudiant_remplace" title="Remplacer ">Remplacer</a>
          </li-->

                <li class="nav-item">
                    <a class="nav-link" href="evolution" title="Evolution ">Evolution</a>
                </li>

                <?php } ?>


                <?php if (($_SESSION['profil'] == 'chef_departement')) { ?>

                <!--li class="nav-item">
            <a class="nav-link" href="black_list" title="liste_rouge ">Black_Liste</a>
          </li-->
                <li class="nav-item">
                    <a class="nav-link" href="details_ch" title="Recouvrement ">Chambre</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index" title="Recouvrement ">Rech_Etu</a>
                </li>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="recouvr" title="Recouvrement ">Recouvr</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="loger" title="Residents ">Pavillon</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="evolution" title="Evolution ">Evolution</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index_aff" title="Recouvrement ">Aff_Sociale</a>
                </li>

                <?php } ?>


                <?php if (($_SESSION['profil'] == 'liste_rouge')) { ?>

                <li class="nav-item">
                    <a class="nav-link" href="index" title="Recouvrement ">liste_rouge</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="black_list_trait" title="Recouvrement ">liste_rouge_trait</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="black_list_view" title="Residents ">liste_rouge_view</a>
                </li>


                <?php } ?>



                <?php if (($_SESSION['profil'] == 'validation')) { ?>
                <li class="nav-item">
                    <a class="nav-link" href="validation" title="Paiement de caution">Validation</a>
                </li>
                <!-- <li class="nav-item">
            <a class="nav-link" href="../personnels/niveau" title="Changer de niveau de formation ">Changer-Classe</a>
          </li> -->
                <?php } ?>


                <?php if (($_SESSION['profil'] == 'quota')) { ?>
                <li class="nav-item">
                    <a class="nav-link" href="details_ch" title="Recherche de lits">Recherche de lits</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index" title="Recherche de lits">Quotas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="sociale" title="Recherche de lits">sociale</a>
                </li>
                <?php } ?>



                <?php if (($_SESSION['profil'] == 'retour')) { ?>
                <li class="nav-item active">
                    <a class="nav-link" href="index" title="Revenir à la page d'accueil">Titu_NoValid <span></span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="suppleant" title="supl_NoValid">supl_NoValid</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="nonAffect" title="NonAffect">NonAffect</a>
                </li>

                <?php if (($_SESSION['username'] == 'dba_rt')) { ?>
                <li class="nav-item">
                    <a class="nav-link" href="remplacer" title="remplacer">Remplacer</a>
                </li>
                <?php } ?>


                <?php } ?>



                <?php if (($_SESSION['profil'] == 'quota') && isset($_SESSION['classe'])) { ?>
                <li class="nav-item active">
                    <a class="nav-link" href="listeLits" title="Revenir à la page d'accueil">Liste_Lits
                        <span></span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="detailsLits"
                        title="Détail des lits affecté à cette classe">Détails_du_choix</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="niveau" title="Changer de niveau de formation ">Changer_de_Formation</a>
                </li>
                <?php } ?>


                <?php if (($_SESSION['profil'] == 'chef_residence')) { ?>
                <li class="nav-item active">
                    <a class="nav-link" href="recouvr" title="Suivre le recouvrement">Recouvrement</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="pavillon" title="Voir les residents">Residents</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="clando" title="Loger un etudiant Non Attributaire">Loger_clando</a>
                </li>

                <li class="nav-item active">
                    <a class="nav-link" href="pavillon_nonLoger"
                        title="Ils ont payé et doivent venir se presenter ...">A Surveiller</a>
                </li>

                <li class="nav-item active">
                    <a class="nav-link" href="loger" title="Loger un etudiant">Loger</a>
                </li>
                <?php } ?>

                <?php if (($_SESSION['profil'] == 'kpay')) { ?>
                <li class="nav-item active">
                    <a class="nav-link" href="search" title="Suivre le kpay">kpay</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="om_search" title="Voir les om">OM</a>
                </li>
                <?php } ?>




                <?php if (($_SESSION['profil'] == 'controle_cr')) { ?>
                <li class="nav-item active">
                    <a class="nav-link" href="recouvr" title="Suivre le recouvrement">Recouvrement</a>
                </li>

                <!--li class="nav-item active">
		  
            <a class="nav-link" href="pavillon" title="Voir les residents">Residents</a>
          </li -->

                <li class="nav-item active">
                    <a class="nav-link" href="pavillon_nonLoger"
                        title="Ils ont payé et doivent venir se presenter ...">A Surveiller</a>
                </li>
                <?php }

                if ($_SESSION['profil'] == 'user' and $_SESSION['type_mdp'] == 'updated') { ?>
                <li class="nav-item active">
                    <a class="nav-link" href="../etudiants/accueil" title="Revenir à la page d'accueil">Accueil</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="../etudiants/resultat" title="Revenir à la page d'accueil">Resultats</a>
                </li>


                <?php
                if ((($affecter == 1) && ($is_valider_choix != 'oui')) or (($affecter == 0) && ($statutStudentConnect['statut'] == 'Attributaire'))) {
                    // if (($is_valider_choix!="oui") && ($statutStudentConnect['statut'] == 'Attributaire') || ($affecter)) {
                    $_SESSION['lit_choisi'] = '';
                    ?>
                <li class="nav-item active">
                    <a class="nav-link" href="../etudiants/codifier"
                        title="Aller à la page des codifications">Choisir_un_lit</a>
                </li>
                <?php
                }

                if (($affecter == 1) && ($statutStudentConnect['statut'] == 'Attributaire')) {
                    while ($rows = $resultatReqLitEtu->fetch_assoc()) {
                        if ($rows['lit']) {
                            $_SESSION['lit_choisi'] = $rows['lit'];
                            $_SESSION['id_lit'] = $rows['id_lit'];
                        } else {
                            $_SESSION['lit_choisi'] = '';
                            $_SESSION['id_lit'] = '';
                        }
                    }
                }

                $litvalide = getValidateLitByStudent_2($_SESSION['num_etu']);
                if (($litvalide == 'oui') && ($statutStudentConnect['statut'] == 'Attributaire')) {
                ?>
                <li class="nav-item active">
                    <a class="nav-link" href="../etudiants/mespaiement" title="Voir mes paiements">Mes_paiements</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="../etudiants/payer" title="Voir mes paiements">Payer</a>
                </li>
                <?php
                }
                ?>

                <li class="nav-item active">
                    <a class="nav-link" href="../etudiants/mp" title="Changer de mot de passe">Mot_de_passe</a>
                </li>

                <?php } ?>
                <?php
                if ($_SESSION['profil'] != 'user') {
                    ?>

                <li class="nav-item active">
                    <a class="nav-link" href="../mp" title="Changer de mot de passe">Md_passe</a>
                </li>
                <?php
                }
                ?>







                <li class="nav-item">
                    <a class="nav-link" href="https://campuscoud.com/" title="Déconnexion"><i class="fa fa-sign-out"
                            aria-hidden="true"></i> Quitter</a>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link active dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        Mon_Compte
                    </a>

                    <ul class="dropdown-menu">

                        <!-- CHANGER ANNEE -->
                        <li>
                            <form action="http://localhost/ccoud/traitement/chance_annee" method="POST"
                                class="px-3 py-2">
                                <label class="small fw-semibold">Année</label>
                                <select name="annee" class="form-select form-select-sm mb-2" required>
                                    <?php
                                    $anneeDebut = 2024;
                                    $anneeActuelle = date('Y') -1;

                                    for ($i = $anneeActuelle; $i >= $anneeDebut; $i--) {
                                        $anneeUniversitaire = $i . '_' . ($i + 1);

                                        $selected = (
                                            isset($_SESSION['annee']) &&
                                            $_SESSION['annee'] == $anneeUniversitaire
                                        ) ? 'selected' : '';

                                        echo "<option value='$anneeUniversitaire' $selected>
                                        $anneeUniversitaire
                                    </option>";
                                    }
                                    ?>
                                </select>

                                <button type="submit" class="btn btn-primary btn-lg w-100">
                                    Changer
                                </button>
                            </form>
                        </li>

                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <!-- DECONNEXION -->
                        <li>
                            <a class="dropdown-item" href="https://campuscoud.com/"
                                onclick="return confirm('Etes-vous sûr ?')">
                                Déconnexion
                            </a>
                        </li>
                    </ul>
                </li>

            </ul>
        </nav>

        <a class="header-menu-toggle" href="#0"><span>Menu</span></a>
    </header>
    <!-- end s-header -->
</body>
<section id="homedesigne" class="s-homedesigne">
    <?php if (($_SESSION['profil'] == 'quota') || ($_SESSION['profil'] == 'paiement') || ($_SESSION['profil'] == 'validation') || ($_SESSION['profil'] == 'chef_service') || ($_SESSION['profil'] == 'chef_residence') || ($_SESSION['profil'] == 'controle_cr') || ($_SESSION['profil'] == 'forclusion') || ($_SESSION['profil'] == 'delai') || ($_SESSION['profil'] == 'cs_acp') || ($_SESSION['profil'] == 'dba') || ($_SESSION['profil'] == 'chef_campus') || ($_SESSION['profil'] == 'sag') || ($_SESSION['profil'] == 'chef_departement') || ($_SESSION['profil'] == 'chef_recette') || ($_SESSION['profil'] == 'audit') || ($_SESSION['profil'] == 'message') || ($_SESSION['profil'] == 'pcs') || ($_SESSION['profil'] == 'pcs2') || ($_SESSION['profil'] == 'liste_rouge') || ($_SESSION['profil'] == 'retour') || ($_SESSION['profil'] == 'kpay')) { ?>

    <p class="lead">Codification <?= $_SESSION['annee'] ?>: Bienvenue! <br> <br> <span>
            (<?= $_SESSION['prenom'] . '  ' . $_SESSION['nom'] ?>)
        </span></p>
    <?php } elseif ($_SESSION['profil'] == 'user' and $_SESSION['type_mdp'] == 'updated') { ?>
    <p class="lead">Codification <?= $_SESSION['annee'] ?>: Bienvenue
        <?= studentConnect($_SESSION['num_etu'])['prenoms'] . ' ' . studentConnect($_SESSION['num_etu'])['nom']; ?>
        !<br> <br>
        <u>SITUATION:</u> Classe : <?= $statutStudentConnect['niveauFormation']; ?>. Quota:
        <?= $quotaStudentConnect; ?>Lits.
        <?php // $statutStudentConnect['moyenne']; ?>
        <?php // $statutStudentConnect['rang']; ?>
        Statut : <?= $statutStudentConnect['statut']; ?>.<br><br>
        <?php
        if ($statutStudentConnect['statut'] == 'Suppleant(e)') {
            $monTitulaire = getOneTitulaireBySuppleant($quotaStudentConnect, $_SESSION['classe'], $_SESSION['sexe_etudiant'], $statutStudentConnect['rang']);

            $tel_titu = getTelephoneEtudiant($monTitulaire['num_etu']);
            ?>
        <u>MON TITULAIRE</u> : <?=
            $monTitulaire['prenoms'] . ' ' . $monTitulaire['nom'] . ' / Tél : ' . $tel_titu;

            $resultatReqLitEtu = getOneLitByStudent($monTitulaire['num_etu']);
            // $lit_titu=$resultatReqLitEtu['lit']; echo $lit_titu;

            ?><br><br>
        <?php
        } else if ($statutStudentConnect['statut'] == 'Attributaire') {
            if ($monSuppleant = getOneSuppleantByTitulaire($quotaStudentConnect, $_SESSION['classe'], $_SESSION['sexe_etudiant'], $statutStudentConnect['rang'])) {
                $tel_suppl = getTelephoneEtudiant($monSuppleant['num_etu']);
                ?>
        <u>MON SUPPLEANT</u> :
        <?= $monSuppleant['prenoms'] . ' ' . $monSuppleant['nom'] . ' / Tél : ' . $tel_suppl; ?><br><br>
        <?php
            }
        }
        if ($statutStudentConnect['statut'] == 'Attributaire') {
            ?>
        <u>ACTION A FAIRE:</u> <?= getValidateLogerByStudent($_SESSION['num_etu']); ?>

        <?php
        // AFFICHAGE DU DERNIER DELAI
        $datesys = date('Y-m-d');
        getLastDelai($_SESSION['num_etu']);
        $dernier_delai = getLastDelai($_SESSION['num_etu']);
        if ($dernier_delai >= $datesys) {
            $dernier_delai_fr = changedateusfr($dernier_delai);
            echo '<br><br><u>DERNIER DELAI:</u> ' . $dernier_delai_fr;
        }
        ?>


        <?php } else if ($statutStudentConnect['statut'] == 'Suppleant(e)') { ?>
        <u>ACTION A FAIRE:</u> <?php
            if (getValidateLitBySuppleant($monTitulaire['num_etu'])) {
                if (getValidateLitBySuppleant($_SESSION['num_etu'])) {
                    if (getValidatePaiementLitBySuppleant($monTitulaire['num_etu'])) {
                        if (getValidateLogerByTitulaire($monTitulaire['num_etu'])) {
                            if (getValidateLogerBySuppleant($_SESSION['num_etu'])) {
                                echo 'Vous avez déjà logé!';
                            } else {
                                echo 'Votre titulaire a logé, veuillez vous approcher du chef de residence pour loger!';
                            }
                        } else {
                            echo "Votre titulaire a payé la caution, mais n'a pas encore logé, veuillez patienter!";
                        }
                    } else {
                        echo 'Veuillez patienter que votre titulaire paye la caution!';
                    }
                } else {
                    echo 'Votre titulaire a validé sa codification, merci de faire de meme au service Hebergement!';
                }
            } else {
                echo 'Veuillez patienter que votre titulaire valide sa codification, pour faire de meme!';
            }
            ?>
        <?php
        } else if ($statutStudentConnect['statut'] == 'Forclos(e)') {
            echo 'Vous etes forclos(e) le ' . getMotifForclusion($_SESSION['id_etu'])['0'] . '. Motif: ' . getMotifForclusion($_SESSION['id_etu'])['1'];
        }

        ?>
    </p>
    <?php }

if ($_SESSION['profil'] == 'user' and $_SESSION['type_mdp'] != 'updated') { ?>
    <p class="lead">
        Codification <?= $_SESSION['annee'] ?> : Bienvenue
        <?php
        $student = studentConnect($_SESSION['num_etu']);

        $prenom = $student['prenoms'] ?? $_SESSION['prenom'];
        $nom = $student['nom'] ?? $_SESSION['nom'];

        echo $prenom . ' ' . $nom;
        ?>
    </p>
    <?php
}

?>
</section>