<div class="container is-fluid mb-6">
	<h1 class="title">Clientes</h1>
	<h2 class="subtitle">Lista de Clientes</h2>
</div>
<div class="container pb-6 pt-6">
	<?php 
		use app\controllers\CustomerController;

		$insCliente = new CustomerController();

		echo $insCliente->listarCostumerControlador($url[1],10,$url[0],"");
	?>
</div>