<div class="container is-fluid mb-6">
	<h1 class="title">Metodos de Pago</h1>
	<h2 class="subtitle">Nuevo Metodo</h2>
</div>

<div class="container pb-6 pt-6">

	<form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/metodoAjax.php" method="POST" autocomplete="off" 
    enctype="multipart/form-data" > <!-- el enctype es necesario cuando se van a enviar archivos en este caso, img -->

		<input type="hidden" name="modulo_metodo" value="registrar">

		<div class="columns">
		  	<div class="column">
		    	<div class="control">
					<label>Metodo de Pago</label>
				  	<input class="input" type="text" name="nombre_metodo" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9 ]{3,60}" maxlength="60" required >
				</div>
		  	</div>
		</div>
		
		
		<p class="has-text-centered">
			<button type="reset" class="button is-link is-light is-rounded">Limpiar</button>
			<button type="submit" class="button is-info is-rounded">Guardar</button>
		</p>
	</form>