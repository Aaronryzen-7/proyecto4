<div class="container is-fluid mb-6">
	<?php 

		$id=$insLogin->limpiarCadena($url[1]);

	?>
	<h1 class="title">Metodos de Pago</h1>
	<h2 class="subtitle">Actualizar Metodo de Pago</h2>
	
</div>
<div class="container pb-6 pt-6">
	<?php
	
		include "./app/views/inc/btn_back.php";

		$datos=$insLogin->seleccionarDatos("Unico","metodos_pagos","id_metodo",$id);

		if($datos->rowCount()==1){
			$datos=$datos->fetch();
	?>

	

	

	<form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/metodoAjax.php" method="POST" autocomplete="off" >

		<input type="hidden" name="modulo_metodo" value="actualizar">
		<input type="hidden" name="id_metodo" value="<?php echo $datos['id_metodo']; ?>">

		<div class="columns">
		  	<div class="column">
		    	<div class="control">
					<label>Nombre Metodo de Pago</label>
				  	<input class="input" type="text" name="nombre_metodo" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9 ]{3,60}" maxlength="60" value="<?php echo $datos['nombre_metodo']; ?>" required >
				</div>
		  	</div>
		</div>
		
		
		<br><br><br>
		<p class="has-text-centered">
			Para poder actualizar los datos de este usuario por favor ingrese su USUARIO y CLAVE con la que ha iniciado sesión
		</p>
		<div class="columns">
		  	<div class="column">
		    	<div class="control">
					<label>Usuario</label>
				  	<input class="input" type="text" name="administrador_usuario" pattern="[a-zA-Z0-9]{4,20}" maxlength="20" required >
				</div>
		  	</div>
		  	<div class="column">
		    	<div class="control">
					<label>Clave</label>
				  	<input class="input" type="password" name="administrador_clave" pattern="[a-zA-Z0-9$@.-]{7,100}" maxlength="100" required >
				</div>
		  	</div>
		</div>
		<p class="has-text-centered">
			<button type="submit" class="button is-success is-rounded">Actualizar</button>
		</p>
	</form>
	<?php
		}else{
			include "./app/views/inc/error_alert.php";
		}
	?>
</div>