<aside class="sidebar">
    <div class="sidebar-logo">
        <i class="fa-solid fa-shop"></i>
        <h2>SuperMarket</h2>
    </div>
    <nav class="sidebar-menu">
        <?php $pagina_actual = basename($_SERVER['PHP_SELF']); ?>

        <a href="index.php" class="<?php echo ($pagina_actual == 'index.php') ? 'active' : ''; ?>"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
        <a href="ventas.php" class="<?php echo ($pagina_actual == 'ventas.php') ? 'active' : ''; ?>"><i class="fa-solid fa-cash-register"></i> Procesar Venta</a>
        <a href="clientes.php" class="<?php echo ($pagina_actual == 'clientes.php') ? 'active' : ''; ?>"><i class="fa-solid fa-users"></i> Gestión Clientes</a>
        
        <?php 
        if (isset($_SESSION['rol']) && strtolower($_SESSION['rol']) === 'administrador') { 
        ?>
            <a href="inventario.php" class="<?php echo ($pagina_actual == 'inventario.php') ? 'active' : ''; ?>"><i class="fa-solid fa-boxes-stacked"></i> Inventario / Stock</a>
            <a href="proveedores.php" class="<?php echo ($pagina_actual == 'proveedores.php') ? 'active' : ''; ?>"><i class="fa-solid fa-truck-field"></i> Proveedores</a>
            <a href="reportes.php" class="<?php echo ($pagina_actual == 'reportes.php') ? 'active' : ''; ?>"><i class="fa-solid fa-file-invoice-dollar"></i> Reportes</a>
            <a href="usuarios.php" class="<?php echo ($pagina_actual == 'usuarios.php') ? 'active' : ''; ?>"><i class="fa-solid fa-user-gear"></i> Gestión Usuarios</a>
        <?php } ?>
    </nav>
    <div class="sidebar-footer" style="display: flex; flex-direction: column; gap: 10px; padding: 15px;">
        <p style="margin: 0; font-size: 0.9rem; color: #b3b3b3;">
            <i class="fa-solid fa-circle-user" style="color: #e100ff;"></i> 
            Usuario: <strong><?php echo $_SESSION['nombre_usuario'] ?? 'catalinamedina641@gmail.com'; ?></strong>
        </p>
        <a href="logout.php" style="color: #ff4a4a; text-decoration: none; font-size: 0.9rem; font-weight: bold; display: flex; align-items: center; gap: 8px; transition: 0.3s;" onmouseover="this.style.color='#ff4a4a'" onmouseout="this.style.color='#ff4a4a'">
            <i class="fa-solid fa-right-from-bracket"></i> Cerrar Sesión
        </a>
    </div>
</aside>
