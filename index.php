<?php
// 1. SIEMPRE INICIAR LA SESIÓN EN LA PRIMERA LÍNEA
session_start();

// 🛡️ CANDADO DE SEGURIDAD: Si no existe la sesión de usuario, lo mandamos a loguearse
if (!isset($_SESSION['nombre_usuario'])) {
    header("Location: login.php");
    exit();
}

// 2. Conectar a la base de datos después de validar la sesión
include("conexion.php"); 

if(isset($_POST['guardar_venta'])){

    $idProducto = $_POST['producto'];
    $cantidad = $_POST['cantidad'];

    $consulta = mysqli_query($conexion, "SELECT * FROM Producto WHERE idProducto = $idProducto");
    $producto = mysqli_fetch_assoc($consulta);

    $precio = $producto['precio'];
    $stockActual = $producto['stock'];

    if($stockActual <= 0){
        echo "<script>
                alert('❌ No hay stock disponible');
                window.location='index.php';
              </script>";
        exit();
    }

    if($cantidad > $stockActual){
        echo "<script>
                alert('❌ Stock insuficiente');
                window.location='index.php';
              </script>";
        exit();
    }

    $total = $precio * $cantidad;

    // Aquí también aseguramos que si se genera una venta desde el index use NOW()
    mysqli_query($conexion, "INSERT INTO Venta(fecha, total) VALUES(NOW(), '$total')");
    $idVenta = mysqli_insert_id($conexion);

    mysqli_query($conexion, "INSERT INTO DetalleVenta(idVenta, idProducto, cantidad, subtotal)
                             VALUES('$idVenta','$idProducto','$cantidad','$total')");

    $nuevoStock = $stockActual - $cantidad;

    mysqli_query($conexion, "UPDATE Producto
                             SET stock = '$nuevoStock'
                             WHERE idProducto = '$idProducto'");

    header("Location: index.php");
    exit();
}

// 3. Consultar estadísticas del Dashboard

// Consulta Ventas (Filtro estricto para que empiece de cero cada mañana)
$consulta_ventas = mysqli_query($conexion, "SELECT SUM(total) as total_dia FROM venta WHERE DATE(fecha) = CURDATE()");
$datos_ventas = mysqli_fetch_assoc($consulta_ventas);
$total_dia = $datos_ventas['total_dia'] ?? 0;

// Consulta Alertas de Stock 
$consulta_stock = mysqli_query($conexion, "SELECT COUNT(*) as alertas FROM producto WHERE stock < 10");
$datos_stock = mysqli_fetch_assoc($consulta_stock);
$alertas_stock = $datos_stock['alertas'] ?? 0;

// CORRECCIÓN CLIENTES: Contamos directo de tu tabla real 'cliente'
$id_clientes = mysqli_query($conexion, "SELECT COUNT(*) as total_clientes FROM cliente");
$datos_clientes = mysqli_fetch_assoc($id_clientes);
$total_clientes = $datos_clientes['total_clientes'] ?? 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SuperMarket - Sistema de Gestión</title>
    <link rel="stylesheet" href="./styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <div class="dashboard-container">
        <?php include("sidebar.php"); ?>

        <main class="main-content">
            <header class="top-header">
                <div class="header-title">
                    <h1>Panel de Control Principal</h1>
                    <p>Automatización de procesos operativos en tiempo real</p>
                </div>
                <div class="user-profile">
                    <span class="user-role" style="background: #e100ff; color: white; padding: 5px 12px; border-radius: 20px; font-weight: bold; font-size: 0.85rem;">
                        <i class="fa-solid fa-user-shield"></i> <?php echo $_SESSION['rol']; ?>
                    </span>
                    <i class="fa-solid fa-bell icon-badge"></i>
                </div>
            </header>

            <section class="cards-grid">
                <div class="card">
                    <div class="card-icon sales-icon">
                        <i class="fa-solid fa-scale-balanced"></i>
                    </div>
                    <div class="card-data">
                        <h3>Ventas del Día</h3>
                        <p class="number">$<?php echo number_format($total_dia, 0, ',', '.'); ?></p>
                        <span class="trend positive"><i class="fa-solid fa-arrow-up"></i> En tiempo real</span>
                    </div>
                </div>

                <div class="card" id="alerta-stock-card">
                    <div class="card-icon inventory-icon">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <div class="card-data">
                        <h3>Alertas de Stock</h3>
                        <p class="number"><?php echo $alertas_stock; ?> Productos</p>
                        <span class="trend negative">Bajo el límite mínimo</span>
                    </div>
                </div>

                <div class="card">
                    <div class="card-icon clients-icon">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <div class="card-data">
                        <h3>Clientes en Sistema</h3>
                        <p class="number"><?php echo $total_clientes; ?></p>
                        <span class="trend positive">Registrados</span>
                    </div>
                </div>
            </section>

            <section class="quick-actions">
                <div class="action-box">
                    <h2><i class="fa-solid fa-bolt"></i> Accesos Directos Operativos</h2>
                    <hr>
                    <div class="buttons-group">
                        <a href="ventas.php" class="btn btn-primary" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px; justify-content: center;">
                            <i class="fa-solid fa-cart-plus"></i> Nueva Venta (Facturar)
                        </a>
                        
                        <?php if (isset($_SESSION['rol']) && strtolower($_SESSION['rol']) !== 'empleado') { ?>
                            <a href="productos/registrar.php" class="btn btn-secondary"><i class="fas fa-plus"></i> Registrar Producto</a>
                            <a href="reporte_diario.php" class="btn btn-success" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px; justify-content: center;">
                            <i class="fa-solid fa-file-arrow-down"></i> Descargar Reporte Diario
                            </a>
                        <?php } ?>
                    </div>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
