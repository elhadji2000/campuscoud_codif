<script type="text/javascript">
function sansEspace() 
{ 
// interdiction d'utiliser le bouton espace 
if (event.keyCode == 32) return false; 
return true; 
} 
</script>

<html class="no-js" lang="en">
<script langage='javascript'>
function upperCaseF(a){
    setTimeout(function(){
        a.value = a.value.toUpperCase();
    }, 1);
}
</script>
<?php 

include('traitement/fonction.php');
//verif_cloture();

include('head.html');

//getTelephoneEtudiant('20230c4mh');
?>


   <!--script langage='javascript'>
                 alert('Fonctionnalite momentanement indisponible, veuillez reessayer plus tard...')
                 </script-->
                 <?php
                /* echo '<meta http-equiv="refresh" content="0;URL=index">';
                     exit();*/?>



	<section id="homedesigne" class="s-homedesigne">
        
       <p class="lead">Bienvenue dans l'espace creation de compte !</p>
    </section> <!-- end s-stats -->
	
  <section id="styles" class="s-styles">
    
        <div class="row narrow section-intro add-bottom text-center">

            

        </div>
	  
	  
	   <div class="row add-bottom">

            <div class="row contact__main">
            <div class="col-eight tab-full contact__form1">
                <form name="contactForm" id="contactForm" method="post" action="insc2">
<?php

if (isset($_SERVER['HTTPS']) &&    ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) ||
    isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
    $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') 
	{
  $protocol = 'https';
}
else {
  $protocol = 'http';
}
/*
?>
                 <script langage='javascript'>
                 alert('Suite à des soucis liés aux envois de mails, la creation de compte a été suspendue. Veuillez reessayer dans quelques minutes!')
                 </script>
                 <?php 
				 echo '<meta http-equiv="refresh" content="0;URL=index">';
                     exit();*/
?>

<!--script type="text/javascript"> 
function noBack(){window.history.forward()} 
noBack(); 
window.onload=noBack; 
window.onpageshow=function(evt){if(evt.persisted)noBack()} 
window.onunload=function(){void(0)} 
</script-->
<body>
<form action="insc2" method="post">

<tr>
    <td colspan="4"><center>
        <strong>VEUILLEZ RENSEIGNER VOS INFORMATIONS PERSONNELLES <br>(Numero Carte, Date de naissance, NIN)</strong>
    </center>
    </td>
</tr>

 <fieldset>
    
                    <div class="form-field">
                        <input onkeydown="upperCaseF(this)" name="num_etu"  required type="text" onkeydown='return sansEspace();' id="num_etu" placeholder="Numero de carte (ou du certificat d'inscription)" value="" class="full-width">
                    </div>
                    <div class="form-field">
                        <input name="dateNaissance" required type="text" onkeydown='return sansEspace();' id="dateNaissance" placeholder="Date de Naissance (JJ/MM/AAAA)"   aria-required="true" class="full-width">
                    </div>
                    <div class="form-field">
                        <input name="numIdentite" required type="text" onkeydown='return sansEspace();' id="numIdentite" placeholder="Numero C.N.I (13 chiffres, sans espace)" value="" class="full-width">
                    </div>
					
					<!--div class="form-field">
                        <input name="telephone" required  maxlength = "9" minlength = "9" type="text" id="telephone" placeholder="Telephone (9 chiffres) pour recevoir le code par SMS"  onkeydown='return sansEspace();' value="" class="full-width">
                    </div-->
					
                    <div class="form-field">
                        <button class="full-width btn--primary">Envoyer mon inscription</button>
						<br><br>
						  <a href='rc'>Faire une reclamation?</a> <br>
                        <div class="submit-loader">
                            <div class="text-loader">Création de compte encours...</div>
                            <div class="s-loader">
                                <div class="bounce1"></div>
                                <div class="bounce2"></div>
                                <div class="bounce3"></div>
                            </div>
                        </div>
                    </div>
				
					<br>
						 <center>  <a href='index'>Retour</a>  </center>
    
                    </fieldset>

</html>
