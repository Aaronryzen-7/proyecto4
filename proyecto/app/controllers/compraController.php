<?php

	namespace app\controllers;
	use app\models\mainModel;

	class compraController extends mainModel{

        public function comprobarCedulaControlador(){
            // limpiamos las cadenas de los datos de los usuarios
            $ci_cliente = $this->limpiarCadena($_POST['ci']);
    
    
            // verificamos si uno de estos valores esta vacio de ser asi se mostrara un sweet alert
            if($ci_cliente == ""){
    
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
            if($this->verificarDatos("[0-9]{6,11}",$ci_cliente)){
                $alerta = [
                    "tipo"=>"simple",
                    "titulo"=>"Ocurrio un error inesperado",
                    "texto"=>"El valor no coincide con el formato solicitado",
                    "icono"=>"error"
                ];
                return json_encode($alerta);
                exit();
            }

            $check_ci = $this->ejecutarConsulta("SELECT * FROM clientes WHERE ci_cliente='$ci_cliente'");
            $check_dolar = $this->ejecutarConsulta("SELECT * FROM dolar_cambio ORDER BY id_cambio DESC LIMIT 1");
            if($check_ci->rowCount()<1 OR $check_dolar->rowCount()<1){ // es para saber cuantos resultados envio la base de datos y fueron almacenados en check_usuario
                $alerta = [
                    "tipo" => "redireccionar",
                    "titulo" => "Crea el Cliente",
                    "texto" => "No hay cliente con esta cedula",
                    "icono" => "error",
                    "url" => APP_URL."customerNew/" // URL de redirección
                ];
                return json_encode($alerta);
                exit();
            }else{
                $check_dolar = $check_dolar->fetch();
                $check_ci = $check_ci->fetch();
                $compras_datos_reg = [
                    [
                        "campo_nombre"=>"ci_cliente",
                        "campo_marcador"=>":CI",
                        "campo_valor"=>$check_ci['ci_cliente']
                    ],
                    [
                        "campo_nombre"=>"fecha_venta",
                        "campo_marcador"=>":FechaVenta",
                        "campo_valor"=>date("Y-m-d H:i:s")
                    ],
                    [
                        "campo_nombre"=>"total_venta",
                        "campo_marcador"=>":TotalVenta",
                        "campo_valor"=>"0"
                    ],
                    [
                        "campo_nombre"=>"id_cambio",
                        "campo_marcador"=>":CambioDolar",
                        "campo_valor"=>$check_dolar['id_cambio']
                    ],
                    [
                        "campo_nombre"=>"es_credito",
                        "campo_marcador"=>":Credito",
                        "campo_valor"=>"0"
                    ],
                    [
                        "campo_nombre"=>"es_apartado",
                        "campo_marcador"=>":Apartado",
                        "campo_valor"=>"0"
                    ]
                ];
        
                $registrar_compra = $this->guardarDatos("ventas",$compras_datos_reg);
        
                
                if($registrar_compra->rowCount()==1){ // funcion para ver si el usuario se registro
                    $alerta = [
                        "tipo" => "redireccionar",
                        "titulo" => "Realiza la compra",
                        "texto" => "Cliente encontrado con exito",
                        "icono" => "success",
                        "url" => APP_URL.'buysNew/'.$check_ci['ci_cliente'].'/'
                    ];
                    return json_encode($alerta);
                    
                }else{ // entra en este sino se registro
                    
        
                }
              
        
            }
                
            

        }
        

        public function listarCompraActualControlador($pagina,$registros,$url,$busqueda){ /* pagina sera el numero total de paginas,
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
    
                $consulta_datos="SELECT p.id_producto, c.nombre_categoria, p.nombre_producto, p.precio_compra, p.precio_venta, 
                p.imagen_producto, p.cantidad_producto FROM productos p LEFT JOIN categoria c ON c.id_categoria = p.id_categoria 
                WHERE p.nombre_producto LIKE '%$busqueda%' OR p.id_producto LIKE '%$busqueda%' OR c.nombre_categoria LIKE '%$busqueda%' 
                ORDER BY p.nombre_producto ASC LIMIT $inicio,$registros";
                /* a traves de condiciones y la funcion LIKE se pide que devuelva los datos de los usuarios que sean los mas compatible 
                posible con el buscador */
    
                $consulta_total="SELECT COUNT(p.id_producto) 
                FROM productos p 
                LEFT JOIN categoria c ON c.id_categoria = p.id_categoria 
                WHERE p.nombre_producto LIKE '%$busqueda%' OR p.id_producto LIKE '%$busqueda%' OR c.nombre_categoria LIKE '%$busqueda%'";
                /* igual que aqui se usa LIKE y COUNT para obtener el numero total de id que aparecen desoues que se utilizo
                el buscador */
    
            }else{
                $consulta_idCompra="SELECT id_venta FROM ventas ORDER BY id_venta DESC LIMIT 1;";
                $datosCompra = $this->ejecutarConsulta($consulta_idCompra); // utilizar la funcion para ejecutar la consulta y devolver el valor
                $datosCompra = $datosCompra->fetch();
                $consulta_datos="SELECT id_detalle, id_inventario, precio_unitario FROM detalles_ventas WHERE id_venta=".$datosCompra['id_venta']." ORDER BY id_inventario ASC LIMIT $inicio,$registros";
                /* es para consultar todos los datos de los usuarios, el que dice id=1 es el administrador administrador, se pide que se
                muestre de forma ordenada por nombre y en limit se coloca a partir de que registro se va a empezar a mostrar, es decir,
                dice que se empiecen a contar de $inicio digamos que es 14 y que a partir de ahi se cuente 15 que es $registro
                es decir se mostrara los dato de los usuario del 14 al 29 */
    
                $consulta_total="SELECT COUNT(id_detalle) FROM detalles_ventas WHERE id_venta=".$datosCompra['id_venta']."";
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
                            <th>Compra</th>
                            <td>1</td>
                        </tr>
                        <tr>
                            <th>Fecha y Hora</th>
                            <td>24-02-2025 08:12:55</td>
                        </tr>
                        <tr>
                            <th>Proveedor</th>
                            <td>La Polar</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <th>ID</th>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio Compra Bs</th>
                            <th>REF</th>
                            <th>Total Bs</th>
                            <th>Total REF</th>
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
                            <td>3</td>
                            <td>Harina Pan</td>
                            <td>400</td>
                            <td>35.00</td>
                            <td>0.50</td>
                            <td>14000.00</td>
                            <td>200</td>
                           
                        </tr>
                        <tr class="has-text-centered" >
                            <td>5</td>
                            <td>Mantequilla</td>
                            <td>100</td>
                            <td>70.00</td>
                            <td>1.00</td>
                            <td>7000.00</td>
                            <td>100</td>
                           
                        </tr>
                    ';// el date es para darle un nuevo formato a la fecha y hora y el strtotime es para pasar la fecha y hora de string a formato time
                    $contador++; // para ir al siguiente registro
                }
                $tabla.='<tr class="has-text-centered" >
                            
                            
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <th></th>
                            <td></td>
                            
                           
                            
                            
                        </tr>';
                $tabla.='<tr class="has-text-centered" >
                            
                            
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <th>Total Bs</th>
                            <td>21000.00</td>
                            
                           
                            
                            
                        </tr>';
                    $tabla.='<tr class="has-text-centered" >
                            
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <th>REF</th>
                            <td>300.00</td>
                            
                           
                            
                            
                        </tr>';
                        
                
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
    
        public function registrarProductoCompraControlador(){

            // limpiamos las cadenas de los datos de los usuarios
            $idProducto = $this->limpiarCadena($_POST['idProducto']);
    
    
            // verificamos si uno de estos valores esta vacio de ser asi se mostrara un sweet alert
            if($idProducto == ""){
    
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
            if($this->verificarDatos("[0-9]{0,999}",$idProducto)){
                $alerta = [
                    "tipo"=>"simple",
                    "titulo"=>"Ocurrio un error inesperado",
                    "texto"=>"El ID del producto no coincide con el formato solicitado",
                    "icono"=>"error"
                ];
                return json_encode($alerta);
                exit();
            }
    
    
            // verificar que el usuario sea unico en la base de datos
            $check_producto = $this->ejecutarConsulta("SELECT * FROM productos WHERE id_producto='$idProducto'");
            $check_detalleCompra = $this->ejecutarConsulta("SELECT * FROM compras ORDER BY id_compra DESC LIMIT 1;");
            if($check_producto->rowCount()<=0 OR $check_detalleCompra->rowCount()<=0){ // es para saber cuantos resultados envio la base de datos y fueron almacenados en check_usuario
                $alerta = [
                    "tipo"=>"simple",
                    "titulo"=>"Ocurrio un error inesperado",
                    "texto"=>"El producto no existe",
                    "icono"=>"error"
                ];
                return json_encode($alerta);
                exit();
            }else{
                $check_producto = $check_producto->fetch();
                $check_detalleCompra = $check_detalleCompra->fetch();
            }
    
            
    
            $productosLista_datos_reg = [
                
                [
                    "campo_nombre"=>"id_compra",
                    "campo_marcador"=>":IdCompra",
                    "campo_valor"=>$check_detalleCompra['id_compra']
                ],
                [
                    "campo_nombre"=>"id_producto",
                    "campo_marcador"=>":IdProducto",
                    "campo_valor"=>$idProducto
                ],
                [
                    "campo_nombre"=>"cantidad",
                    "campo_marcador"=>":Cantidad",
                    "campo_valor"=>"1"
                ],
                [
                    "campo_nombre"=>"precio_unitario",
                    "campo_marcador"=>":PrecioUnitario",
                    "campo_valor"=>$check_producto['precio_venta']
                ]
            ];
    
            $registrar_listaCompra = $this->guardarDatos("detalles_compras",$productosLista_datos_reg);
    
    
            if($registrar_listaCompra->rowCount()==1){ // funcion para ver si el usuario se registro
                
                
                $alerta = [
                    "tipo"=>"recargar",
                    "titulo"=>"Producto en lista",
                    "texto"=>"El producto ".$check_producto['nombre_producto']." "."se registro con exito",
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
    }