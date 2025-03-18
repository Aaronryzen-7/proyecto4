<?php


// este es el controlador de usuarios

namespace app\controllers;
use app\models\mainModel; // usamos el mainModel

class productController extends mainModel{
    // funcion para registrar usuarios y todo lo demas
    public function registrarProductoControlador(){

        // limpiamos las cadenas de los datos de los usuarios
        $categoria = $this->limpiarCadena($_POST['categoria']);
        $nombre_producto = $this->limpiarCadena($_POST['nombre_producto']);
        $precio_compra = $this->limpiarCadena($_POST['precio_compra']);
        $precio_venta = $this->limpiarCadena($_POST['precio_venta']);
        $cantidad_producto = $this->limpiarCadena($_POST['cantidad_producto']);


        // verificamos si uno de estos valores esta vacio de ser asi se mostrara un sweet alert
        if($categoria == "" || $nombre_producto == ""){

            $alerta = [
                "tipo"=>"simple",
                "titulo"=>"Ocurrio un error inesperado",
                "texto"=>"No has llenado todos los campos que son obligatorios",
                "icono"=>"error"
            ];
            return json_encode($alerta);
            exit();
        }
        if($precio_compra == "" || $precio_venta == ""){
            $precio_compra = 0;
            $precio_venta = 0;
        }
        if($cantidad_producto == ""){
            $cantidad_producto = 0;
        }


        // verificando integridad de los datos
        if($this->verificarDatos("[0-9]",$categoria)){
            $alerta = [
                "tipo"=>"simple",
                "titulo"=>"Ocurrio un error inesperado",
                "texto"=>"La categoria no coincide con el formato solicitado",
                "icono"=>"error"
            ];
            return json_encode($alerta);
            exit();
        }

        if($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9 ]{3,100}",$nombre_producto)){
            $alerta = [
                "tipo"=>"simple",
                "titulo"=>"Ocurrio un error inesperado",
                "texto"=>"El nombre del producto no coincide con el formato solicitado",
                "icono"=>"error"
            ];
            return json_encode($alerta);
            exit();
        }

        

        if($this->verificarDatos("[0-9]+(\.[0-9]{1,2})?",$precio_compra) || $this->verificarDatos("[0-9]+(\.[0-9]{1,2})?",$precio_venta)){
            $alerta = [
                "tipo"=>"simple",
                "titulo"=>"Ocurrio un error inesperado",
                "texto"=>"Los Precios no coinciden con el formato solicitado",
                "icono"=>"error"
            ];
            return json_encode($alerta);
            exit();
        }
        
        if($this->verificarDatos("[0-9]",$cantidad_producto)){
            $alerta = [
                "tipo"=>"simple",
                "titulo"=>"Ocurrio un error inesperado",
                "texto"=>"La cantidad del producto no coincide con el formato solicitado",
                "icono"=>"error"
            ];
            return json_encode($alerta);
            exit();
        }


        // verificar que el usuario sea unico en la base de datos
        $check_categoria = $this->ejecutarConsulta("SELECT id_categoria FROM categoria WHERE id_categoria='$categoria'");
        if($check_categoria->rowCount()!=1){ // es para saber cuantos resultados envio la base de datos y fueron almacenados en check_usuario
            $alerta = [
                "tipo"=>"simple",
                "titulo"=>"Ocurrio un error inesperado",
                "texto"=>"La categoria ingresada no existe",
                "icono"=>"error"
            ];
            return json_encode($alerta);
            exit();
        }

        $img_dir = "../views/fotos/";

