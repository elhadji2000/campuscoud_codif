<html class="no-js" lang="en">
<script langage='javascript'>
function upperCaseF(a){
    setTimeout(function(){
        a.value = a.value.toUpperCase();
    }, 1);
}
</script>
<?php /* if($_SERVER["HTTP_REFERER"]!="https://campuscoud.com/index" 
and $_SERVER["HTTP_REFERER"]!="https://campuscoud.com/" 
and $_SERVER["HTTP_REFERER"]!="https://campuscoud.com/mpo1"
 and $_SERVER["HTTP_REFERER"]!="https://campuscoud.com/mpo2")
{echo '<meta http-equiv="refresh" content="0;URL=index">'; exit();}  */

include('head.html');
?>

	<section id="homedesigne" class="s-homedesigne">
        
       <p class="lead">Bienvenue dans l'espace recuperation de compte !</p>
    </section> <!-- end s-stats -->
	
  <section id="styles" class="s-styles">
    
        <div class="row narrow section-intro add-bottom text-center">

            

        </div>
	  
	  
	   <div class="row add-bottom">

            <div class="row contact__main">
            <div class="col-eight tab-full contact__form1">
                <form name="contactForm" id="contactForm" method="post" action="mpo2">
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
                 alert('Suite à des soucis liés aux envois de mails, cette fonctionnalité a été suspendue. Veuillez reessayer dans quelques heures!')
                 </script>
                 <?php 
				 echo '<meta http-equiv="refresh" content="0;URL=log">';
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
<form action="mpo2" method="post">

<tr>
    <td colspan="4"><center>
        <strong>VEUILLEZ RENSEIGNER VOS INFORMATIONS PERSONNELLES <br>(Numero Carte, Date de naissance, NIN)</strong>
    </center>
    </td>
</tr>

 <fieldset>
    
                    <div class="form-field">
                        <input onkeydown="upperCaseF(this)"  name="num_etu" required type="text" id="num_etu" placeholder="Numero de carte (ou du certificat d'inscription)" value="" class="full-width">
                    </div>
                    <div class="form-field">
                        <input name="dateNaissance" required type="text" id="dateNaissance" placeholder="Date de Naissance format (JJ/MM/AAAA)"   aria-required="true" class="full-width">
                    </div>
                    <div class="form-field">
                        <input name="numIdentite" required type="text" id="numIdentite" placeholder="Numero d'Identification Nationale" value="" class="full-width">
                    </div>

					
                    <div class="form-field">
                        <button class="full-width btn--primary">Reinitialiser mon mot de passe</button>
                        <div class="submit-loader">
                            <div class="text-loader">Création de compte en cours...</div>
                            <div class="s-loader">
                                <div class="bounce1"></div>
                                <div class="bounce2"></div>
                                <div class="bounce3"></div>
                            </div>
                        </div>
                    </div>
    <br>
						 <!--a href='index'>Retour à la page d'accueil</a> <br-->
                    </fieldset>

</html>
