<div class="container is-fluid mb-6">
	<h1 class="title">Producto</h1>
	<h2 class="subtitle">Nuevo producto</h2>
</div>

<div class="container pb-6 pt-6">

	<form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/productoAjax.php" method="POST" autocomplete="off" 
    enctype="multipart/form-data" > <!-- el enctype es necesario cuando se van a enviar archivos en este caso, img -->

		<input type="hidden" name="modulo_producto" value="registrar">

		<div class="columns">
		  	<div class="column">
		    	<div class="control">
					<label>Categoria</label><br>
				  	<?php 
                        echo $insListaCategoria->listaCategoriaProductoControlador("");
                    ?>
				</div>
		  	</div>
		  	<div class="column">
			  <div class="control">
					<label>Nombre del Producto</label>
				  	<input class="input" type="text" name="nombre_producto" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9 ]{3,100}" maxlength="100" required >
				</div>
		  	</div>
		</div>
		
		<div class="columns">
		  	<div class="column">
		    	<div class="control">
					<label>Precio de Compra</label>
					<input class="input" type="text" name="precio_compra" pattern="[0-9]+(\.[0-9]{0,2})?" maxlength="5">
				</div>
		  	</div>
		  	<div class="column">
		    	<div class="control">
					<label>Precio de Venta</label>
					<input class="input" type="text" name="precio_venta" pattern="[0-9]+(\.[0-9]{0,2})?" maxlength="5">
				</div>
		  	</div>
		</div>

		<div class="columns">
		  	<div class="column">
		    	<div class="control">
					<label>Cantidad del Producto</label>
					<input class="input" type="text" name="cantidad_producto" pattern="[0-9]{0,5}" maxlength="5">
				</div>
		  	</div>
		  	<div class="column">
		    	<div class="control">
				</div>
		  	</div>
		</div>

		<div class="columns">
		  	<div class="column">
				<div class="file has-name is-boxed">
					<label class="file-label">
						<input class="file-input" type="file" name="imagen_producto" accept=".jpg, .png, .jpeg" >
						<span class="file-cta">
							<span class="file-label">
								Seleccione una foto del producto
							</span>
						</span>
						<span class="file-name">JPG, JPEG, PNG. (MAX 5MB)</span>
					</label>
				</div>
		  	</div>
		</div>
		
		<p class="has-text-centered">
			<button type="reset" class="button is-link is-light is-rounded">Limpiar</button>
			<button type="submit" class="button is-info is-rounded">Guardar</button>
		</p>
	</form>
</div>