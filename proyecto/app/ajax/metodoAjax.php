<?php

require_once "../../config/app.php";
require_once "../views/inc/session_start.php";
require_once "../../autoload.php";


use app\controllers\methodController;


if(isset($_POST["modulo_metodo"])){

    $insMetodo = new methodController();

    if($_POST["modulo_metodo"]=="registrar"){
        echo $insMetodo->registrarMetodoControlador();
    }

    if($_POST['modulo_metodo']=="actualizar"){
        echo $insMetodo->actualizarMetodoControlador();
    }

    if($_POST["modulo_metodo"]=="eliminar"){
        echo $insMetodo->eliminarMetodoControlador();
    }

   
    


}else{
    session_destroy();
    header("Location: ".APP_URL."login/");
}

?>