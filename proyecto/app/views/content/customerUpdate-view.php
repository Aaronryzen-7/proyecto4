<div class="container is-fluid mb-6">
	<?php 

		$id=$insLogin->limpiarCadena($url[1]);

		
	?>
	<h1 class="title">Cliente</h1>
	<h2 class="subtitle">Actualizar Cliente</h2>
	
</div>
<div class="container pb-6 pt-6">
	<?php
	
		include "./app/views/inc/btn_back.php";

		$datos=$insLogin->seleccionarDatos("Unico","clientes","ci_cliente",$id);
        

		if($datos->rowCount()==1){
			$datos=$datos->fetch();
	?>

	<h2 class="title has-text-centered"><?php echo $datos['nombre_cliente'] ." ". $datos['apellido_cliente']; ?></h2>

	

	<form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/clienteAjax.php" method="POST" autocomplete="off" >

		<input type="hidden" name="modulo_cliente" value="actualizar">
		<input type="hidden" name="ci" value="<?php echo $datos['ci_cliente']; ?>">

		<div class="columns">
            <div class="column">
			  <div class="control">
					<label>Cedula del Cliente</label>
				  	<input class="input" type="text" name="ci_cliente" pattern="[0-9]{6,11}" maxlength="40" value="<?php echo $datos['ci_cliente']; ?>" required >
				</div>
		  	</div>
		  	<div class="column">
		  	</div>
		</div>

		<div class="columns">
            <div class="column">
			  <div class="control">
					<label>Nombre del Cliente</label>
				  	<input class="input" type="text" name="nombre_cliente" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9 ]{3,40}" maxlength="40" value="<?php echo $datos['nombre_cliente']; ?>" required >
				</div>
		  	</div>
		  	<div class="column">
			  <div class="control">
					<label>Apellido del Cliente</label>
				  	<input class="input" type="text" name="apellido_cliente" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9 ]{3,40}" maxlength="40" value="<?php echo $datos['apellido_cliente']; ?>" required >
				</div>
		  	</div>
		</div>
		
		<div class="columns">
		  	<div class="column">
		    	<div class="control">
					<label>Direccion</label>
					<input class="input" type="text" name="direccion_cliente" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9# ]{3,100}" maxlength="100" value="<?php echo $datos['direccion_cliente']; ?>" required >
				</div>
		  	</div>
		  	<div class="column">
		    	<div class="control">
					<label>Numero de Telefono</label>
					<input class="input" type="text" name="telefono_cliente" pattern="[0-9]{10,11}" maxlength="11" value="<?php echo $datos['telefono_cliente']; ?>" required >
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