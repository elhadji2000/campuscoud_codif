<html class="no-js" lang="en">
<?php /*  if($_SERVER["HTTP_REFERER"]!="https://codification.ucad.sn/log" 
and $_SERVER["HTTP_REFERER"]!="https://codification.ucad.sn/mp1"
 and $_SERVER["HTTP_REFERER"]!="https://codification.ucad.sn/mp2")
{echo '<meta http-equiv="refresh" content="0;URL=index">'; exit();}  */

include('head.html');
?>

	<section id="homedesigne" class="s-homedesigne">
        
       <p class="lead">Bienvenue dans l'espace de recuperation de compte!</p>
    </section> <!-- end s-stats -->
	
  <section id="styles" class="s-styles">
    
        <div class="row narrow section-intro add-bottom text-center">

            

        </div>
	  
	  
	   <div class="row add-bottom">

            <div class="row contact__main">
            <div class="col-eight tab-full contact__form1">
                <form name="contactForm" id="contactForm" method="post" action="mp2">
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
<form action="mp2" method="post">

<tr>
    <td colspan="4"><center>
        <strong>VEUILLEZ RENSEIGNER LES CHAMPS</strong>
    </center>
    </td>
</tr>

 <fieldset>
    
                    <div class="form-field">
                        <input name="num_etu" required type="text" id="num_etu" placeholder="Numero de carte (ou du certificat d'inscription)" value="" class="full-width">
                    </div>
                    <!--div class="form-field">
                        <input name="dateNaissance" required type="date" id="dateNaissance" placeholder="Date de Naissance" value="01-01-2000"  aria-required="true" class="full-width">
                    </div-->
					<div class="form-field">
                        <input name="dateNaissance" required type="text" id="dateNaissance" placeholder="Date de naissance (jj/mm/aaaa)" value="" class="full-width">
                    </div>
                    <div class="form-field">
                        <input name="numIdentite" required type="number" id="numIdentite" placeholder="C.N.I" value="" class="full-width">
                    </div>
					
					<!--div class="form-field">
                        <input name="mdp" type="password" required id="mdp" placeholder="Mot de passe" value="" class="full-width">
                    </div>
                    <div class="form-field">
                        <input name="mdp_conf" type="password" required id="mdp_conf" placeholder="Confirmez Mot de passe" value="" class="full-width">
                    </div-->
                    <div class="form-field">
                        <button class="full-width btn--primary">Recuperer mon compte</button>
                        <div class="submit-loader">
                            <div class="text-loader">Recuperation de compte encours...</div>
                            <div class="s-loader">
                                <div class="bounce1"></div>
                                <div class="bounce2"></div>
                                <div class="bounce3"></div>
                            </div>
                        </div>
                    </div>
    
                    </fieldset>

</html>