        // comprobar si se selecciono una imagen
        if($_FILES['imagen_producto']['name'] != "" && $_FILES['imagen_producto']['size'] > 0){ // lo que se hace aqui es lo siguiente: si el archivo con el name usuario_foto es diferente de una cadena vacia y el mismo arhivo tiene un peso de mas de 0 quiere decir que si se selecciono un archivo
            if(!file_exists($img_dir)){ // comprueba si el directorio o carpeta existe
                if(!mkdir($img_dir, 0777)){ // si la carpeta no existe se crea
                    $alerta = [
                        "tipo"=>"simple",
                        "titulo"=>"Ocurrio un error inesperado",
                        "texto"=>"Error al crear la carpeta de las fotos",
                        "icono"=>"error"
                    ];
                    return json_encode($alerta);
                    exit(); // sino se logra crear lanza un error
                }
            }

            // verificando formato de imagenes
            if(mime_content_type($_FILES['imagen_producto']['tmp_name']) != "image/jpeg" // para verificar que la img se uno de estos dos formatos
            && mime_content_type($_FILES['imagen_producto']['tmp_name']) != "image/png"){
                $alerta = [
                    "tipo"=>"simple",
                    "titulo"=>"Ocurrio un error inesperado",
                    "texto"=>"La imagen es de un formato no permitido",
                    "icono"=>"error"
                ];
                return json_encode($alerta);
                exit();
            }


            // verificando el peso de la imagen

            if(($_FILES['imagen_producto']['size']/1024)>5120){ // es un calculo para verificar si la imagen pesa mas de 5mb, el peso esta en kb
                $alerta = [
                    "tipo"=>"simple",
                    "titulo"=>"Ocurrio un error inesperado",
                    "texto"=>"La imagen que ha seleccionado supera el peso permitido",
                    "icono"=>"error"
                ];
                return json_encode($alerta);
                exit();
            }


            // nombre de la foto
            $foto = str_ireplace(" ","_",$nombre_producto); // la foto tendra el nombre del usuario,pero si tiene un espacion sera remplazado por un guion bajo
            $foto = $foto."_".rand(0,100); // al nombre de la foto se le colocara un guion bajo seguido de un numero aleatorio


            // extension de la imagen
            switch(mime_content_type($_FILES['imagen_producto']['tmp_name'])){
                case "image/jpeg":
                    $foto = $foto.".jpg"; // dependiendo del caso se le agrega la extension que es
                break;

                case "image/png":
                    $foto = $foto.".png";
                break;
            }

            // dandole permisos para que se pueda guardar las imagenes en esa direccion, se le dieron permisos de lectura y escritura
            chmod($img_dir,0777);

            // moviendo imagen al directorio
            if(!move_uploaded_file($_FILES['imagen_producto']['tmp_name'], $img_dir.$foto)){ // es una funcion para poder mover la imagen o subir la imagen a donde queremos
                $alerta = [
                    "tipo"=>"simple",
                    "titulo"=>"Ocurrio un error inesperado",
                    "texto"=>"No podemos subir la imagen al sistema en estos momentos",
                    "icono"=>"error"
                ];
                return json_encode($alerta);
                exit();
            }

        }else{
            $foto = "";
        }

        $productos_datos_reg = [
            [
                "campo_nombre"=>"id_categoria",
                "campo_marcador"=>":Categoria",
                "campo_valor"=>$categoria
            ],
            [
                "campo_nombre"=>"nombre_producto",
                "campo_marcador"=>":NombreProducto",
                "campo_valor"=>$nombre_producto
            ],
            [
                "campo_nombre"=>"imagen_producto",
                "campo_marcador"=>":Imagen",
                "campo_valor"=>$foto
            ]
        ];

        $registrar_producto = $this->guardarDatos("productos",$productos_datos_reg);


