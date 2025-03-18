<?php 
	use app\controllers\compraController;

?>



<div class="container is-fluid mb-6">
	<h2>Detalles Compra</h2>
</div>

<form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/compraAjax.php" method="post" autocomplete="off" enctype="multipart/form-data">
	<input type="hidden" name="ci_cliente" value="registrarProducto">
<!--
	<div class="columns">
        <div class="column">
			<div class="control barrita">
				<label>ID del Producto</label>
				<input class="input" type="text" name="idProducto" pattern="[0-9]{0,999}" maxlength="4" required >
			</div>
		</div>
		<div class="column">
			<div class="control">
				<p class="has-text-centered mb-4 mt-3">
					<button type="submit" class="button is-info is-rounded">AGREGAR</button>
				</p>
			</div>
		</div>
	</div>
-->
</form>

<div class="container pb-6 pt-6">
<?php
	$insCompra = new compraController();

	echo $insCompra->listarCompraActualControlador(1,10,$url[0],"");

?>
</div>

<div>

<!--
<div class="column">
			<div class="control">
				<p class="has-text-centered mb-4 mt-3">
					<button type="submit" class="button is-info is-rounded">Realizar Compra</button>
				</p>
			</div>
		</div>
</div>
-->


