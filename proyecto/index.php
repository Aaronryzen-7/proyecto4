<?php
require_once 'config/app.php';
require_once 'autoload.php';
require_once 'app/views/inc/session_start.php';

if(isset($_GET['views'])){ // para verificar si la url ya viene definida en el metodo get
    $url = explode("/",$_GET['views']); // si viene definida se va a separar ese string con /
} else{
    $url = ["login"]; // sino viene definida su valor sera login
}


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <?php
        require_once 'app/views/inc/head.php';
    ?>
</head>
<body>
    
    

    <?php
        use app\controllers\viewsControllers;
        use app\controllers\loginController; // llamamos el namesapce de este para utilizarlo durante el programa
        use app\controllers\productController;

        $insLogin = new loginController(); // aqui se crea su instancia
        $insListaCategoria = new productController();

        $viewsControllers = new viewsControllers();
        $vista = $viewsControllers->obtenerVistasControlador($url[0]);

        if($vista == "login" || $vista == "404"){
            require_once "app/views/content/".$vista."-view.php";
        }else{

            if(!isset($_SESSION['id']) || !isset($_SESSION['nombre']) || $_SESSION['id'] == "" || 
            $_SESSION['nombre'] == ""){
                $insLogin->cerrarSesionControlador();
                exit();
            }
            require_once "app/views/inc/navbar.php";
            require_once $vista;
        }

        require_once 'app/views/inc/script.php';
    ?>
</body>
</html>