<div class="container is-fluid mb-6">
	<h1 class="title">Categoria de Productos</h1>
	<h2 class="subtitle">Lista de Categorias</h2>
</div>
<div class="container pb-6 pt-6">
	<?php 
		use app\controllers\categoryController;

		$insCambioDolar = new categoryController();

		echo $insCambioDolar->listarCategoriaControlador($url[1],10,$url[0],"");
	?>
</div>