<div class="main-container">

    <form class="FormularioAjax box login" action="<?php echo APP_URL; ?>app/ajax/cambioDolarAjax.php" method="POST" autocomplete="off" >
        <input type="hidden" name="cambio_dolar" value="cambio_dolar">
		<h5 class="title is-5 has-text-centered is-uppercase">Asignar nuevo valor del dolar</h5>

		<div class="field">
			<label class="label">Valor del Dolar</label>
			<div class="control">
			    <input class="input" type="text" name="valor" pattern="[0-9]+(\.[0-9]{1,2})?" maxlength="5" required >
			</div>
		</div>

		

		<p class="has-text-centered mb-4 mt-3">
			<button type="submit" class="button is-info is-rounded">Cambiar Valor</button>
		</p>

	</form>
</div>