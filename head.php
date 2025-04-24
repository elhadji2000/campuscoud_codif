<?php if (empty($_SESSION['username']) && empty($_SESSION['mdp'])) {
  header('Location: /campuscoud.com/');
  exit();
}

require_once(__DIR__ . '/traitement/fonction.php');

if ($_SESSION['profil'] == 'user') {
  $inforequeteAffectEtu = getStudentChoiseLit($_SESSION['id_etu']);

  $affecter = 0;
  while ($row = $inforequeteAffectEtu->fetch_assoc()) {
    $affecter++;
  }

  $quotaStudentConnect = getQuotaClasse($_SESSION['classe'], $_SESSION['sexe_etudiant'])['COUNT(*)']; //var_dump($quotaStudentConnect); die;
  $statutStudentConnect = getOnestudentStatus($quotaStudentConnect, $_SESSION['classe'], $_SESSION['sexe_etudiant'], $_SESSION['num_etu']);

if($statutStudentConnect['statut'] != 'Suppleant(e)')  {
	$resultatReqLitEtu = getOneLitByStudent($_SESSION['num_etu']);
} else  {
   $monTitulaire = getOneTitulaireBySuppleant($quotaStudentConnect, $_SESSION['classe'], $_SESSION['sexe_etudiant'], $statutStudentConnect['rang']);
  $resultatReqLitEtu = getOneLitByStudent($monTitulaire['num_etu']);
} 

  
}
include('activite.php');
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
    <!--script type="text/javascript" src="http://gc.kis.v2.scr.kaspersky-labs.com/FD126C42-EBFA-4E12-B309-BB3FDD723AC1/main.js?attr=XVAeJj2H8SiGwCW5J1IPPcts3FQ0aufoBHihAk5OJxq__d0uH7HdmpAcb7IONjRf_X8CZD-oGIglx6sUchpI_HYSIuxjlIWfVnPuZbs02VmnPdHOhWp4ZYS5cesFzatCiui-dXbcxsY8piHQq6Jz-pnlufRYyuGSc6Ae4wADXh0FdQjNGEdnc483w5ZQchd-SyWJ3NFD4Cmbo2r05Z3tQA" charset="UTF-8"></script--><script src="../js/modernizr.js"></script>
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
    
	  <a class="site-logo" href="#"><img src="/assets/images/logo.png" alt="Homepage" /></a>
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
        
        
                        <?php if (($_SESSION['profil'] == 'dba')) { ?>
          
          <li class="nav-item">
            <a class="nav-link" href="affUsers" title="Affiche users ">Aff_users</a>
          </li>
           <li class="nav-item">
            <a class="nav-link" href="addUser" title="Affiche users ">Add_users</a>
          </li>
           <li class="nav-item">
            <a class="nav-link" href="attributaire" title="Affiche Attributaires ">Attributaires</a>
          </li>
           <li class="nav-item">
            <a class="nav-link" href="suppleant" title="Affiche Suppleants ">Suppleants</a>
          </li>
           <li class="nav-item">
            <a class="nav-link" href="evolution" title="Affiche evolution ">Evolution</a>
          </li >
           <li class="nav-item">
            <a class="nav-link" href="etudiant" title="Ajouter etudiant ">Add_etudiant</a>
          </li >
          
        <?php } ?>
        
        
          <?php if (($_SESSION['profil'] == 'chef_recette')) { ?>
          
          <li class="nav-item">
            <a class="nav-link" href="index" title="Suivre les recettes ">Suivi_Recettes</a>
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
            <a class="nav-link" href="index" title="Recouvrement ">Recouvrement</a>
          </li>
           <li class="nav-item">
            <a class="nav-link" href="loger" title="Residents ">Residents</a>
          </li>
           <li class="nav-item">
            <a class="nav-link" href="evolution" title="Evolution ">Evolution</a>
          </li>

        <?php } ?>
        
        
         <?php if (($_SESSION['profil'] == 'chef_departement')) { ?>
          
          <li class="nav-item">
            <a class="nav-link" href="index" title="Recouvrement ">Recouvrement</a>
          </li>
           <li class="nav-item">
            <a class="nav-link" href="loger" title="Residents ">Residents</a>
          </li>
           <li class="nav-item">
            <a class="nav-link" href="evolution" title="Evolution ">Evolution</a>
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
        <?php if (($_SESSION['profil'] == 'quota') && isset($_SESSION['classe'])) { ?>
          <li class="nav-item active">
            <a class="nav-link" href="listeLits" title="Revenir à la page d'accueil">Liste_Lits <span></span></a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="detailsLits" title="Détail des lits affecté à cette classe">Détails_du_choix</a>
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
		  <!--li class="nav-item active">
            <a class="nav-link" href="clando" title="Loger un etudiant Non Attributaire">Loger_1_Non_Attributaire</a>
          </li-->
		  <li class="nav-item active">
            <a class="nav-link" href="loger" title="Loger un etudiant">Loger</a>
          </li>
        <?php } 	
		
		
		
		
		if ($_SESSION['profil'] == 'user' and $_SESSION['type_mdp']=='updated') { ?>
          <li class="nav-item active">
            <a class="nav-link" href="../etudiants/resultat" title="Revenir à la page d'accueil">Resultats</a>
          </li>
		  

  <?php        
          if (($affecter == 0) && ($statutStudentConnect['statut'] == 'Attributaire')) {
            $_SESSION['lit_choisi'] = ''; ?>
            <li class="nav-item active">
              <a class="nav-link" href="../etudiants/codifier" title="Aller à la page des codifications">Choisir_un_lit</a>
			  </li>			
			<?php	
           } 
		   
		   else {
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
	  


$litvalide=getValidateLitByStudent_2($_SESSION['num_etu']);
if (($litvalide == 'oui') && ($statutStudentConnect['statut'] == 'Attributaire')) {		  
		  
	?>	  
 <li class="nav-item active">
              <a class="nav-link" href="../etudiants/mespaiement" title="Voir mes paiements">Mes_paiements</a>
            </li>	
<?php		  
}	?>	  
		  
<li class="nav-item active">
			<a class="nav-link" href="../etudiants/mp" title="Changer de mot de passe">Mot_de_passe</a>
          </li>		  

<?php } ?>

        <!--li class="nav-item">
          <a class="nav-link" href="../etudiants/mp" title="Déconnexion"><i class="fa fa-sign-out" aria-hidden="true"></i> Mot de passe</a>
        </li-->




<?php
if ($_SESSION['profil'] != 'user' ){ ?>	  
		  	  
 <li class="nav-item active">
              <a class="nav-link" href="../mp" title="Changer de mot de passe">Mot_de_passe</a>
            </li>	
<?php		  
}	?>	




		
		
		
        <li class="nav-item">
          <a class="nav-link" href="https://campuscoud.com/" title="Déconnexion"><i class="fa fa-sign-out" aria-hidden="true"></i> Déconnexion</a>
        </li>
		
      </ul>
    </nav>

    <a class="header-menu-toggle" href="#0"><span>Menu</span></a>
  </header>
  <!-- end s-header -->
</body>
<section id="homedesigne" class="s-homedesigne">
  <?php   if (($_SESSION['profil'] == 'quota') || ($_SESSION['profil'] == 'paiement') || ($_SESSION['profil'] == 'validation') || ($_SESSION['profil'] == 'chef_residence') || ($_SESSION['profil'] == 'forclusion') || ($_SESSION['profil'] == 'delai') || ($_SESSION['profil'] == 'cs_acp') || ($_SESSION['profil'] == 'dba') || ($_SESSION['profil'] == 'chef_campus') || ($_SESSION['profil'] == 'sag') || ($_SESSION['profil'] == 'chef_departement')  || ($_SESSION['profil'] == 'chef_recette') || ($_SESSION['profil'] == 'pcs')) { ?>
  
    <p class="lead">Espace Administration: Bienvenue! <br> <br> <span>
        (<?= $_SESSION['prenom'] . "  " . $_SESSION['nom'] ?>)
      </span></p>
  <?php } elseif ($_SESSION['profil'] == 'user' and $_SESSION['type_mdp']=='updated') { ?>
    <p class="lead">Bienvenue <?= studentConnect($_SESSION['num_etu'])['prenoms'] . ' ' . studentConnect($_SESSION['num_etu'])['nom']; ?> !<br> <br>
      <u>SITUATION:</u> Classe : <?= $statutStudentConnect['niveauFormation']; ?>. Quota: <?= $quotaStudentConnect; ?>Lits.
      <?php //$statutStudentConnect['moyenne']; ?>
      <?php //$statutStudentConnect['rang']; ?>
      Statut : <?= $statutStudentConnect['statut']; ?>.<br><br>
      <?php
	  if ($statutStudentConnect['statut'] == 'Suppleant(e)') {
        $monTitulaire = getOneTitulaireBySuppleant($quotaStudentConnect, $_SESSION['classe'], $_SESSION['sexe_etudiant'], $statutStudentConnect['rang']);
		
		$tel_titu=getTelephoneEtudiant($monTitulaire['num_etu']);
        ?>
        <u>MON TITULAIRE</u> : <?= $monTitulaire['prenoms'] . ' ' . $monTitulaire['nom'] . ' / Tél : ' . $tel_titu; 
		
		$resultatReqLitEtu = getOneLitByStudent($monTitulaire['num_etu']);
		//$lit_titu=$resultatReqLitEtu['lit']; echo $lit_titu;
		
		?><br><br>
        <?php
      } else if ($statutStudentConnect['statut'] == 'Attributaire') {
        if($monSuppleant = getOneSuppleantByTitulaire($quotaStudentConnect, $_SESSION['classe'], $_SESSION['sexe_etudiant'], $statutStudentConnect['rang']))
		{
		$tel_suppl=getTelephoneEtudiant($monSuppleant['num_etu']);
      ?>
        <u>MON SUPPLEANT</u> : <?= $monSuppleant['prenoms'] . ' ' . $monSuppleant['nom'] . ' / Tél : ' . $tel_suppl; ?><br><br>
      <?php
		}
      }
      if ($statutStudentConnect['statut'] == 'Attributaire') { ?>
        <u>ACTION A FAIRE:</u> <?= getValidateLogerByStudent($_SESSION['num_etu']); ?>
      
  <?php 
        //AFFICHAGE DU DERNIER DELAI
		$datesys=date('Y-m-d');
        getLastDelai($_SESSION['num_etu']); 
		$dernier_delai=getLastDelai($_SESSION['num_etu']);
		if($dernier_delai>=$datesys){
			$dernier_delai_fr=changedateusfr($dernier_delai);
		echo "<br><br><u>DERNIER DELAI:</u> ".$dernier_delai_fr;
		}
  ?>


      <?php } else 
      if ($statutStudentConnect['statut'] == 'Suppleant(e)') { ?>
        <u>ACTION A FAIRE:</u> <?php
                  if (getValidateLitBySuppleant($monTitulaire['num_etu'])) {
                    if (getValidateLitBySuppleant($_SESSION['num_etu'])) {
                      if (getValidatePaiementLitBySuppleant($monTitulaire['num_etu'])) {
                        if (getValidateLogerByTitulaire($monTitulaire['num_etu'])) {
                          if (getValidateLogerBySuppleant($_SESSION['num_etu'])) {
                            echo "Vous avez déjà logé!";
                          } else {
                            echo "Votre titulaire a logé, veuillez vous approcher du chef de residence pour loger!";
                          }
                        } else {
                          echo "Votre titulaire a payé la caution, mais n'a pas encore logé, veuillez patienter!";
                        }
                      } else {
                        echo "Veuillez patienter que votre titulaire paye la caution!";
                      }
                    } else {
                      echo "Votre titulaire a validé sa codification, merci de faire de meme au service Hebergement!";
                    }
                  } else {
                    echo "Veuillez patienter que votre titulaire valide sa codification, pour faire de meme!";
                  }
                  ?>
      <?php } else 
      if ($statutStudentConnect['statut'] == 'Forclos(e)') { 
	  
	  echo "Vous etes forclos(e) le ".getMotifForclusion($_SESSION['id_etu'])['0'].". Motif: ".getMotifForclusion($_SESSION['id_etu'])['1'];
	  }
	  
	  ?>
    </p>
  <?php } 
  
		
		if ($_SESSION['profil'] == 'user' and $_SESSION['type_mdp']!='updated') { ?>
		 <p class="lead">Bienvenue <?= studentConnect($_SESSION['num_etu'])['prenoms'] . ' ' . studentConnect($_SESSION['num_etu'])['nom']; ?>
          </p>
		<?php     }
  
  
  ?>
</section>