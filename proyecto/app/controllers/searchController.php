<?php

	namespace app\controllers;
	use app\models\mainModel;

	class searchController extends mainModel{

		/*----------  Controlador modulos de busquedas  ----------*/
		public function modulosBusquedaControlador($modulo){

			$listaModulos=['userSearch', 'productSearch', 'customerSearch'];

			if(in_array($modulo, $listaModulos)){
				return false;
			}else{
				return true;
			}
		} /* esta funcion es para saber si se puede buscar realizar una busqueda con lo tenga la url, en este caso el modulo de la
		url debe de ser userSearch por que no hay mas pero en caso de ser mas se pueden agregar */


		/*----------  Controlador iniciar busqueda  ----------*/
		public function iniciarBuscadorControlador(){

		    $url=$this->limpiarCadena($_POST['modulo_url']);
			$texto=$this->limpiarCadena($_POST['txt_buscador']); // recibimos los datos de userSearch-view.php

			if($this->modulosBusquedaControlador($url)){ // verificar si el modulo de busqueda en la URL esta permitido
				$alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No podemos procesar la petición en este momento",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
			}

			if($texto==""){ // si texto esta vacio dara esta alerta de error
				$alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"Introduce un termino de busqueda",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
			}

			if($this->verificarDatos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{1,30}",$texto)){ // verificamos los datos del texto
				$alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"El termino de busqueda no coincide con el formato solicitado",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
			}

			$_SESSION[$url]=$texto; // cambiamos la url de la session por el texto

			$alerta=[
				"tipo"=>"redireccionar",
				"url"=>APP_URL.$url."/" // aqui le agregamos la url mas el nuevo texto
			];

			return json_encode($alerta);
		}


		/*----------  Controlador eliminar busqueda  ----------*/
		public function eliminarBuscadorControlador(){

			$url=$this->limpiarCadena($_POST['modulo_url']);

			if($this->modulosBusquedaControlador($url)){
				$alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No podemos procesar la petición en este momento",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
			}

			unset($_SESSION[$url]); // unset es para destruir la variable, no deja nada

			$alerta=[
				"tipo"=>"redireccionar",
				"url"=>APP_URL.$url."/" // por lo tanto el valor sera normal
			];

			return json_encode($alerta);
		}

	}