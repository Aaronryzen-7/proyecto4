<?php
	
	require_once "../../config/app.php";
	require_once "../views/inc/session_start.php";
	require_once "../../autoload.php";
	
	use app\controllers\dolarCambioController;

	if(isset($_POST['cambio_dolar'])){

		$insCambioDolar = new dolarCambioController();

		if($_POST['cambio_dolar']=="cambio_dolar"){
			echo $insCambioDolar->guardarCambioDolarControlador();
		}

		
		
	}else{
		session_destroy();
		header("Location: ".APP_URL."login/");
	}