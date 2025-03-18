<div class="main-container">

    <form class="FormularioAjax box login" action="<?php echo APP_URL; ?>app/ajax/compraAjax.php" method="POST" autocomplete="off" >
        <input type="hidden" name="ci_cliente" value="ci_cliente">
		<h5 class="title is-5 has-text-centered is-uppercase">Cedula del Cliente</h5>

		<div class="field">
			<label class="label">Cedula del Cliente</label>
			<div class="control">
			    <input class="input" type="text" name="ci" pattern="[0-9]{6,11}" maxlength="12" required >
			</div>
		</div>

		

		<p class="has-text-centered mb-4 mt-3">
			<button type="submit" class="button is-info is-rounded">Iniciar Compra</button>
		</p>

	</form>
</div>