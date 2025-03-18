<div class="container is-fluid mb-6">
	<h1 class="title">Metodos de Pago</h1>
	<h2 class="subtitle">Lista de Metodos de Pago/h2>
</div>
<div class="container pb-6 pt-6">
	<?php 
		use app\controllers\methodController;

		$insMetodo = new methodController();

		echo $insMetodo->listarMetodoControlador($url[1],10,$url[0],"");
	?>
</div>