        if($registrar_producto->rowCount()==1){ // funcion para ver si el usuario se registro
            $check_idProducto = $this->ejecutarConsulta("SELECT * FROM productos ORDER BY id_producto DESC LIMIT 1;");
            if($check_idProducto->rowCount()==1){
                $check_idProducto=$check_idProducto->fetch();

                $inventario_datos_reg = [
                    [
                        "campo_nombre"=>"id_producto",
                        "campo_marcador"=>":Producto",
                        "campo_valor"=>$check_idProducto['id_producto']
                    ],
                    [
                        "campo_nombre"=>"precio_compra",
                        "campo_marcador"=>":PrecioCompra",
                        "campo_valor"=>$precio_compra
                    ],
                    [
                        "campo_nombre"=>"precio_venta",
                        "campo_marcador"=>":PrecioVenta",
                        "campo_valor"=>$precio_venta
                    ],
                    [
                        "campo_nombre"=>"cantidad_producto",
                        "campo_marcador"=>":Cantidad",
                        "campo_valor"=>$cantidad_producto
                    ]
                ];
        
                $registrar_inventario = $this->guardarDatos("inventario",$inventario_datos_reg);

                if($registrar_inventario->rowCount()==1){
                    $alerta = [
                        "tipo"=>"limpiar",
                        "titulo"=>"Usuario registrado",
                        "texto"=>"El producto ".$nombre_producto." "."se registro con exito",
                        "icono"=>"success"
                    ];
                }
                else{
                    $alerta = [
                        "tipo"=>"simple",
                        "titulo"=>"Ocurrio un error inesperado",
                        "texto"=>"No se pudo registrar el usuario, por favor intente nuevamente",
                        "icono"=>"error"
                    ];
                }
            }
            
            
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

    public function listaCategoriaProductoControlador($contenido){
        if($contenido==""){
            $listaCategoria=$this->ejecutarConsulta("SELECT * FROM categoria;");
        }else{
            $listaCategoria=$this->ejecutarConsulta("SELECT * FROM categoria ORDER BY CASE WHEN id_categoria = '$contenido' THEN 0 ELSE 1 END, id_categoria;");
        }
        
        $totalListaCategoria=$this->ejecutarConsulta("SELECT COUNT(id_categoria) FROM categoria;");
        $totalListaCategoria = (int) $totalListaCategoria->fetchColumn();
        if($listaCategoria->rowCount()>0){
            $listaCategoria = $listaCategoria->fetchAll();
            

            $select = "<select name='categoria' class='input'>";
            foreach($listaCategoria as $lista){
                $select.="<option value='".$lista['id_categoria']."'>".$lista['nombre_categoria']."</option>";
            }
            $select.="</select>";
            return $select;
        }else{

        }
        
    }

    public function listarProductosControlador($pagina,$registros,$url,$busqueda){ /* pagina sera el numero total de paginas,
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

            $consulta_datos="SELECT p.id_producto, c.nombre_categoria, p.nombre_producto, i.precio_compra, i.precio_venta, 
            p.imagen_producto, i.cantidad_producto, i.id_inventario FROM productos p LEFT JOIN categoria c ON c.id_categoria = p.id_categoria
            LEFT JOIN inventario i ON i.id_producto = p.id_producto
            WHERE p.nombre_producto LIKE '%$busqueda%' OR i.id_inventario LIKE '%$busqueda%' OR c.nombre_categoria LIKE '%$busqueda%' 
            ORDER BY p.nombre_producto ASC LIMIT $inicio,$registros";
            /* a traves de condiciones y la funcion LIKE se pide que devuelva los datos de los usuarios que sean los mas compatible 
            posible con el buscador */

            $consulta_total="SELECT COUNT(p.id_producto) 
            FROM productos p 
            LEFT JOIN categoria c ON c.id_categoria = p.id_categoria
            LEFT JOIN inventario i ON i.id_producto = p.id_producto
            WHERE p.nombre_producto LIKE '%$busqueda%' OR i.id_inventario LIKE '%$busqueda%' OR c.nombre_categoria LIKE '%$busqueda%'";
            /* igual que aqui se usa LIKE y COUNT para obtener el numero total de id que aparecen desoues que se utilizo
            el buscador */

        }else{

            $consulta_datos="SELECT p.id_producto, c.nombre_categoria, p.nombre_producto, i.precio_compra, i.precio_venta, 
            p.imagen_producto, i.cantidad_producto, i.id_inventario FROM productos p LEFT JOIN categoria c ON c.id_categoria = p.id_categoria
            LEFT JOIN inventario i ON i.id_producto = p.id_producto
            ORDER BY p.nombre_producto ASC LIMIT $inicio,$registros";
            /* es para consultar todos los datos de los usuarios, el que dice id=1 es el administrador administrador, se pide que se
            muestre de forma ordenada por nombre y en limit se coloca a partir de que registro se va a empezar a mostrar, es decir,
            dice que se empiecen a contar de $inicio digamos que es 14 y que a partir de ahi se cuente 15 que es $registro
            es decir se mostrara los dato de los usuario del 14 al 29 */

            $consulta_total="SELECT COUNT(id_producto) FROM productos";
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
                        <th class="has-text-centered">ID</th>
                        <th class="has-text-centered">Nombre Producto</th>
                        <th class="has-text-centered">Categoria</th>
                        <th class="has-text-centered">Precio Compra</th>
                        <th class="has-text-centered">Precio Venta</th>
                        <th class="has-text-centered">Cantidad</th>
                        <th class="has-text-centered">Imagen de Referencia</th>
                        <th class="has-text-centered" colspan="3">Opciones</th>
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
                        <td>'.$rows['id_inventario'].'</td>
                        <td>'.$rows['nombre_producto'].'</td>
                        <td>'.$rows['nombre_categoria'].'</td>
                        <td>'.$rows['precio_compra'].'</td>
                        <td>'.$rows['precio_venta'].'</td>
                        <td>'.$rows['cantidad_producto'].'</td>
                        <td> <img src="'.APP_URL.'app/views/fotos/'.$rows['imagen_producto'].'" alt="imgProduct" class="imgProduct"></td>
                        <td>
			                <a href="'.APP_URL.'productPhoto/'.$rows['id_producto'].'/" class="button is-info is-rounded is-small">Foto</a>
			            </td>
                        <td>
                            <a href="'.APP_URL.'productUpdate/'.$rows['id_inventario'].'/" class="button is-success is-rounded is-small">Actualizar</a>
                        </td>
                        <td>
                            <form class="FormularioAjax" action="'.APP_URL.'app/ajax/productoAjax.php" method="POST" autocomplete="off" >

                                <input type="hidden" name="modulo_producto" value="eliminar">
                                <input type="hidden" name="id_producto" value="'.$rows['id_inventario'].'">

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

    public function eliminarProductoControlador(){
        $id=$this->limpiarCadena($_POST['id_producto']); // por seguridad limpiar el texto id

			

			# Verificando usuario #
		    $datos=$this->ejecutarConsulta("SELECT * FROM productos WHERE id_producto='$id'"); // consulta sql para que devuelva los datos del usuario que queremos eliminar
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

            
		    $eliminarProducto=$this->eliminarRegistro("productos","id_producto",$id); // ejecutar la consulta para eliminar el usuario

		    if($eliminarProducto->rowCount()==1){ // verificar si el usuario fue eliminado

		    	

		        $alerta=[
					"tipo"=>"recargar",
					"titulo"=>"Producto eliminado",
					"texto"=>"El producto " .$datos['nombre_producto'] ." ". "ha sido eliminado del sistema correctamente",
					"icono"=>"success"
				]; // mensaje y recargar la lista despues de ser eliminado

		    }else{

		    	$alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No hemos podido eliminar el producto ".$datos['nombre_producto'] ." ". "del sistema, por favor intente nuevamente",
					"icono"=>"error"
				];
		    } // si el usuario no fue eliminado lanzara este error

		    return json_encode($alerta);
    }

    // controlador para actualizar usuarios
    public function actualizarProductoControlador(){

        $id=$this->limpiarCadena($_POST['id_producto']);

        # Verificando usuario #
        $datos=$this->ejecutarConsulta("SELECT * FROM productos WHERE id_producto='$id'");
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
        $categoria = $this->limpiarCadena($_POST['categoria']);
        $nombre_producto = $this->limpiarCadena($_POST['nombre_producto']);
        $precio_compra = $this->limpiarCadena($_POST['precio_compra']);
        $precio_venta = $this->limpiarCadena($_POST['precio_venta']);
        $cantidad_producto = $this->limpiarCadena($_POST['cantidad_producto']);


        // verificamos si uno de estos valores esta vacio de ser asi se mostrara un sweet alert
        if($categoria == "" || $nombre_producto == "" || $precio_compra == "" || $precio_venta == "" || $cantidad_producto == ""){

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
        if($this->verificarDatos("[0-9]",$categoria)){
            $alerta = [
                "tipo"=>"simple",
                "titulo"=>"Ocurrio un error inesperado",
                "texto"=>"La categoria no coincide con el formato solicitado",
                "icono"=>"error"
            ];
            return json_encode($alerta);
            exit();
        }

        if($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9 ]{3,40}",$nombre_producto)){
            $alerta = [
                "tipo"=>"simple",
                "titulo"=>"Ocurrio un error inesperado",
                "texto"=>"El nombre del producto no coincide con el formato solicitado",
                "icono"=>"error"
            ];
            return json_encode($alerta);
            exit();
        }

        

        if($this->verificarDatos("[0-9]+(\.[0-9]{1,2})?",$precio_compra) || $this->verificarDatos("[0-9]+(\.[0-9]{1,2})?",$precio_venta)){
            $alerta = [
                "tipo"=>"simple",
                "titulo"=>"Ocurrio un error inesperado",
                "texto"=>"Los Precios no coinciden con el formato solicitado",
                "icono"=>"error"
            ];
            return json_encode($alerta);
            exit();
        }
        
        if($this->verificarDatos("[0-9]",$cantidad_producto)){
            $alerta = [
                "tipo"=>"simple",
                "titulo"=>"Ocurrio un error inesperado",
                "texto"=>"La cantidad del producto no coincide con el formato solicitado",
                "icono"=>"error"
            ];
            return json_encode($alerta);
            exit();
        }


        // verificar que el usuario sea unico en la base de datos
        $check_categoria = $this->ejecutarConsulta("SELECT id_categoria FROM categoria WHERE id_categoria='$categoria'");
        if($check_categoria->rowCount()!=1){ // es para saber cuantos resultados envio la base de datos y fueron almacenados en check_usuario
            $alerta = [
                "tipo"=>"simple",
                "titulo"=>"Ocurrio un error inesperado",
                "texto"=>"La categoria ingresada no existe",
                "icono"=>"error"
            ];
            return json_encode($alerta);
            exit();
        }

        

        $productos_datos_up=[
            [
                "campo_nombre"=>"id_categoria",
                "campo_marcador"=>":Categoria",
                "campo_valor"=>$categoria
            ],
            [
                "campo_nombre"=>"nombre_producto",
                "campo_marcador"=>":Nombre",
                "campo_valor"=>$nombre_producto
            ],
            [
                "campo_nombre"=>"precio_compra",
                "campo_marcador"=>":Compra",
                "campo_valor"=>$precio_compra
            ],
            [
                "campo_nombre"=>"precio_venta",
                "campo_marcador"=>":Venta",
                "campo_valor"=>$precio_venta
            ],
            [
                "campo_nombre"=>"cantidad_producto",
                "campo_marcador"=>":Cantidad",
                "campo_valor"=>$cantidad_producto
            ]
        ];

       

        $condicion=[
            "condicion_campo"=>"id_producto",
            "condicion_marcador"=>":ID",
            "condicion_valor"=>$id
        ];

        if($this->actualizarDatos("productos",$productos_datos_up,$condicion,$condicion)){

            

            $alerta=[
                "tipo"=>"recargar",
                "titulo"=>"Producto actualizado",
                "texto"=>"Los datos del producto ".$datos['nombre_producto']." se actualizaron correctamente",
                "icono"=>"success"
            ];
        }else{
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"No hemos podido actualizar los datos del producto ".$datos['nombre_producto'].", por favor intente nuevamente",
                "icono"=>"error"
            ];
        }

        return json_encode($alerta);
    }

