<nav class="navbar">
    <div class="navbar-brand">
        <a class="navbar-item" href="<?php echo APP_URL; ?>dashboard/">
            <img src="<?php echo APP_URL; ?>app/views/img/sai.jpeg" alt="imgSAI" class="imgSAI" height="100%" width="100%">
        </a>
        <div class="navbar-burger" data-target="navbarExampleTransparentExample">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>

    <div id="navbarExampleTransparentExample" class="navbar-menu">

        <div class="navbar-start">
            <a class="navbar-item" href="<?php echo APP_URL; ?>dashboard/">
                Dashboard
            </a>

            <?php
            if($_SESSION['permisos']==2 OR $_SESSION['permisos']==1){
            ?>
            <div class="navbar-item has-dropdown is-hoverable">
                <a class="navbar-link" href="#">
                    Usuarios
                </a>
                <div class="navbar-dropdown is-boxed">

                    <a class="navbar-item" href="<?php echo APP_URL; ?>userNew/">
                        Nuevo
                    </a>
                    <a class="navbar-item" href="<?php echo APP_URL; ?>userList/">
                        Lista
                    </a>
                    <a class="navbar-item" href="<?php echo APP_URL; ?>userSearch/">
                        Buscar
                    </a>

                </div>
            </div>
            <?php } ?>

            <?php
            if($_SESSION['permisos']==3 OR $_SESSION['permisos']==1){
            ?>
            
            <div class="navbar-item has-dropdown is-hoverable">
                <a class="navbar-link" href="#">
                    Productos
                </a>
                <div class="navbar-dropdown is-boxed">

                    <a class="navbar-item" href="<?php echo APP_URL; ?>productNew/">
                        Nuevo Producto
                    </a>
                    <a class="navbar-item" href="<?php echo APP_URL; ?>productList/">
                        Lista Productos
                    </a>
                    <a class="navbar-item" href="<?php echo APP_URL; ?>productSearch/">
                        Buscar Producto
                    </a>
                    <a class="navbar-item" href="<?php echo APP_URL; ?>categoryNew/">
                        Nueva Categoria
                    </a>
                    <a class="navbar-item" href="<?php echo APP_URL; ?>categoryList/">
                        Lista Categoria
                    </a>
                    <a class="navbar-item" href="<?php echo APP_URL; ?>categoryList/">
                        Inventario
                    </a>

                </div>
            </div>
            <?php } ?>

            

           

            <?php
            if($_SESSION['permisos']==3 OR $_SESSION['permisos']==1){
            ?>
            <div class="navbar-item has-dropdown is-hoverable">
                <a class="navbar-link" href="#">
                    Compras
                </a>
                <div class="navbar-dropdown is-boxed">

                    <a class="navbar-item" href="<?php echo APP_URL; ?>methodNew/">
                        Anotar Compra
                    </a>
                    <a class="navbar-item" href="<?php echo APP_URL; ?>methodList/">
                        Historial de Compras
                    </a>
                </div>
            </div>
            <?php } ?>

            <?php
            if($_SESSION['permisos']==3 OR $_SESSION['permisos']==1){
            ?>
            <div class="navbar-item has-dropdown is-hoverable">
                <a class="navbar-link" href="#">
                    Ventas
                </a>
                <div class="navbar-dropdown is-boxed">

                    <a class="navbar-item" href="<?php echo APP_URL; ?>ciCompraNew/">
                        Realizar Venta
                    </a>
                    <a class="navbar-item" href="<?php echo APP_URL; ?>methodList/">
                        Historial de Ventas
                    </a>
                </div>
            </div>
            <?php } ?>

            <?php
            if($_SESSION['permisos']==3 OR $_SESSION['permisos']==1){
            ?>
            <div class="navbar-item has-dropdown is-hoverable">
                <a class="navbar-link" href="#">
                    Clientes
                </a>
                <div class="navbar-dropdown is-boxed">

                    <a class="navbar-item" href="<?php echo APP_URL; ?>customerNew/">
                        Nuevo Cliente
                    </a>
                    <a class="navbar-item" href="<?php echo APP_URL; ?>customerList/">
                        Lista Cliente
                    </a>
                    <a class="navbar-item" href="<?php echo APP_URL; ?>customerSearch/">
                        Buscar Cliente
                    </a>
                </div>
            </div>
            <?php } ?>

            <?php
            if($_SESSION['permisos']==3 OR $_SESSION['permisos']==1){
            ?>
            <div class="navbar-item has-dropdown is-hoverable">
                <a class="navbar-link" href="#">
                    Credito
                </a>
                <div class="navbar-dropdown is-boxed">

                    <a class="navbar-item" href="<?php echo APP_URL; ?>methodNew/">
                        Nueva Venta a Credito
                    </a>
                    <a class="navbar-item" href="<?php echo APP_URL; ?>methodList/">
                        Lista Clientes
                    </a>
                    <a class="navbar-item" href="<?php echo APP_URL; ?>methodList/">
                        Agregar Pago
                    </a>
                </div>
            </div>
            <?php } ?>

            <?php
            if($_SESSION['permisos']==3 OR $_SESSION['permisos']==1){
            ?>
            <div class="navbar-item has-dropdown is-hoverable">
                <a class="navbar-link" href="#">
                    Apartados
                </a>
                <div class="navbar-dropdown is-boxed">

                    <a class="navbar-item" href="<?php echo APP_URL; ?>methodNew/">
                        Nueva Venta por Apartado
                    </a>
                    <a class="navbar-item" href="<?php echo APP_URL; ?>methodList/">
                        Lista Clientes
                    </a>
                    <a class="navbar-item" href="<?php echo APP_URL; ?>methodList/">
                        Agregar Pago
                    </a>
                </div>
            </div>
            <?php } ?>



        </div>


       
    </div>

        <div class="navbar-end">
            <a class="navbar-item" href="<?php echo APP_URL; ?>dolarCambio/">
                <?php echo $_SESSION['dolar']." "; ?>Bs
            </a>
            <div class="navbar-item has-dropdown is-hoverable">
                <a class="navbar-link">
                    ** <?php echo $_SESSION['nombre']; ?> **
                </a>
                <div class="navbar-dropdown is-boxed">

                    <a class="navbar-item" href="<?php echo APP_URL."userUpdate/".$_SESSION['id']?>">
                        Mi cuenta
                    </a>
                    <hr class="navbar-divider">
                    <a class="navbar-item" href="<?php echo APP_URL; ?>logOut/" id="btn_exit" >
                        Salir
                    </a>

                </div>
            </div>
        </div>

    </div>
</nav>