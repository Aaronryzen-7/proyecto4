<?php

namespace app\controllers; // se crea el namespace

use app\models\viewsModel; /* aqui estamos incluyendo el modelo que se relaciona con este controlador, se incluye en este archivo
utilizando use de los namespace */

class viewsControllers extends viewsModel{ // clase del namespace
    public function obtenerVistasControlador($vista){
        if($vista!=""){
            $respuesta = $this->obtenerVistasModelo($vista);
        }else{
            $respuesta = "login";
        }


        return $respuesta;
    }
}

?>