    public function eliminarFotoProductoControlador(){

        $id=$this->limpiarCadena($_POST['id_producto']); // se revisa el id recibido por seguridad

        # Verificando usuario #
        $datos=$this->ejecutarConsulta("SELECT * FROM productos WHERE id_producto='$id'");
        if($datos->rowCount()<=0){ // se ejecuta el sql para saber si el usuario existe o no, dependiendo de lo que devuelva utilizando rowCount si es 1 es que si existe
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"No hemos encontrado el producto en el sistema",
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

        if(is_file($img_dir.$datos['imagen_producto'])){ // se verifica que la imagen exista en esa direccion

            chmod($img_dir.$datos['imagen_producto'],0777); // de ser asi se le proporcionan los permisos a la imagen directamente

            if(!unlink($img_dir.$datos['imagen_producto'])){ // y si es asi se elimina
                $alerta=[
                    "tipo"=>"simple",
                    "titulo"=>"Ocurrió un error inesperado",
                    "texto"=>"Error al intentar eliminar la foto del producto, por favor intente nuevamente",
                    "icono"=>"error"
                ];
                return json_encode($alerta); // error de si no se puede eliminar
                exit();
            }
        }else{
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"No hemos encontrado la foto del producto en el sistema",
                "icono"=>"error"
            ];
            return json_encode($alerta); // error sino encuentra la imagen
            exit();
        }

        // array para los datos de la imagen eliminada
        $producto_datos_up=[
            [
                "campo_nombre"=>"imagen_producto",
                "campo_marcador"=>":Foto",
                "campo_valor"=>""
            ]
        ];

        $condicion=[
            "condicion_campo"=>"id_producto",
            "condicion_marcador"=>":ID",
            "condicion_valor"=>$id
        ];

        if($this->actualizarDatos("productos",$producto_datos_up,$condicion)){ // funcion sql para actualizar los datos

            

            $alerta=[
                "tipo"=>"recargar",
                "titulo"=>"Foto eliminada",
                "texto"=>"La foto del producto ".$datos['nombre_producto']."",
                "icono"=>"success" // mensaje de que se elimino la foto
            ];
        }else{
            $alerta=[
                "tipo"=>"recargar",
                "titulo"=>"Foto eliminada",
                "texto"=>"No hemos podido actualizar algunos datos del producto ".$datos['usuario_nombre'].", sin embargo la foto ha sido eliminada correctamente",
                "icono"=>"warning" //  mensaje de que no se pudo eliminar la foto
            ];
        }

        return json_encode($alerta);
    }


