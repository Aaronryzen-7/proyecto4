<?php


// este es el controlador de usuarios

namespace app\controllers;
use app\models\mainModel; // usamos el mainModel

class userController extends mainModel{
    // funcion para registrar usuarios y todo lo demas
    public function registrarUsuarioControlador(){

        // limpiamos las cadenas de los datos de los usuarios
        $nombre = $this->limpiarCadena($_POST['nombre_usuario']);
        $email = $this->limpiarCadena($_POST['email']);
        $clave1 = $this->limpiarCadena($_POST['usuario_clave_1']);
        $clave2 = $this->limpiarCadena($_POST['usuario_clave_2']);
        $usuario_permisos = $this->limpiarCadena($_POST['usuario_permisos']);


        // verificamos si uno de estos valores esta vacio de ser asi se mostrara un sweet alert
        if($nombre == "" || $clave1 == "" || $clave2 == "" || $usuario_permisos == ""){

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
        if($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9 ]{3,40}",$nombre)){
            $alerta = [
                "tipo"=>"simple",
                "titulo"=>"Ocurrio un error inesperado",
                "texto"=>"El nombre no coincide con el formato solicitado",
                "icono"=>"error"
            ];
            return json_encode($alerta);
            exit();
        }

        

        if($this->verificarDatos("[a-zA-Z0-9$@.-]{7,100}",$clave1) || $this->verificarDatos("[a-zA-Z0-9$@.-]{7,100}",$clave2)){
            $alerta = [
                "tipo"=>"simple",
                "titulo"=>"Ocurrio un error inesperado",
                "texto"=>"Las CLAVES no coincide con el formato solicitado",
                "icono"=>"error"
            ];
            return json_encode($alerta);
            exit();
        }
        /*
        if($this->verificarDatos("[1-3]",$usuario_permisos)){
            $alerta = [
                "tipo"=>"simple",
                "titulo"=>"Ocurrio un error inesperado",
                "texto"=>"El Permiso asignado no cumple con el formato solicitado",
                "icono"=>"error"
            ];
            return json_encode($alerta);
            exit();
        }*/



        // verificar email
        if($email != ""){
            if(filter_var($email,FILTER_VALIDATE_EMAIL)){
                // verificar si el correo existe o ya esta guardado en la base de datos sino para guardarlo
                $check_email = $this->ejecutarConsulta("SELECT email FROM usuarios WHERE email='$email'");

                if($check_email->rowCount()>0){ // es para saber cuantos resultados envio la base de datos y fueron almacenados en check_email
                    $alerta = [
                        "tipo"=>"simple",
                        "titulo"=>"Ocurrio un error inesperado",
                        "texto"=>"El EMAIL ingresado ya se encuentra registrado",
                        "icono"=>"error"
                    ];
                    return json_encode($alerta);
                    exit();
                }
            }else{
                $alerta = [
                    "tipo"=>"simple",
                    "titulo"=>"Ocurrio un error inesperado",
                    "texto"=>"El EMAIL no coincide con el formato solicitado",
                    "icono"=>"error"
                ];
                return json_encode($alerta);
                exit();
            }
        }



        // verificar que las claves conincidan
        if($clave1 != $clave2){
            $alerta = [
                "tipo"=>"simple",
                "titulo"=>"Ocurrio un error inesperado",
                "texto"=>"Las CLAVES no coinciden",
                "icono"=>"error"
            ];
            return json_encode($alerta);
            exit();
        }else{
            // ya sabemos que las claves coinciden, entonces la encriptamos utilizando cualquiera de las dos ya que son las mismas
            $clave = password_hash($clave1, PASSWORD_BCRYPT, ["cost"=>10]); // para encriptar la clave
        }


        // verificar que el usuario sea unico en la base de datos
        $check_usuario = $this->ejecutarConsulta("SELECT nombre_usuario FROM usuarios WHERE nombre_usuario='$nombre'");
        if($check_usuario->rowCount()>0){ // es para saber cuantos resultados envio la base de datos y fueron almacenados en check_usuario
            $alerta = [
                "tipo"=>"simple",
                "titulo"=>"Ocurrio un error inesperado",
                "texto"=>"El USUARIO ingresado ya se encuentra registrado",
                "icono"=>"error"
            ];
            return json_encode($alerta);
            exit();
        }



        $usuario_datos_reg = [
            [
                "campo_nombre"=>"nombre_usuario",
                "campo_marcador"=>":Nombre",
                "campo_valor"=>$nombre
            ],
            [
                "campo_nombre"=>"email",
                "campo_marcador"=>":Email",
                "campo_valor"=>$email
            ],
            [
                "campo_nombre"=>"contrasena",
                "campo_marcador"=>":Clave",
                "campo_valor"=>$clave
            ]
        ];

        $registrar_usuario = $this->guardarDatos("usuarios",$usuario_datos_reg);


        if($registrar_usuario->rowCount()==1){ // funcion para ver si el usuario se registro
            $consulta = "
                BEGIN;
                SET @last_id_usuario = (SELECT MAX(id_usuario) FROM usuarios);
                INSERT INTO usuario_permisos (id_usuario, id_permiso) VALUES (@last_id_usuario, '$usuario_permisos');
                COMMIT;
            ";
            $this->ejecutarConsulta($consulta);
            
            $alerta = [
                "tipo"=>"limpiar",
                "titulo"=>"Usuario registrado",
                "texto"=>"El usuario ".$nombre." "."se registro con exito",
                "icono"=>"success"
            ];
        }else{ // entra en este sino se registro
            

            $alerta = [
                "tipo"=>"simple",
                "titulo"=>"Ocurrio un error inesperado",
                "texto"=>"No se pudo registrar el usuario, por favor intente nuevamente",
                "icono"=>"error"
            ];
        }
        return json_encode($alerta);

    }

