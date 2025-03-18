<?php


// este es el controlador de usuarios

namespace app\controllers;
use app\models\mainModel; // usamos el mainModel

class customerController extends mainModel{
    // funcion para registrar usuarios y todo lo demas
    public function registrarCostumerControlador(){

        // limpiamos las cadenas de los datos de los usuarios
        $ci = $this->limpiarCadena($_POST['ci_cliente']);
        $nombre_cliente = $this->limpiarCadena($_POST['nombre_cliente']);
        $apellido_cliente = $this->limpiarCadena($_POST['apellido_cliente']);
        $direccion = $this->limpiarCadena($_POST['direccion_cliente']);
        $telefono = $this->limpiarCadena($_POST['telefono_cliente']);


        // verificamos si uno de estos valores esta vacio de ser asi se mostrara un sweet alert
        if($ci == "" || $nombre_cliente == "" || $apellido_cliente == "" || $direccion == "" || $telefono == ""){

            $alerta = [
                "tipo"=>"simple",
                "titulo"=>"Ocurrio un error inesperado",
                "texto"=>"No has llenado todos los campos que son obligatorios",
                "icono"=>"error"
            ];
            return json_encode($alerta);
            exit();
        }



        // verificando integridad de los datos
        if($this->verificarDatos("[0-9]{6,11}",$ci)){
            $alerta = [
                "tipo"=>"simple",
                "titulo"=>"Ocurrio un error inesperado",
                "texto"=>"La cedula de identidad no coincide con el formato solicitado",
                "icono"=>"error"
            ];
            return json_encode($alerta);
            exit();
        }

        if($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9 ]{3,40}",$nombre_cliente)){
            $alerta = [
                "tipo"=>"simple",
                "titulo"=>"Ocurrio un error inesperado",
                "texto"=>"El nombre del cliente no coincide con el formato solicitado",
                "icono"=>"error"
            ];
            return json_encode($alerta);
            exit();
        }

        

