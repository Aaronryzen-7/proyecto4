<div class="container is-fluid mb-6">
	<?php 

		$id=$insLogin->limpiarCadena($url[1]);

		
	?>
	<h1 class="title">Productos</h1>
	<h2 class="subtitle">Actualizar producto</h2>
	
</div>
<div class="container pb-6 pt-6">
	<?php
	
		include "./app/views/inc/btn_back.php";

		$datos=$insLogin->seleccionarDatos("Unico","inventario","id_inventario",$id);
        

		if($datos->rowCount()==1){
			$datos=$datos->fetch();
			$producto=$insLogin->seleccionarDatos("Unico", "productos", "id_producto", $datos['id_producto']);
			$producto=$producto->fetch();
			
	?>

	<h2 class="title has-text-centered"><?php echo $producto['nombre_producto']; ?></h2>

	

	<form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/productoAjax.php" method="POST" autocomplete="off" >

		<input type="hidden" name="modulo_producto" value="actualizar">
		<input type="hidden" name="id_producto" value="<?php echo $datos['id_producto']; ?>">

		<div class="columns">
		  	<div class="column">
		    	<div class="control">
					<label>Categoria</label><br>
				  	<?php 
                        echo $insListaCategoria->listaCategoriaProductoControlador($producto['id_categoria']);
                    ?>
				</div>
		  	</div>
		  	<div class="column">
			  <div class="control">
					<label>Nombre del Producto</label>
				  	<input class="input" type="text" name="nombre_producto" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9 ]{3,40}" maxlength="40" value="<?php echo $producto['nombre_producto']; ?>" required >
				</div>
		  	</div>
		</div>
		
		<div class="columns">
		  	<div class="column">
		    	<div class="control">
					<label>Precio de Compra</label>
					<input class="input" type="text" name="precio_compra" pattern="[0-9]+(\.[0-9]{1,2})?" maxlength="5" value="<?php echo $datos['precio_compra']; ?>" required >
				</div>
		  	</div>
		  	<div class="column">
		    	<div class="control">
					<label>Precio de Venta</label>
					<input class="input" type="text" name="precio_venta" pattern="[0-9]+(\.[0-9]{1,2})?" maxlength="5" value="<?php echo $datos['precio_venta']; ?>" required >
				</div>
		  	</div>
		</div>

		<div class="columns">
		  	<div class="column">
		    	<div class="control">
					<label>Cantidad del Producto</label>
					<input class="input" type="text" name="cantidad_producto" pattern="[0-9]" maxlength="5" value="<?php echo $datos['cantidad_producto']; ?>" required >
				</div>
		  	</div>
		  	<div class="column">
		    	<div class="control">
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