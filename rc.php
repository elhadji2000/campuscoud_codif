<html class="no-js" lang="en">
<script langage='javascript'>
function upperCaseF(a){
    setTimeout(function(){
        a.value = a.value.toUpperCase();
    }, 1);
}


function sansEspace() 
{ 
// interdiction d'utiliser le bouton espace 
if (event.keyCode == 32) return false; 
return true; 
} 
</script>
<?php /* if($_SERVER["HTTP_REFERER"]!="https://campuscoud.com/log" 
and $_SERVER["HTTP_REFERER"]!="https://campuscoud.com/rc1"
 and $_SERVER["HTTP_REFERER"]!="https://campuscoud.com/rc2")
{echo '<meta http-equiv="refresh" content="0;URL=index">'; exit();}  */

include('head.html');
include('activite.php');
?>

	<section id="homedesigne" class="s-homedesigne">
        
       <p class="lead">Bienvenue dans l'espace reclamation!</p>
    </section> <!-- end s-stats -->
	
  <section id="styles" class="s-styles">
    
        <div class="row narrow section-intro add-bottom text-center">

            

        </div>
	  
	  
	   <div class="row add-bottom">

            <div class="row contact__main">
            <div class="col-eight tab-full contact__form1">
                <form name="contactForm" id="contactForm" method="post" action="rc2">
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
?>

<!--script type="text/javascript"> 
function noBack(){window.history.forward()} 
noBack(); 
window.onload=noBack; 
window.onpageshow=function(evt){if(evt.persisted)noBack()} 
window.onunload=function(){void(0)} 
</script-->
<body>
<form action="rc2" method="post">

<tr>
    <td colspan="4"><center>
        <!--strong>VEUILLEZ RENSEIGNER LES CHAMPS OU APPELER AU 78 539 02 73 / 78 539 26 92</strong-->
		<strong>VEUILLEZ RENSEIGNER LES CHAMPS</strong>
    </center>
    </td>
</tr>

 <fieldset>
    
                    <div class="form-field">
                        <input onkeydown="upperCaseF(this)"  name="num_etu" required type="text" onkeydown='return sansEspace();' id="num_etu" placeholder="Numero de carte (ou du certificat d'inscription)" value="" class="full-width">
                    </div>
                   
					
					<div class="form-field">
                        <input name="contact" required  maxlength = "9" minlength = "9" type="text" id="contact" placeholder="Telephone(9 chiffres)"  onkeydown='return sansEspace();' value="" class="full-width">
                    </div>
					

					
					<div class="form-field">
					 <SELECT name="type"  required>  <option>Type de reclamation?</option>
					 
					 <option>Un message me dit que les informations saisies semblent incorrectes...</option>
					 <option>Je nai plus le Numero de Telephone que jai fourni à linscription</option>
					 <option>Apres ceation du compte, jai nai pas recu le SMS contenant le mot de passe</option>					
					<option>Autre</option>
					
                     </select>
		            </div>
		
					<div class="form-field">
					<textarea cols="75" required  type="txt" placeholder="Detailler votre reclamation ici..." name="reclamation" rows="2"></textarea>
					</div>
                    <div class="form-field">
                        <button class="full-width btn--primary">Envoyer ma reclamation</button>
                        <div class="submit-loader">
                            <div class="text-loader">Envoyer reclamation en cours...</div>
                            <div class="s-loader">
                                <div class="bounce1"></div>
                                <div class="bounce2"></div>
                                <div class="bounce3"></div>
                            </div>
                        </div>
                    </div>
    <br>
						 <a href='index'>Retour à la page d'accueil</a> <br>
                    </fieldset>

</html>
