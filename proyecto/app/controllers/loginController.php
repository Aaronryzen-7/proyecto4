<?php

	namespace app\controllers;
	use app\models\mainModel;

	class loginController extends mainModel{

		/*----------  Controlador iniciar sesion  ----------*/
		public function iniciarSesionControlador(){

			$usuario=$this->limpiarCadena($_POST['login_usuario']); // limpiamos el texto enviado del formulario del login
		    $clave=$this->limpiarCadena($_POST['login_clave']);

		    # Verificando campos obligatorios #
		    if($usuario=="" || $clave==""){
		        echo "<script>
			        Swal.fire({
					  icon: 'error',
					  title: 'Ocurrió un error inesperado',
					  text: 'No has llenado todos los campos que son obligatorios'
					});
				</script>"; /* si el usuario o clave se encuentran vacios se arrojara este error con sweetAlert
                es distinto por que es directo con js */
		    }else{ // si ninguno esta vacion se verificara la integridad de los datos y despues se verificara si es unico

			    # Verificando integridad de los datos #
			    if($this->verificarDatos("[a-zA-Z0-9]{4,20}",$usuario)){
			        echo "<script>
				        Swal.fire({
						  icon: 'error',
						  title: 'Ocurrió un error inesperado',
						  text: 'El USUARIO no coincide con el formato solicitado'
						});
					</script>";
			    }else{

			    	# Verificando integridad de los datos #
				    if($this->verificarDatos("[a-zA-Z0-9$@.-]{7,100}",$clave)){
				        echo "<script>
					        Swal.fire({
							  icon: 'error',
							  title: 'Ocurrió un error inesperado',
							  text: 'La CLAVE no coincide con el formato solicitado'
							});
						</script>";
				    }else{

					    # Verificando usuario #
					    $check_usuario=$this->ejecutarConsulta("SELECT * FROM usuarios WHERE nombre_usuario='$usuario'");

					    if($check_usuario->rowCount()==1){ // comprobar si la base de dato devolvio un valor

					    	$check_usuario=$check_usuario->fetch(); // para crear un array asociativo de cada uno de los valores de la base de datos

					    	if($check_usuario['nombre_usuario']==$usuario && password_verify($clave,$check_usuario['contrasena'])){ 
							
                            // verifica si el usuario y la clave son correctas

								$consulta_permisos = "SELECT * FROM usuario_permisos WHERE id_usuario='".$check_usuario['id_usuario']."';";
								$check_permisos=$this->ejecutarConsulta($consulta_permisos);
								$check_dolar=$this->ejecutarConsulta("SELECT * FROM dolar_cambio ORDER BY id_cambio DESC LIMIT 1;");
								if($check_permisos->rowCount()==1 AND $check_dolar->rowCount()==1){
									$check_permisos=$check_permisos->fetch();
									$check_dolar=$check_dolar->fetch();

									$_SESSION['id']=$check_usuario['id_usuario'];
									$_SESSION['nombre']=$check_usuario['nombre_usuario'];
									$_SESSION['permisos']=$check_permisos['id_permiso'];
									$_SESSION['dolar']=$check_dolar['valor'];
									// guarda cada uno de los datos que utilizaremos en las variables de session


									if(headers_sent()){
										echo "<script> window.location.href='".APP_URL."dashboard/'; </script>";
									}else{
										header("Location: ".APP_URL."dashboard/");;
									} // este if-else es para enviar al usuario al dashboard o menu

								}else{
									echo "<script>
							        Swal.fire({
									  icon: 'error',
									  title: 'Ocurrió un error inesperado',
									  text: 'El Usuario no posee ningun permiso, contacte con el Administrador'
									});
									</script>";
								}

					    		

					    	}else{
					    		echo "<script>
							        Swal.fire({
									  icon: 'error',
									  title: 'Ocurrió un error inesperado',
									  text: 'Usuario o clave incorrectos'
									});
								</script>";
					    	} // si el usuario o clave no coinciden lanza este error

					    }else{ // si en el rowCount devuelve otro valor, mostrara este error
					        echo "<script>
						        Swal.fire({
								  icon: 'error',
								  title: 'Ocurrió un error inesperado',
								  text: 'Usuario o clave incorrectos'
								});
							</script>";
					    }
				    }
			    }
		    }
		}


		/*----------  Controlador cerrar sesion  ----------*/
		public function cerrarSesionControlador(){

			session_destroy(); // con esto se elimina toda la sesion

		    if(headers_sent()){
                echo "<script> window.location.href='".APP_URL."login/'; </script>";
            }else{
                header("Location: ".APP_URL."login/");
            }
		}

	}