    /*----------  Controlador actualizar foto usuario  ----------*/
    public function actualizarFotoProductoControlador(){

        $id=$this->limpiarCadena($_POST['id_producto']); // limpiamos el id por seguridad

        # Verificando usuario #
        $datos=$this->ejecutarConsulta("SELECT * FROM productos WHERE id_producto='$id'"); // verificamos que el usuario exista por su id, de existir lo guardamos en un array
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

        # Directorio de imagenes #
        $img_dir="../views/fotos/";

        # Comprobar si se selecciono una imagen #
        if($_FILES['imagen_producto']['name']=="" && $_FILES['imagen_producto']['size']<=0){
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"No ha seleccionado una foto para el producto",
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
        if(mime_content_type($_FILES['imagen_producto']['tmp_name'])!="image/jpeg" && mime_content_type($_FILES['imagen_producto']['tmp_name'])!="image/png"){
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
        if(($_FILES['imagen_producto']['size']/1024)>5120){
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
        if($datos['imagen_producto']!=""){
            $foto=explode(".", $datos['imagen_producto']); // al colocar explode y el punto se divide la cadena en dos partes una con el nombre y la otra con la extension
            $foto=$foto[0]; // entonces cuando colocamos aqui 0 elegimos el nombre y no la extension
        }else{
            $foto=str_ireplace(" ","_",$datos['nombre_producto']);
            $foto=$foto."_".rand(0,100); // aqui con else se le crea el nombre desde cero a la foto
        }
        

        # Extension de la imagen #
        switch(mime_content_type($_FILES['imagen_producto']['tmp_name'])){
            case 'image/jpeg':
                $foto=$foto.".jpg";
            break;
            case 'image/png':
                $foto=$foto.".png";
            break;
        } // aqui le agregamos la extension a la foto dependiendo de cual sea

        chmod($img_dir,0777); // permisos de lectura y escritura

        # Moviendo imagen al directorio #
        if(!move_uploaded_file($_FILES['imagen_producto']['tmp_name'],$img_dir.$foto)){ // para mover la foto a la nueva carpeta 
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
        if(is_file($img_dir.$datos['imagen_producto']) && $datos['imagen_producto']!=$foto){ // comprobar si la foto anterior existe
            chmod($img_dir.$datos['imagen_producto'], 0777); // dar los permisos
            unlink($img_dir.$datos['imagen_producto']);  // eliminar la foto
        }

        $producto_datos_up=[ // array para el sql
            [
                "campo_nombre"=>"imagen_producto",
                "campo_marcador"=>":Foto",
                "campo_valor"=>$foto
            ]
        ];

        $condicion=[ // array para el sql
            "condicion_campo"=>"id_producto",
            "condicion_marcador"=>":ID",
            "condicion_valor"=>$id
        ];

        if($this->actualizarDatos("productos",$producto_datos_up,$condicion)){

            

            $alerta=[
                "tipo"=>"recargar",
                "titulo"=>"Foto actualizada",
                "texto"=>"La foto del producto ".$datos['nombre_producto']."",
                "icono"=>"success"
            ];
        }else{

            $alerta=[
                "tipo"=>"recargar",
                "titulo"=>"Foto actualizada",
                "texto"=>"No hemos podido actualizar algunos datos del producto ".$datos['nombre_producto'].", sin embargo la foto ha sido actualizada",
                "icono"=>"warning"
            ];
        }

        return json_encode($alerta);
    }
}