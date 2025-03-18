<?php




namespace app\controllers;
use app\models\mainModel; 

class methodController extends mainModel{
    public function registrarMetodoControlador(){

        // limpiamos las cadenas de los datos de los usuarios
        $nombreMetodo = $this->limpiarCadena($_POST['nombre_metodo']);


        // verificamos si uno de estos valores esta vacio de ser asi se mostrara un sweet alert
        if($nombreMetodo == ""){

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
        if($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9 ]{3,60}",$nombreMetodo)){
            $alerta = [
                "tipo"=>"simple",
                "titulo"=>"Ocurrio un error inesperado",
                "texto"=>"El nombre no coincide con el formato solicitado",
                "icono"=>"error"
            ];
            return json_encode($alerta);
            exit();
        }

        
        



        $metodos_datos_reg = [
            [
                "campo_nombre"=>"nombre_metodo",
                "campo_marcador"=>":NombreMetodo",
                "campo_valor"=>$nombreMetodo
            ]
        ];

        $registrar_metodo = $this->guardarDatos("metodos_pagos",$metodos_datos_reg);


        if($registrar_metodo->rowCount()==1){ // funcion para ver si el usuario se registro
            
            
            $alerta = [
                "tipo"=>"limpiar",
                "titulo"=>"Metodo de pago registrado",
                "texto"=>"El metodo de pago se creo con exito",
                "icono"=>"success"
            ];
        }else{ // entra en este sino se registro
            

            $alerta = [
                "tipo"=>"simple",
                "titulo"=>"Ocurrio un error inesperado",
                "texto"=>"No se pudo registrar el metodo de pago, por favor intente nuevamente",
                "icono"=>"error"
            ];
        }
        return json_encode($alerta);

    }

    public function listarMetodoControlador($pagina,$registros,$url,$busqueda){ /* pagina sera el numero total de paginas,
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

            $consulta_datos="SELECT * FROM metodos_pagos ORDER BY nombre_metodo ASC LIMIT $inicio,$registros";
            /* es para consultar todos los datos de los usuarios, el que dice id=1 es el administrador administrador, se pide que se
            muestre de forma ordenada por nombre y en limit se coloca a partir de que registro se va a empezar a mostrar, es decir,
            dice que se empiecen a contar de $inicio digamos que es 14 y que a partir de ahi se cuente 15 que es $registro
            es decir se mostrara los dato de los usuario del 14 al 29 */

            $consulta_total="SELECT COUNT(id_metodo) FROM metodos_pagos";
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
                        <th class="has-text-centered">Metodo de Pago</th>
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
                        <td>'.$rows['nombre_metodo'].'</td>
                        
                        <td>
                            <a href="'.APP_URL.'methodUpdate/'.$rows['id_metodo'].'/" class="button is-success is-rounded is-small">Actualizar</a>
                        </td>
                        <td>
                            <form class="FormularioAjax" action="'.APP_URL.'app/ajax/metodoAjax.php" method="POST" autocomplete="off" >

                                <input type="hidden" name="modulo_metodo" value="eliminar">
                                <input type="hidden" name="id_metodo" value="'.$rows['id_metodo'].'">

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

    public function actualizarMetodoControlador(){

        $id=$this->limpiarCadena($_POST['id_metodo']);

        

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
        $nombreMetodo=$this->limpiarCadena($_POST['nombre_metodo']);

        # Verificando campos obligatorios #
        if($nombreMetodo==""){
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
        if($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9 ]{3,60}",$nombreMetodo)){
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"El NOMBRE no coincide con el formato solicitado",
                "icono"=>"error"
            ];
            return json_encode($alerta);
            exit();
        }
        

        

        


        $metodos_datos_up=[
            [
                "campo_nombre"=>"nombre_metodo",
                "campo_marcador"=>":NombreMetodo",
                "campo_valor"=>$nombreMetodo
            ]
        ];

        $condicion=[
            "condicion_campo"=>"id_metodo",
            "condicion_marcador"=>":ID",
            "condicion_valor"=>$id
        ];

        if($this->actualizarDatos("metodos_pagos",$metodos_datos_up,$condicion)){

            

            $alerta=[
                "tipo"=>"recargar",
                "titulo"=>"Metodo actualizado",
                "texto"=>"El metodo de pago ha sido actualizado",
                "icono"=>"success"
            ];
        }else{
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"No hemos podido actualizar los datos del metodo de pago",
                "icono"=>"error"
            ];
        }

        return json_encode($alerta);
    }

    public function eliminarMetodoControlador(){
        $id=$this->limpiarCadena($_POST['id_metodo']); // por seguridad limpiar el texto id


          
		    $eliminarMetodo=$this->eliminarRegistro("metodos_pagos","id_metodo",$id); // ejecutar la consulta para eliminar el usuario

		    if($eliminarMetodo->rowCount()==1){ // verificar si el usuario fue eliminado

		    	

		        $alerta=[
					"tipo"=>"recargar",
					"titulo"=>"Metodo eliminado",
					"texto"=>"El metodo de pago fue eliminado",
					"icono"=>"success"
				]; // mensaje y recargar la lista despues de ser eliminado

		    }else{

		    	$alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No hemos podido eliminar el metodo de pago",
					"icono"=>"error"
				];
		    } // si el usuario no fue eliminado lanzara este error

		    return json_encode($alerta);
    }
}