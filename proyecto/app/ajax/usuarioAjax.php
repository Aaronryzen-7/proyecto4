<?php

require_once "../../config/app.php";
require_once "../views/inc/session_start.php";
require_once "../../autoload.php";


use app\controllers\userController;

/* en la vista de userNew hay un elemento html que se encuentra oculto pero tiene un name de modulo_usuario
todo eso es para saber aqui en este archivo si se envio o no, por eso se usa isset para comprobar el envio del formulario...
despues se comprueba si el modulo usuario tiene como valor registrar, de ser asi vamos a registrar el usuario con la clase 
registrarUsuarioControlador....

si modulo usuario no fue enviado, se destruye la sesion y se envia al login
*/
if(isset($_POST["modulo_usuario"])){

    $insUsuario = new userController();

    if($_POST["modulo_usuario"]=="registrar"){
        echo $insUsuario->registrarUsuarioControlador();
    }

    if($_POST["modulo_usuario"]=="eliminar"){
        echo $insUsuario->eliminarUsuarioControlador();
    }

    if($_POST['modulo_usuario']=="actualizar"){
        echo $insUsuario->actualizarUsuarioControlador();
    }

    if($_POST['modulo_usuario']=="eliminarFoto"){
        echo $insUsuario->eliminarFotoUsuarioControlador();
    }

    if($_POST['modulo_usuario']=="actualizarFoto"){
        echo $insUsuario->actualizarFotoUsuarioControlador();
    }
    


}else{
    session_destroy();
    header("Location: ".APP_URL."login/");
}

?>