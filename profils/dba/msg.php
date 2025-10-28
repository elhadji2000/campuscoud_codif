<html>
<meta charset="UTF-8" />
<body>

<?php
//Estimation: 80 SMS/Minute


include('../../traitement/fonction.php');

$connexion = connexionBD(); 

//sms_attributaires('20230BM1I');

//sms_suppleants('20220B8LB') ;

sms_nv_attributaire('20240CYV6'); 
				
//sms_nv_suppleant('20210ASYL'); 

echo isIndivLitStudent('20200A5C8');
echo getPrixMensuelLit('20200A5C8');
echo getFacturation(true)['montant'];

?>
</html>