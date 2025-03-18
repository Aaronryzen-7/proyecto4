<?php
	
	require_once "../../config/app.php";
	require_once "../views/inc/session_start.php";
	require_once "../../autoload.php";
	
	use app\controllers\compraController;

	if(isset($_POST['ci_cliente'])){

		$insCliente = new compraController();

		if($_POST['ci_cliente']=="ci_cliente"){
			echo $insCliente->comprobarCedulaControlador();
		}

		if($_POST['ci_cliente']=="registrarProducto"){
			echo $insCliente->registrarProductoCompraControlador();
		}

		
		
	}else{
		session_destroy();
		header("Location: ".APP_URL."login/");
	}