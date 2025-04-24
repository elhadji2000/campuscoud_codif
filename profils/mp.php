<?php session_start();  ?>  
<html lang="en">

<?php 

include('../activite.php');
include('head.html');
include('../head2.php');
 
?>

	<!--section id="homedesigne" class="s-homedesigne">
        
       <p class="lead">Bienvenue dans l'espace changement de mot de passe !</p>
    </section--> <!-- end s-stats -->
	
  <section id="styles" class="s-styles">

	  
	  
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
        <strong>VEUILLEZ SAISIR VOS MOTS DE PASSE ACTUEL ET NOUVEAU</strong>
    </center>
    </td>
</tr>

 <fieldset>
    
                   <div class="form-field">
                        <input name="mdp" type="password" required id="mdp" placeholder="Actuel Mot de passe" value="" class="full-width">
                    </div>
                    <div class="form-field">
                        <input name="mdp_new" type="password" required id="mdp_new" placeholder="Nouveau Mot de passe" value="" class="full-width">
                    </div>
                    <div class="form-field">
                        <input name="mdp_conf" type="password" required id="mdp_conf" placeholder="Confirmez Nouveau Mot de passe" value="" class="full-width">
                    </div>
					
                    <div class="form-field">
                        <button class="full-width btn--primary">Changer mon mot de passe</button>
                        <div class="submit-loader">
                            <div class="text-loader"></div>
                            <div class="s-loader">
                                <div class="bounce1"></div>
                                <div class="bounce2"></div>
                                <div class="bounce3"></div>
                            </div>
                        </div>
                    </div>
    
                    </fieldset>

</html>
