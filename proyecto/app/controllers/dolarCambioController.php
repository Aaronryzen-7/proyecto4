<?php

	namespace app\controllers;
	use app\models\mainModel;

	class dolarCambioController extends mainModel{

        public function guardarCambioDolarControlador(){

            // limpiamos las cadenas de los datos de los usuarios
            $valorDolar = $this->limpiarCadena($_POST['valor']);
    
    
            // verificamos si uno de estos valores esta vacio de ser asi se mostrara un sweet alert
            if($valorDolar == ""){
    
                $alerta = [
                    "tipo"=>"simple",
                    "titulo"=>"Ocurrio un error inesperado",
                    "texto"=>"No has llenado el campo que es obligatorio",
                    "icono"=>"error"
                ];
                return json_encode($alerta);
                exit();
            }
    
    
    
            // verificando integridad de los datos
            if($this->verificarDatos("[0-9]+(\.[0-9]{1,2})?",$valorDolar)){
                $alerta = [
                    "tipo"=>"simple",
                    "titulo"=>"Ocurrio un error inesperado",
                    "texto"=>"El valor no coincide con el formato solicitado",
                    "icono"=>"error"
                ];
                return json_encode($alerta);
                exit();
            }
    
    
    
    
            $valor_cambio_reg = [
                [
                    "campo_nombre"=>"fecha",
                    "campo_marcador"=>":Fecha",
                    "campo_valor"=>date("Y-m-d H:i:s")
                ],
                [
                    "campo_nombre"=>"valor",
                    "campo_marcador"=>":Valor",
                    "campo_valor"=>$valorDolar
                ]
            ];
    
            $registrar_cambioDolar = $this->guardarDatos("dolar_cambio",$valor_cambio_reg);
    
    
            if($registrar_cambioDolar->rowCount()==1){ // funcion para ver si el usuario se registro
                $alerta = [
                    "tipo"=>"recargar",
                    "titulo"=>"Valor del dolar cambiado",
                    "texto"=>"El usuario ".$_SESSION['nombre']." "."cambio el valor del dolar con exito",
                    "icono"=>"success"
                ];
                $_SESSION['dolar']=$valorDolar;
            }else{ // entra en este sino se registro
                
    
                $alerta = [
                    "tipo"=>"simple",
                    "titulo"=>"Ocurrio un error inesperado",
                    "texto"=>"No se pudo cambiar el precio del dolar",
                    "icono"=>"error"
                ];
            }
            return json_encode($alerta);
    
        }
    }