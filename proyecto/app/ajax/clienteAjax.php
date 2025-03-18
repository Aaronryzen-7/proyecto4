<?php

require_once "../../config/app.php";
require_once "../views/inc/session_start.php";
require_once "../../autoload.php";


use app\controllers\customerController;


if(isset($_POST["modulo_cliente"])){

    $insCostumer = new customerController();

    if($_POST["modulo_cliente"]=="registrar"){
        echo $insCostumer->registrarCostumerControlador();
    }

    if($_POST["modulo_cliente"]=="eliminar"){
        echo $insCostumer->eliminarCostumerControlador();
    }

    if($_POST['modulo_cliente']=="actualizar"){
        echo $insCostumer->actualizarCostumerControlador();
    }

   

}else{
    session_destroy();
    header("Location: ".APP_URL."login/");
}

?>