        if($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9 ]{3,40}",$apellido_cliente)){
            $alerta = [
                "tipo"=>"simple",
                "titulo"=>"Ocurrio un error inesperado",
                "texto"=>"El apellido del cliente no coincide con el formato solicitado",
                "icono"=>"error"
            ];
            return json_encode($alerta);
            exit();
        }
        
        if($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9 ]{3,100}",$direccion)){
            $alerta = [
                "tipo"=>"simple",
                "titulo"=>"Ocurrio un error inesperado",
                "texto"=>"La direccion del cliente no coincide con el formato solicitado",
                "icono"=>"error"
            ];
            return json_encode($alerta);
            exit();
        }

        if($this->verificarDatos("[0-9]{10,11}",$telefono)){
            $alerta = [
                "tipo"=>"simple",
                "titulo"=>"Ocurrio un error inesperado",
                "texto"=>"El telefono del cliente no coincide con el formato solicitado",
                "icono"=>"error"
            ];
            return json_encode($alerta);
            exit();
        }


        // verificar que el usuario sea unico en la base de datos
        $check_ci = $this->ejecutarConsulta("SELECT ci_cliente FROM clientes WHERE ci_cliente='$ci'");
        if($check_ci->rowCount()>=1){ // es para saber cuantos resultados envio la base de datos y fueron almacenados en check_usuario
            $alerta = [
                "tipo"=>"simple",
                "titulo"=>"Ocurrio un error inesperado",
                "texto"=>"La cedula del cliente ya se encuentra registrada",
                "icono"=>"error"
            ];
            return json_encode($alerta);
            exit();
        }

        

        $clientes_datos_reg = [
            [
                "campo_nombre"=>"ci_cliente",
                "campo_marcador"=>":CI",
                "campo_valor"=>$ci
            ],
            [
                "campo_nombre"=>"nombre_cliente",
                "campo_marcador"=>":NombreCliente",
                "campo_valor"=>$nombre_cliente
            ],
            [
                "campo_nombre"=>"apellido_cliente",
                "campo_marcador"=>":ApellidoCliente",
                "campo_valor"=>$apellido_cliente
            ],
            [
                "campo_nombre"=>"direccion_cliente",
                "campo_marcador"=>":Direccion",
                "campo_valor"=>$direccion
            ],
            [
                "campo_nombre"=>"telefono_cliente",
                "campo_marcador"=>":Telefono",
                "campo_valor"=>$telefono
            ]
        ];

        $registrar_cliente = $this->guardarDatos("clientes",$clientes_datos_reg);


        if($registrar_cliente->rowCount()==1){ // funcion para ver si el usuario se registro
            
            
            $alerta = [
                "tipo"=>"limpiar",
                "titulo"=>"Cliente registrado",
                "texto"=>"El cliente ".$nombre_cliente." "."se registro con exito",
                "icono"=>"success"
            ];
        }else{ // entra en este sino se registro
            

            $alerta = [
                "tipo"=>"simple",
                "titulo"=>"Ocurrio un error inesperado",
                "texto"=>"No se pudo registrar el cliente, por favor intente nuevamente",
                "icono"=>"error"
            ];
        }
        return json_encode($alerta);

    }

    public function listarCostumerControlador($pagina,$registros,$url,$busqueda){ /* pagina sera el numero total de paginas,
        registros sera cuantos registros se mostraran por pagina, url la direccion de la pagina y busqueda que utilizara si se hace una */

        $pagina=$this->limpiarCadena($pagina); // usamos el metodo para limpiar las cadenas de texto
        $registros=$this->limpiarCadena($registros);

        $url=$this->limpiarCadena($url);
        $url=APP_URL.$url."/"; // aqui hacemos que url almacene toda la url completa

        $busqueda=$this->limpiarCadena($busqueda);
        $tabla=""; // esta variable es la que devolvera la tabla como resultado final

        $pagina = (isset($pagina) && $pagina>0) ? (int) $pagina : 1; // si pagina esta definida y es mayor de 0 se mostrara su valor sino sera 1
        $inicio = ($pagina>0) ? (($pagina * $registros)-$registros) : 0; // si pagina es mayor de 0 realizara la operacion y dara ese valor pero sino sera 0 el valor
        // ademas inicio es para saber de que numero vamos a empezar a contar dependiendo de la pagina... ejemplo de la operacion (2*15=30)-15=15

        if(isset($busqueda) && $busqueda!=""){/* ahora definiremos el sql para la consulta de los usuarios
            entonces en este if comprobamos si se esta usando la busqueda de ser asi se definiran dos consultas sql en las cuales
            se utiliza como parametro la busqueda.... y sino se esta usando la busqueda en el else se utilizara dos consultas sql
            que devolveran toda la lista de los usuario excepto el que esta con la sesion activa */

            $consulta_datos="SELECT * FROM clientes WHERE nombre_cliente LIKE '%$busqueda%' OR apellido_cliente LIKE '%$busqueda%' OR ci_cliente LIKE '%$busqueda%' ORDER BY nombre_cliente ASC LIMIT $inicio,$registros";
            /* a traves de condiciones y la funcion LIKE se pide que devuelva los datos de los usuarios que sean los mas compatible 
            posible con el buscador */

            $consulta_total="SELECT COUNT(ci_cliente) FROM clientes WHERE nombre_cliente LIKE '%$busqueda%' OR apellido_cliente LIKE '%$busqueda%' OR ci_cliente LIKE '%$busqueda%'";
            /* igual que aqui se usa LIKE y COUNT para obtener el numero total de id que aparecen desoues que se utilizo
            el buscador */

        }else{

            $consulta_datos="SELECT * FROM clientes ORDER BY nombre_cliente ASC LIMIT $inicio,$registros";
            /* es para consultar todos los datos de los usuarios, el que dice id=1 es el administrador administrador, se pide que se
            muestre de forma ordenada por nombre y en limit se coloca a partir de que registro se va a empezar a mostrar, es decir,
            dice que se empiecen a contar de $inicio digamos que es 14 y que a partir de ahi se cuente 15 que es $registro
            es decir se mostrara los dato de los usuario del 14 al 29 */

            $consulta_total="SELECT COUNT(ci_cliente) FROM clientes";
            /* este sql es para contar el total de registros que hay en la base de datos, es decir, si son 32 devolvera 29 ya que no se
            cuenta el que tiene la sesion activa y tampoco el administrador principal y que ademas se empieza a contar de 0*/

        }

        $datos = $this->ejecutarConsulta($consulta_datos); // utilizar la funcion para ejecutar la consulta y devolver el valor
        $datos = $datos->fetchAll(); // guardar ese valor devuelto por la base de datos como array

        $total = $this->ejecutarConsulta($consulta_total); // ejecutar consulta sql
        $total = (int) $total->fetchColumn(); // con fetchColumn devolvera solo la primera columna ya que solo es una que da el total de registro y se tranformo en int para poder usarla mejor

        $numeroPaginas =ceil($total/$registros);/* asi sabemos el numero de paginas en total ejemplo
        con el valor devuelto de total digamos que devolvio 40 y queremos 15 usuarios por pagina entonces
        la operacion 40/15=2,66 y con ceil se redondea al numero mas cercano que es 3... entonces en total habran 3 paginas
        que tendram 15 usuarios la primera, 15 usuarios la segunda y 10 usuarios la tercera*/

        $tabla.='
            <div class="table-container">
            <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
                <thead>
                    <tr>
                        <th class="has-text-centered">#</th>
                        <th class="has-text-centered">CI</th>
                        <th class="has-text-centered">Nombre</th>
                        <th class="has-text-centered">Apellido</th>
                        <th class="has-text-centered">Direccion</th>
                        <th class="has-text-centered">Telefono</th>
                        <th class="has-text-centered" colspan="2">Opciones</th>
                    </tr>
                </thead>
                <tbody>
        '; // se le agrega este codigo html a la tabla que se mostrara en el view

        if($total>=1 && $pagina<=$numeroPaginas){
            $contador=$inicio+1; // a partir de que registro va a contar el +1 es por que se empieza a contar desde 0
            $pag_inicio=$inicio+1;
            foreach($datos as $rows){ // para recorrer e ir agregando todo los datos del array de los usuarios
                $tabla.='
                    <tr class="has-text-centered" >
                        <td>'.$contador.'</td>
                        <td>'.$rows['ci_cliente'].'</td>
                        <td>'.$rows['nombre_cliente'].'</td>
                        <td>'.$rows['apellido_cliente'].'</td>
                        <td>'.$rows['direccion_cliente'].'</td>
                        <td>'.$rows['telefono_cliente'].'</td>
                        <td>
                            <a href="'.APP_URL.'customerUpdate/'.$rows['ci_cliente'].'/" class="button is-success is-rounded is-small">Actualizar</a>
                        </td>
                        <td>
                            <form class="FormularioAjax" action="'.APP_URL.'app/ajax/clienteAjax.php" method="POST" autocomplete="off" >

                                <input type="hidden" name="modulo_cliente" value="eliminar">
                                <input type="hidden" name="ci_cliente" value="'.$rows['ci_cliente'].'">

                                <button type="submit" class="button is-danger is-rounded is-small">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                ';// el date es para darle un nuevo formato a la fecha y hora y el strtotime es para pasar la fecha y hora de string a formato time
                $contador++; // para ir al siguiente registro
            }
            $pag_final=$contador-1;

        }else{ // se ejecuta este else si hay un error con el total o el numero de paginas
            if($total>=1){ // con este if cuando se muestre para recargar la pagina y se le de te enviara a la pagina 1 de la lista de usuarios
                $tabla.='
                    <tr class="has-text-centered" >
                        <td colspan="7">
                            <a href="'.$url.'1/" class="button is-link is-rounded is-small mt-4 mb-4">
                                Haga clic acá para recargar el listado
                            </a>
                        </td>
                    </tr>
                ';
            }else{ // con esta condicion doble podemos averiguar si fuen un error en la carga o es que no hay registro
                $tabla.='
                    <tr class="has-text-centered" >
                        <td colspan="7">
                            No hay registros en el sistema
                        </td>
                    </tr>
                ';
            }
        }

        $tabla.='</tbody></table></div>'; // se le agrega mas codigo html

        ### Paginacion ###
        if($total>0 && $pagina<=$numeroPaginas){
            $tabla.='<p class="has-text-right">Mostrando usuarios <strong>'.$pag_inicio.'</strong> al <strong>'.$pag_final.'</strong> de un <strong>total de '.$total.'</strong></p>';

            $tabla.=$this->paginadorTablas($pagina,$numeroPaginas,$url,7); // aqui ejecutamos la funcion del modelo principal con los datos recogidos aqui
        }

        return $tabla; // retornamos el valor de la tabla
    }

    public function eliminarCostumerControlador(){
        $id=$this->limpiarCadena($_POST['ci_cliente']); // por seguridad limpiar el texto id

			

			# Verificando usuario #
		    $datos=$this->ejecutarConsulta("SELECT * FROM clientes WHERE ci_cliente='$id'"); // consulta sql para que devuelva los datos del usuario que queremos eliminar
		    if($datos->rowCount()<=0){ // verificar si se devolvio algun resultado de ser 0, quiere decir que no existe un usuario con ese id
		        $alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No hemos encontrado el producto en el sistema",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
		    }else{
		    	$datos=$datos->fetch(); // pero si existe, sus datos retornados se guardaran en un array llamado $datos
		    }

            
		    $eliminarCliente=$this->eliminarRegistro("clientes","ci_cliente",$id); // ejecutar la consulta para eliminar el usuario

		    if($eliminarCliente->rowCount()==1){ // verificar si el usuario fue eliminado

		    	

		        $alerta=[
					"tipo"=>"recargar",
					"titulo"=>"Cliente eliminado",
					"texto"=>"El cliente " .$datos['nombre_cliente'] ." ". "ha sido eliminado del sistema correctamente",
					"icono"=>"success"
				]; // mensaje y recargar la lista despues de ser eliminado

		    }else{

		    	$alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No hemos podido eliminar el cliente ".$datos['nombre_cliente'] ." ". "del sistema, por favor intente nuevamente",
					"icono"=>"error"
				];
		    } // si el usuario no fue eliminado lanzara este error

		    return json_encode($alerta);
    }

    // controlador para actualizar usuarios
    public function actualizarCostumerControlador(){

        $id=$this->limpiarCadena($_POST['ci']);

        # Verificando usuario #
        $datos=$this->ejecutarConsulta("SELECT * FROM clientes WHERE ci_cliente='$id'");
        if($datos->rowCount()<=0){
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"No hemos encontrado el producto en el sistema",
                "icono"=>"error"
            ];
            return json_encode($alerta);
            exit();
        }else{
            $datos=$datos->fetch();
        }

        $admin_usuario=$this->limpiarCadena($_POST['administrador_usuario']);
        $admin_clave=$this->limpiarCadena($_POST['administrador_clave']);

        # Verificando campos obligatorios admin #
        if($admin_usuario=="" || $admin_clave==""){
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"No ha llenado todos los campos que son obligatorios, que corresponden a su USUARIO y CLAVE",
                "icono"=>"error"
            ];
            return json_encode($alerta);
            exit();
        }

        if($this->verificarDatos("[a-zA-Z0-9]{4,20}",$admin_usuario)){
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"Su USUARIO no coincide con el formato solicitado",
                "icono"=>"error"
            ];
            return json_encode($alerta);
            exit();
        }

        if($this->verificarDatos("[a-zA-Z0-9$@.-]{7,100}",$admin_clave)){
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"Su CLAVE no coincide con el formato solicitado",
                "icono"=>"error"
            ];
            return json_encode($alerta);
            exit();
        }

        # Verificando administrador #
        $check_admin=$this->ejecutarConsulta("SELECT * FROM usuarios WHERE nombre_usuario='$admin_usuario' AND id_usuario='".$_SESSION['id']."'");
        if($check_admin->rowCount()==1){

            $check_admin=$check_admin->fetch();

            if($check_admin['nombre_usuario']!=$admin_usuario || !password_verify($admin_clave,$check_admin['contrasena'])){

                $alerta=[
                    "tipo"=>"simple",
                    "titulo"=>"Ocurrió un error inesperado",
                    "texto"=>"USUARIO o CLAVE de administrador incorrectos",
                    "icono"=>"error"
                ];
                return json_encode($alerta);
                exit();
            }
        }else{
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"USUARIO o CLAVE de administrador incorrectos",
                "icono"=>"error"
            ];
            return json_encode($alerta);
            exit();
        }


        # Almacenando datos#
        $ci = $this->limpiarCadena($_POST['ci_cliente']);
        $nombre_cliente = $this->limpiarCadena($_POST['nombre_cliente']);
        $apellido_cliente = $this->limpiarCadena($_POST['apellido_cliente']);
        $direccion = $this->limpiarCadena($_POST['direccion_cliente']);
        $telefono = $this->limpiarCadena($_POST['telefono_cliente']);


        // verificamos si uno de estos valores esta vacio de ser asi se mostrara un sweet alert
        if($ci == "" || $nombre_cliente == "" || $apellido_cliente == "" || $direccion == "" || $telefono == ""){

            $alerta = [
                "tipo"=>"simple",
                "titulo"=>"Ocurrio un error inesperado",
                "texto"=>"No has llenado todos los campos que son obligatorios",
                "icono"=>"error"
            ];
            return json_encode($alerta);
            exit();
        }



        // verificando integridad de los datos
        if($this->verificarDatos("[0-9]{6,11}",$ci)){
            $alerta = [
                "tipo"=>"simple",
                "titulo"=>"Ocurrio un error inesperado",
                "texto"=>"La cedula de identidad no coincide con el formato solicitado",
                "icono"=>"error"
            ];
            return json_encode($alerta);
            exit();
        }

        if($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9 ]{3,40}",$nombre_cliente)){
            $alerta = [
                "tipo"=>"simple",
                "titulo"=>"Ocurrio un error inesperado",
                "texto"=>"El nombre del cliente no coincide con el formato solicitado",
                "icono"=>"error"
            ];
            return json_encode($alerta);
            exit();
        }

        

        if($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9 ]{3,40}",$apellido_cliente)){
            $alerta = [
                "tipo"=>"simple",
                "titulo"=>"Ocurrio un error inesperado",
                "texto"=>"El apellido del cliente no coincide con el formato solicitado",
                "icono"=>"error"
            ];
            return json_encode($alerta);
            exit();
        }
        
        if($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9 ]{3,100}",$direccion)){
            $alerta = [
                "tipo"=>"simple",
                "titulo"=>"Ocurrio un error inesperado",
                "texto"=>"La direccion del cliente no coincide con el formato solicitado",
                "icono"=>"error"
            ];
            return json_encode($alerta);
            exit();
        }

        if($this->verificarDatos("[0-9]{10,11}",$telefono)){
            $alerta = [
                "tipo"=>"simple",
                "titulo"=>"Ocurrio un error inesperado",
                "texto"=>"El telefono del cliente no coincide con el formato solicitado",
                "icono"=>"error"
            ];
            return json_encode($alerta);
            exit();
        }


        
        if($datos['ci_cliente']!=$ci){
            $check_ci=$this->ejecutarConsulta("SELECT ci_cliente FROM clientes WHERE ci_cliente='$ci'");
            if($check_ci->rowCount()>0){
                $alerta=[
                    "tipo"=>"simple",
                    "titulo"=>"Ocurrió un error inesperado",
                    "texto"=>"La cedula del cliente ya se encuentra registrada",
                    "icono"=>"error"
                ];
                return json_encode($alerta);
                exit();
            }
        }


        

        $clientes_datos_up=[
            [
                "campo_nombre"=>"ci_cliente",
                "campo_marcador"=>":CI",
                "campo_valor"=>$ci
            ],
            [
                "campo_nombre"=>"nombre_cliente",
                "campo_marcador"=>":NombreCliente",
                "campo_valor"=>$nombre_cliente
            ],
            [
                "campo_nombre"=>"apellido_cliente",
                "campo_marcador"=>":ApellidoCliente",
                "campo_valor"=>$apellido_cliente
            ],
            [
                "campo_nombre"=>"direccion_cliente",
                "campo_marcador"=>":Direccion",
                "campo_valor"=>$direccion
            ],
            [
                "campo_nombre"=>"telefono_cliente",
                "campo_marcador"=>":Telefono",
                "campo_valor"=>$telefono
            ]
        ];

       

        $condicion=[
            "condicion_campo"=>"ci_cliente",
            "condicion_marcador"=>":ID",
            "condicion_valor"=>$id
        ];

        if($this->actualizarDatos("clientes",$clientes_datos_up,$condicion)){

            

            $alerta=[
                "tipo"=>"recargar",
                "titulo"=>"Cliente actualizado",
                "texto"=>"Los datos del cliente ".$datos['nombre_cliente']." se actualizaron correctamente",
                "icono"=>"success"
            ];
        }else{
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"No hemos podido actualizar los datos del cliente ".$datos['nombre_cliente'].", por favor intente nuevamente",
                "icono"=>"error"
            ];
        }

        return json_encode($alerta);
    }

}