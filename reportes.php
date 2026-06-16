<?php
ob_start();
session_start();

// Si no ha iniciado sesión o NO es Administrador, lo saca patitas pa' la calle hacia el index
if (!isset($_SESSION['nombre_usuario']) || $_SESSION['rol'] !== 'Administrador') {
    header("Location: index.php");
    exit();
}

include("conexion.php");
// ... resto de tu código normal ...

// 2. CONSULTAS AUTOMÁTICAS PARA LOS REPORTES

// Dinero total recaudado sumando la columna 'total' de la tabla venta
// 2. CONSULTAS AUTOMÁTICAS PARA LOS REPORTES (FILTRADAS PARA QUE EMPIECEN DESDE CERO CADA DÍA)

// Dinero total recaudado sumando solo las ventas del día de hoy
$query_total = mysqli_query($conexion, "SELECT SUM(total) as gran_total FROM venta WHERE DATE(fecha) = CURDATE()");
$res_total = mysqli_fetch_assoc($query_total);
$total_sistema = $res_total['gran_total'] ?? 0;

// Cantidad de facturas o ventas emitidas únicamente el día de hoy
$query_cantidad = mysqli_query($conexion, "SELECT COUNT(*) as total_ventas FROM venta WHERE DATE(fecha) = CURDATE()");
$res_cantidad = mysqli_fetch_assoc($query_cantidad);
$cantidad_ventas = $res_cantidad['total_ventas'] ?? 0;

// Historial unificado del día: Muestra solo qué se ha vendido HOY
$query_historial = mysqli_query($conexion, "
    SELECT v.idVenta, v.fecha, v.total, p.nombre as producto, dv.cantidad 
    FROM venta v
    INNER JOIN detalle_venta dv ON v.idVenta = dv.idVenta
    INNER JOIN producto p ON dv.idProducto = p.idProducto
    WHERE DATE(v.fecha) = CURDATE()
    ORDER BY v.fecha DESC
");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SuperMarket - Reportes</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .cards-container { display: flex; gap: 20px; margin-top: 20px; }
        .report-card { 
            background: #48484d; padding: 25px; border-radius: 12px; width: 50%; 
            color: white; display: flex; align-items: center; gap: 20px; 
            box-shadow: 0 4px 6px rgba(0,0,0,0.1); 
        }
        .report-card i { font-size: 2.5rem; }
        .report-card h3 { margin: 0; font-size: 1.8rem; color: #fff; }
        .report-card p { margin: 5px 0 0 0; color: #f2efef; font-size: 0.9rem; }
        
        .table-container { 
            background: #434347; padding: 25px; border-radius: 12px; 
            margin-top: 20px; color: white; box-shadow: 0 4px 6px rgba(0,0,0,0.1); 
        }
        .main-table { width: 100%; border-collapse: collapse; margin-top: 15px; text-align: left; }
        .main-table th { background-color: #1a1924; color: #e100ff; padding: 12px; font-size: 0.9rem; }
        .main-table td { padding: 12px; border-bottom: 1px solid #444; font-size: 0.85rem; color: #eaeaea; }
        .main-table tr:hover { background-color: #2c2b3d; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <?php include("sidebar.php"); ?>

        <main class="main-content">
            <header class="top-header">
                <div class="header-title">
                    <h1>Reportes y Estadísticas</h1>
                    <p>Monitoreo de ingresos y transacciones comerciales en tiempo real</p>
                </div>
            </header>

            <div class="cards-container">
                <div class="report-card" style="border-left: 5px solid #e100ff;">
                    <i class="fa-solid fa-hand-holding-dollar" style="color: #e100ff;"></i>
                    <div>
                        <h3>$<?php echo number_format($total_sistema, 2); ?></h3>
                        <p>Total Ingresos Recaudados</p>
                    </div>
                </div>
                
                <div class="report-card" style="border-left: 5px solid #00e673;">
                    <i class="fa-solid fa-receipt" style="color: #00e673;"></i>
                    <div>
                        <h3><?php echo $cantidad_ventas; ?></h3>
                        <p>Facturas Emitidas</p>
                    </div>
                </div>
            </div>

            <div class="table-container">
                <h3 style="color: #e100ff; margin-top: 0; display:flex; align-items:center; gap:8px;">
                    <i class="fa-solid fa-history"></i> Historial Detallado de Transacciones
                </h3>
                <table class="main-table">
                    <thead>
                        <tr>
                            <th>ID Venta</th>
                            <th>Fecha / Hora</th>
                            <th>Producto Vendido</th>
                            <th>Cantidad</th>
                            <th>Total Pagado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($query_historial) > 0) {
                            while($row = mysqli_fetch_assoc($query_historial)) { ?>
                            <tr>
                                <td>#<?php echo $row['idVenta']; ?></td>
                                <td><?php echo $row['fecha']; ?></td>
                                <td><strong><?php echo $row['producto']; ?></strong></td>
                                <td><?php echo $row['cantidad']; ?> u</td>
                                <td style="color: #00e673; font-weight: bold;">$<?php echo number_format($row['total'], 2); ?></td>
                            </tr>
                        <?php } } else { ?>
                            <tr>
                                <td colspan="5" style="text-align: center; color: #bfb2b2; padding: 20px;">
                                    Aún no se registran movimientos ni ventas en el sistema.
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
<?php ob_end_flush(); ?>