    /*----------  Controlador listar usuario  ----------*/
		public function listarUsuarioControlador($pagina,$registros,$url,$busqueda){ /* pagina sera el numero total de paginas,
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

				$consulta_datos="SELECT * FROM usuarios WHERE ((id_usuario!='".$_SESSION['id']."' AND id_usuario!='2') AND (nombre_usuario LIKE '%$busqueda%' OR email LIKE '%$busqueda%')) ORDER BY nombre_usuario ASC LIMIT $inicio,$registros";
                /* a traves de condiciones y la funcion LIKE se pide que devuelva los datos de los usuarios que sean los mas compatible 
                posible con el buscador */

				$consulta_total="SELECT COUNT(id_usuario) FROM usuarios WHERE ((id_usuario!='".$_SESSION['id']."' AND id_usuario!='2') AND (nombre_usuario LIKE '%$busqueda%' OR email LIKE '%$busqueda%'))";
                /* igual que aqui se usa LIKE y COUNT para obtener el numero total de id que aparecen desoues que se utilizo
                el buscador */

			}else{

				$consulta_datos="SELECT * FROM usuarios WHERE id_usuario!='".$_SESSION['id']."' AND id_usuario!='2' ORDER BY nombre_usuario ASC LIMIT $inicio,$registros";
                /* es para consultar todos los datos de los usuarios, el que dice id=1 es el administrador administrador, se pide que se
                muestre de forma ordenada por nombre y en limit se coloca a partir de que registro se va a empezar a mostrar, es decir,
                dice que se empiecen a contar de $inicio digamos que es 14 y que a partir de ahi se cuente 15 que es $registro
                es decir se mostrara los dato de los usuario del 14 al 29 */

				$consulta_total="SELECT COUNT(id_usuario) FROM usuarios WHERE id_usuario!='".$_SESSION['id']."' AND id_usuario!='2'";
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
		                    <th class="has-text-centered">Nombre</th>
		                    <th class="has-text-centered">Email</th>
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
							<td>'.$rows['nombre_usuario'].'</td>
							<td>'.$rows['email'].'</td>
							
			                <td>
			                    <a href="'.APP_URL.'userUpdate/'.$rows['id_usuario'].'/" class="button is-success is-rounded is-small">Actualizar</a>
			                </td>
			                <td>
			                	<form class="FormularioAjax" action="'.APP_URL.'app/ajax/usuarioAjax.php" method="POST" autocomplete="off" >

			                		<input type="hidden" name="modulo_usuario" value="eliminar">
			                		<input type="hidden" name="id_usuario" value="'.$rows['id_usuario'].'">

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
    
    

    // controlador eliminar usuarios
    public function eliminarUsuarioControlador(){
        $id=$this->limpiarCadena($_POST['id_usuario']); // por seguridad limpiar el texto id

			if($id==2){ // en este caso el id 2 es el admin principal de ser asi no se puede eliminar y lanzara el error
				$alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No podemos eliminar el usuario principal del sistema",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
			}

			# Verificando usuario #
		    $datos=$this->ejecutarConsulta("SELECT * FROM usuarios WHERE id_usuario='$id'"); // consulta sql para que devuelva los datos del usuario que queremos eliminar
		    if($datos->rowCount()<=0){ // verificar si se devolvio algun resultado de ser 0, quiere decir que no existe un usuario con ese id
		        $alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No hemos encontrado el usuario en el sistema",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
		    }else{
		    	$datos=$datos->fetch(); // pero si existe, sus datos retornados se guardaran en un array llamado $datos
		    }

            $eliminarPermisoUsuario=$this->eliminarRegistro("usuario_permisos","id_usuario",$id);
		    $eliminarUsuario=$this->eliminarRegistro("usuarios","id_usuario",$id); // ejecutar la consulta para eliminar el usuario

		    if($eliminarUsuario->rowCount()==1 AND $eliminarPermisoUsuario->rowCount()==1){ // verificar si el usuario fue eliminado

		    	

		        $alerta=[
					"tipo"=>"recargar",
					"titulo"=>"Usuario eliminado",
					"texto"=>"El usuario " .$datos['nombre_usuario'] ." ". "ha sido eliminado del sistema correctamente",
					"icono"=>"success"
				]; // mensaje y recargar la lista despues de ser eliminado

		    }else{

		    	$alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No hemos podido eliminar el usuario ".$datos['nombre_usuario'] ." ". "del sistema, por favor intente nuevamente",
					"icono"=>"error"
				];
		    } // si el usuario no fue eliminado lanzara este error

		    return json_encode($alerta);
    }

