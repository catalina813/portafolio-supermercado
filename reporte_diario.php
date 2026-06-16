<?php
session_start();

// 1. Candado de seguridad: solo entra el Administrador
if (!isset($_SESSION['nombre_usuario']) || $_SESSION['rol'] !== 'Administrador') {
    die("❌ No tienes permisos para generar este reporte.");
}

include("conexion.php");

// 2. Cambiamos los encabezados para que el navegador entienda que es una DESCARGA directa
header("Content-Type: text/plain; charset=utf-8");
header("Content-Disposition: attachment; filename=Reporte_Ventas_".date('Y-m-d').".txt");
header("Pragma: no-cache");
header("Expires: 0");

// 3. Consultar las ventas del día cruzando tus tablas reales
$consulta = mysqli_query($conexion, "
    SELECT v.idVenta, v.fecha, v.total 
    FROM venta v 
    WHERE DATE(v.fecha) = CURDATE()
    ORDER BY v.idVenta DESC
");

// 4. Diseñar el cuerpo del reporte en bloc de notas
echo "==================================================\n";
echo "           SUPERMARKET - REPORTE DIARIO          \n";
echo "           Fecha del Reporte: " . date('d/m/Y') . "    \n";
echo "==================================================\n\n";

echo "ID VENTA | HORA     | TOTAL FACTURADO \n";
echo "--------------------------------------------------\n";

$gran_total = 0;
$contador_ventas = 0;

if ($consulta && mysqli_num_rows($consulta) > 0) {
    while ($fila = mysqli_fetch_assoc($consulta)) {
        $hora = date('H:i:s', strtotime($fila['fecha']));
        echo "#" . str_pad($fila['idVenta'], 7) . " | " . $hora . " | $" . number_format($fila['total'], 0, ',', '.') . "\n";
        $gran_total += $fila['total'];
        $contador_ventas++;
    }
} else {
    echo "   No se registraron movimientos comerciales hoy.\n";
}

echo "--------------------------------------------------\n";
echo "Total de facturas emitidas: " . $contador_ventas . "\n";
echo "DINERO TOTAL RECAUDADO HOY: $" . number_format($gran_total, 0, ',', '.') . "\n";
echo "==================================================\n";
echo "Generado automáticamente por el Panel de Control.";
exit();
?>
