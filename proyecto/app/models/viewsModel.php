<?php

namespace app\models;

class viewsModel{
    protected function obtenerVistasModelo($vista){
        $listaBlanca = ["dashboard", "userNew", "userList", "userSearch", "userUpdate", "userPhoto", 
        "logOut", "dolarCambio", "categoryNew", "categoryList", "categoryUpdate", 
        "productNew", "productList", "productSearch", "productUpdate", "productPhoto",
        "customerNew", "customerList", "customerUpdate", "customerSearch",
        "methodNew", "methodUpdate", "methodList",
        "ciCompraNew", "buysNew", "compraController"];

        if(in_array($vista,$listaBlanca)){ // la funcion in_array es para saber si el valor de $vista esta o existe en el array $listaBlanca
            if(is_file("app/views/content/".$vista."-view.php")){ // devuelve true si el archivo existe dentro de esa ubicacion
                $contenido = "app/views/content/".$vista."-view.php"; // si el archivo existe en dicha direccion, se crea esta variable contenido y se le agrega la direccion
            }else{
                $contenido = "404"; // sino existe se le agrega 404, lo cual sera la pagina de error
            }
        }elseif($vista == "login" || $vista == "index"){
            $contenido = "login"; // si vista es igual a uno de los de arriba su valor sera login
        }else{
            $contenido = "404"; // sino es ninguna de las anteriores por lo tanto la pagina no existe
        }


        return $contenido;
    }
}


?>