    // controlador para actualizar usuarios
    public function actualizarUsuarioControlador(){

        $id=$this->limpiarCadena($_POST['id_usuario']);

        # Verificando usuario #
        $datos=$this->ejecutarConsulta("SELECT * FROM usuarios WHERE id_usuario='$id'");
        if($datos->rowCount()<=0){
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"No hemos encontrado el usuario en el sistema",
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
        $nombre=$this->limpiarCadena($_POST['nombre_usuario']);
        $email=$this->limpiarCadena($_POST['email']);
        $clave1=$this->limpiarCadena($_POST['usuario_clave_1']);
        $clave2=$this->limpiarCadena($_POST['usuario_clave_2']);
        $usuario_permisos=$this->limpiarCadena($_POST['usuario_permisos']);

        # Verificando campos obligatorios #
        if($nombre==""){
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"No has llenado todos los campos que son obligatorios",
                "icono"=>"error"
            ];
            return json_encode($alerta);
            exit();
        }


        # Verificando integridad de los datos #
        if($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9 ]{3,40}",$nombre)){
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"El NOMBRE no coincide con el formato solicitado",
                "icono"=>"error"
            ];
            return json_encode($alerta);
            exit();
        }
        if($this->verificarDatos("[0-3]",$usuario_permisos)){
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"El Permiso del usuario no coincide con el formato solicitado",
                "icono"=>"error"
            ];
            return json_encode($alerta);
            exit();
        }

        

        # Verificando email #
        if($email!="" && $datos['email']!=$email){
            if(filter_var($email, FILTER_VALIDATE_EMAIL)){
                $check_email=$this->ejecutarConsulta("SELECT email FROM usuarios WHERE email='$email'");
                if($check_email->rowCount()>0){
                    $alerta=[
                        "tipo"=>"simple",
                        "titulo"=>"Ocurrió un error inesperado",
                        "texto"=>"El EMAIL que acaba de ingresar ya se encuentra registrado en el sistema, por favor verifique e intente nuevamente",
                        "icono"=>"error"
                    ];
                    return json_encode($alerta);
                    exit();
                }
            }else{
                $alerta=[
                    "tipo"=>"simple",
                    "titulo"=>"Ocurrió un error inesperado",
                    "texto"=>"Ha ingresado un correo electrónico no valido",
                    "icono"=>"error"
                ];
                return json_encode($alerta);
                exit();
            }
        }

        # Verificando claves #
        if($clave1!="" || $clave2!=""){
            if($this->verificarDatos("[a-zA-Z0-9$@.-]{7,100}",$clave1) || $this->verificarDatos("[a-zA-Z0-9$@.-]{7,100}",$clave2)){

                $alerta=[
                    "tipo"=>"simple",
                    "titulo"=>"Ocurrió un error inesperado",
                    "texto"=>"Las CLAVES no coinciden con el formato solicitado",
                    "icono"=>"error"
                ];
                return json_encode($alerta);
                exit();
            }else{
                if($clave1!=$clave2){

                    $alerta=[
                        "tipo"=>"simple",
                        "titulo"=>"Ocurrió un error inesperado",
                        "texto"=>"Las nuevas CLAVES que acaba de ingresar no coinciden, por favor verifique e intente nuevamente",
                        "icono"=>"error"
                    ];
                    return json_encode($alerta);
                    exit();
                }else{
                    $clave=password_hash($clave1,PASSWORD_BCRYPT,["cost"=>10]);
                }
            }
        }else{
            $clave=$datos['contrasena'];
        }

        # Verificando usuario #
        if($datos['nombre_usuario']!=$nombre){
            $check_usuario=$this->ejecutarConsulta("SELECT nombre_usuario FROM usuarios WHERE nombre_usuario='$nombre'");
            if($check_usuario->rowCount()>0){
                $alerta=[
                    "tipo"=>"simple",
                    "titulo"=>"Ocurrió un error inesperado",
                    "texto"=>"El USUARIO ingresado ya se encuentra registrado, por favor elija otro",
                    "icono"=>"error"
                ];
                return json_encode($alerta);
                exit();
            }
        }

        if($usuario_permisos=="0"){
            $check_permisos=$this->seleccionarDatos("Unico","usuario_permisos","id_usuario",$id);
            if($check_permisos->rowCount()==1){
                $check_permisos=$check_permisos->fetch();
                $permisoNuevo=$check_permisos['id_permiso'];
            }else{
                $alerta=[
                    "tipo"=>"simple",
                    "titulo"=>"Ocurrió un error inesperado",
                    "texto"=>"Error al asignar los permisos",
                    "icono"=>"error"
                ];
                return json_encode($alerta);
                exit();
            }
        }else{
            $permisoNuevo=$usuario_permisos;
        }

        $usuario_datos_up=[
            [
                "campo_nombre"=>"nombre_usuario",
                "campo_marcador"=>":Nombre",
                "campo_valor"=>$nombre
            ],
            [
                "campo_nombre"=>"email",
                "campo_marcador"=>":Email",
                "campo_valor"=>$email
            ],
            [
                "campo_nombre"=>"contrasena",
                "campo_marcador"=>":Clave",
                "campo_valor"=>$clave
            ]
        ];

        $usuario_permisos_datos_up=[
            [
                "campo_nombre"=>"id_usuario",
                "campo_marcador"=>":ID",
                "campo_valor"=>$id
            ],
            [
                "campo_nombre"=>"id_permiso",
                "campo_marcador"=>":Permiso",
                "campo_valor"=>$permisoNuevo
            ]
        ];

        $condicion=[
            "condicion_campo"=>"id_usuario",
            "condicion_marcador"=>":ID",
            "condicion_valor"=>$id
        ];

        if($this->actualizarDatos("usuarios",$usuario_datos_up,$condicion) AND $this->actualizarDatos("usuario_permisos",$usuario_permisos_datos_up,$condicion)){

            if($id==$_SESSION['id']){
                $_SESSION['nombre']=$nombre;
            }

            $alerta=[
                "tipo"=>"recargar",
                "titulo"=>"Usuario actualizado",
                "texto"=>"Los datos del usuario ".$datos['nombre_usuario']." se actualizaron correctamente",
                "icono"=>"success"
            ];
        }else{
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"No hemos podido actualizar los datos del usuario ".$datos['nombre_usuario'].", por favor intente nuevamente",
                "icono"=>"error"
            ];
        }

        return json_encode($alerta);
    }


    public function eliminarFotoUsuarioControlador(){

        $id=$this->limpiarCadena($_POST['usuario_id']); // se revisa el id recibido por seguridad

        # Verificando usuario #
        $datos=$this->ejecutarConsulta("SELECT * FROM usuario WHERE usuario_id='$id'");
        if($datos->rowCount()<=0){ // se ejecuta el sql para saber si el usuario existe o no, dependiendo de lo que devuelva utilizando rowCount si es 1 es que si existe
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"No hemos encontrado el usuario en el sistema",
                "icono"=>"error"
            ];
            return json_encode($alerta);
            exit();
        }else{
            $datos=$datos->fetch(); // si existe el usuario se toman todos sus datos de la base de datos y se guardan en una variable
        }

        # Directorio de imagenes #
        $img_dir="../views/fotos/"; // se guarda en una variable la direccion de la imagen

        chmod($img_dir,0777); // se le da la direccion y los permisos para esa direccion

        if(is_file($img_dir.$datos['usuario_foto'])){ // se verifica que la imagen exista en esa direccion

            chmod($img_dir.$datos['usuario_foto'],0777); // de ser asi se le proporcionan los permisos a la imagen directamente

            if(!unlink($img_dir.$datos['usuario_foto'])){ // y si es asi se elimina
                $alerta=[
                    "tipo"=>"simple",
                    "titulo"=>"Ocurrió un error inesperado",
                    "texto"=>"Error al intentar eliminar la foto del usuario, por favor intente nuevamente",
                    "icono"=>"error"
                ];
                return json_encode($alerta); // error de si no se puede eliminar
                exit();
            }
        }else{
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"No hemos encontrado la foto del usuario en el sistema",
                "icono"=>"error"
            ];
            return json_encode($alerta); // error sino encuentra la imagen
            exit();
        }

        // array para los datos de la imagen eliminada
        $usuario_datos_up=[
            [
                "campo_nombre"=>"usuario_foto",
                "campo_marcador"=>":Foto",
                "campo_valor"=>""
            ],
            [
                "campo_nombre"=>"usuario_actualizado",
                "campo_marcador"=>":Actualizado",
                "campo_valor"=>date("Y-m-d H:i:s")
            ]
        ];

        $condicion=[
            "condicion_campo"=>"usuario_id",
            "condicion_marcador"=>":ID",
            "condicion_valor"=>$id
        ];

        if($this->actualizarDatos("usuario",$usuario_datos_up,$condicion)){ // funcion sql para actualizar los datos

            if($id==$_SESSION['id']){
                $_SESSION['foto']=""; // comprobar si la sesion abierta es la que se esta modificando para que se modifique los datos de una
            }

            $alerta=[
                "tipo"=>"recargar",
                "titulo"=>"Foto eliminada",
                "texto"=>"La foto del usuario ".$datos['usuario_nombre']." ".$datos['usuario_apellido']." se elimino correctamente",
                "icono"=>"success" // mensaje de que se elimino la foto
            ];
        }else{
            $alerta=[
                "tipo"=>"recargar",
                "titulo"=>"Foto eliminada",
                "texto"=>"No hemos podido actualizar algunos datos del usuario ".$datos['usuario_nombre']." ".$datos['usuario_apellido'].", sin embargo la foto ha sido eliminada correctamente",
                "icono"=>"warning" //  mensaje de que no se pudo eliminar la foto
            ];
        }

        return json_encode($alerta);
    }


    /*----------  Controlador actualizar foto usuario  ----------*/
    public function actualizarFotoUsuarioControlador(){

        $id=$this->limpiarCadena($_POST['usuario_id']); // limpiamos el id por seguridad

        # Verificando usuario #
        $datos=$this->ejecutarConsulta("SELECT * FROM usuario WHERE usuario_id='$id'"); // verificamos que el usuario exista por su id, de existir lo guardamos en un array
        if($datos->rowCount()<=0){
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"No hemos encontrado el usuario en el sistema",
                "icono"=>"error"
            ];
            return json_encode($alerta);
            exit();
        }else{
            $datos=$datos->fetch();
        }

        # Directorio de imagenes #
        $img_dir="../views/fotos/";

        # Comprobar si se selecciono una imagen #
        if($_FILES['usuario_foto']['name']=="" && $_FILES['usuario_foto']['size']<=0){
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"No ha seleccionado una foto para el usuario",
                "icono"=>"error"
            ];
            return json_encode($alerta);
            exit(); // es para comprobar si existe una imagen
        }

        # Creando directorio #
        if(!file_exists($img_dir)){ // si la carpeta donde se guardara la foto no existe, se creara
            if(!mkdir($img_dir,0777)){
                $alerta=[
                    "tipo"=>"simple",
                    "titulo"=>"Ocurrió un error inesperado",
                    "texto"=>"Error al crear el directorio",
                    "icono"=>"error"
                ];
                return json_encode($alerta);
                exit();
            } 
        }

        # Verificando formato de imagenes #
        if(mime_content_type($_FILES['usuario_foto']['tmp_name'])!="image/jpeg" && mime_content_type($_FILES['usuario_foto']['tmp_name'])!="image/png"){
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"La imagen que ha seleccionado es de un formato no permitido",
                "icono"=>"error"
            ];
            return json_encode($alerta);
            exit(); // todo para comprobar si es jpg o png
        }

        # Verificando peso de imagen #
        if(($_FILES['usuario_foto']['size']/1024)>5120){
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"La imagen que ha seleccionado supera el peso permitido",
                "icono"=>"error"
            ];
            return json_encode($alerta);
            exit();
        } // verificar si la imagen seleccionar no pesa mas de 5mb

        # Nombre de la foto #
        if($datos['usuario_foto']!=""){
            $foto=explode(".", $datos['usuario_foto']); // al colocar explode y el punto se divide la cadena en dos partes una con el nombre y la otra con la extension
            $foto=$foto[0]; // entonces cuando colocamos aqui 0 elegimos el nombre y no la extension
        }else{
            $foto=str_ireplace(" ","_",$datos['usuario_nombre']);
            $foto=$foto."_".rand(0,100); // aqui con else se le crea el nombre desde cero a la foto
        }
        

        # Extension de la imagen #
        switch(mime_content_type($_FILES['usuario_foto']['tmp_name'])){
            case 'image/jpeg':
                $foto=$foto.".jpg";
            break;
            case 'image/png':
                $foto=$foto.".png";
            break;
        } // aqui le agregamos la extension a la foto dependiendo de cual sea

        chmod($img_dir,0777); // permisos de lectura y escritura

        # Moviendo imagen al directorio #
        if(!move_uploaded_file($_FILES['usuario_foto']['tmp_name'],$img_dir.$foto)){ // para mover la foto a la nueva carpeta 
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"No podemos subir la imagen al sistema en este momento",
                "icono"=>"error"
            ];
            return json_encode($alerta);
            exit();
        }

        # Eliminando imagen anterior #
        if(is_file($img_dir.$datos['usuario_foto']) && $datos['usuario_foto']!=$foto){ // comprobar si la foto anterior existe
            chmod($img_dir.$datos['usuario_foto'], 0777); // dar los permisos
            unlink($img_dir.$datos['usuario_foto']);  // eliminar la foto
        }

        $usuario_datos_up=[ // array para el sql
            [
                "campo_nombre"=>"usuario_foto",
                "campo_marcador"=>":Foto",
                "campo_valor"=>$foto
            ],
            [
                "campo_nombre"=>"usuario_actualizado",
                "campo_marcador"=>":Actualizado",
                "campo_valor"=>date("Y-m-d H:i:s")
            ]
        ];

        $condicion=[ // array para el sql
            "condicion_campo"=>"usuario_id",
            "condicion_marcador"=>":ID",
            "condicion_valor"=>$id
        ];

        if($this->actualizarDatos("usuario",$usuario_datos_up,$condicion)){

            if($id==$_SESSION['id']){
                $_SESSION['foto']=$foto;
            }

            $alerta=[
                "tipo"=>"recargar",
                "titulo"=>"Foto actualizada",
                "texto"=>"La foto del usuario ".$datos['usuario_nombre']." ".$datos['usuario_apellido']." se actualizo correctamente",
                "icono"=>"success"
            ];
        }else{

            $alerta=[
                "tipo"=>"recargar",
                "titulo"=>"Foto actualizada",
                "texto"=>"No hemos podido actualizar algunos datos del usuario ".$datos['usuario_nombre']." ".$datos['usuario_apellido']." , sin embargo la foto ha sido actualizada",
                "icono"=>"warning"
            ];
        }

        return json_encode($alerta);
    }




}





?>