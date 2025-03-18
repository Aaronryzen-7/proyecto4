<div class="container is-fluid mb-6">
	<h1 class="title">Cliente</h1>
	<h2 class="subtitle">Nuevo Cliente</h2>
</div>

<div class="container pb-6 pt-6">

	<form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/clienteAjax.php" method="POST" autocomplete="off" 
    enctype="multipart/form-data" > <!-- el enctype es necesario cuando se van a enviar archivos en este caso, img -->

		<input type="hidden" name="modulo_cliente" value="registrar">

        <div class="columns">
            <div class="column">
			  <div class="control">
					<label>Cedula del Cliente</label>
				  	<input class="input" type="text" name="ci_cliente" pattern="[0-9]{6,11}" maxlength="40" required >
				</div>
		  	</div>
		  	<div class="column">
		  	</div>
		</div>

		<div class="columns">
            <div class="column">
			  <div class="control">
					<label>Nombre del Cliente</label>
				  	<input class="input" type="text" name="nombre_cliente" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9 ]{3,40}" maxlength="40" required >
				</div>
		  	</div>
		  	<div class="column">
			  <div class="control">
					<label>Apellido del Cliente</label>
				  	<input class="input" type="text" name="apellido_cliente" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9 ]{3,40}" maxlength="40" required >
				</div>
		  	</div>
		</div>
		
		<div class="columns">
		  	<div class="column">
		    	<div class="control">
					<label>Direccion</label>
					<input class="input" type="text" name="direccion_cliente" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9# ]{3,100}" maxlength="100" required >
				</div>
		  	</div>
		  	<div class="column">
		    	<div class="control">
					<label>Numero de Telefono</label>
					<input class="input" type="text" name="telefono_cliente" pattern="[0-9]{10,11}" maxlength="11" required >
				</div>
		  	</div>
		</div>

		
		
		<p class="has-text-centered">
			<button type="reset" class="button is-link is-light is-rounded">Limpiar</button>
			<button type="submit" class="button is-info is-rounded">Guardar</button>
		</p>
	</form>
</div>