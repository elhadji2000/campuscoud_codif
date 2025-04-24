<?php ?>
<script type="text/javascript">
	var theTime;

	// la fonction ne commence � marcher qu'apres le premier clic

	currentTime = new Date();
	theTime = currentTime.getTime();
	/////////////////////////////////////////////////

	document.onmousemove = stockTime;
	document.onkeydown = stockTime; //  pour prendre en compte les actions du clavier

	function stockTime() {
		currentTime = new Date();
		theTime = currentTime.getTime();
	}

	function verifTime() {
		currentTime = new Date();
		var timeNow = currentTime.getTime();
		if (timeNow - theTime > 600000) {           //300000==>5mn

			  alert('Votre session a expiré. Veuillez vous reconnecter!');
			 //   top.location.href="index"			   
			  top.location.href="https://campuscoud.com"			 

		}
	}
	window.setInterval("verifTime()", 300000);
</script>