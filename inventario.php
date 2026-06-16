<?php
// 1. Unificamos el búfer y el inicio de sesión en la cima de forma limpia y única
ob_start();
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

include("conexion.php");

// Candado de seguridad: si no hay sesión activa o NO es Administrador, al index
if (!isset($_SESSION['nombre_usuario']) || $_SESSION['rol'] !== 'Administrador') {
    header("Location: index.php");
    exit();
}

// 2. Consultar TODOS los productos uniendo la tabla categoria en minúsculas para el servidor
$query = "SELECT p.idProducto, p.nombre, p.descripcion, p.precio, p.stock, c.nombre AS categoria 
          FROM producto p 
          INNER JOIN categoria c ON p.idCategoria = c.idCategoria 
          ORDER BY p.idProducto DESC";

$resultado = mysqli_query($conexion, $query) or die("❌ Error en la consulta de inventario: " . mysqli_error($conexion));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SuperMarket - Inventario</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Estilos específicos para la tabla de inventario */
        .table-container {
            background: #242333; /* Fondo oscuro que hace juego con el Dashboard */
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.02);
            margin-top: 20px;
            color: white;
        }
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            text-align: left;
        }
        .main-table th {
            background-color: #1a1924;
            color: #e100ff; /* Letras fucsias para los encabezados de tabla */
            padding: 12px;
            font-size: 0.95rem;
        }
        .main-table td {
            padding: 12px;
            border-bottom: 1px solid #444;
            font-size: 0.9rem;
            color: #e0e0e0;
        }
        .main-table tr:hover {
            background-color: #2c2b3d;
        }
        .badge-stock {
            padding: 4px 8px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.8rem;
        }
        .stock-ok { background: rgba(0, 230, 115, 0.2); color: #00e673; }
        .stock-low { background: rgba(255, 74, 74, 0.2); color: #ff4a4a; }
        
        /* Botón de registrar con tus tonos fucsias */
        .btn-fucsia-top {
            background: #e100ff;
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: bold;
            transition: 0.3s;
        }
        .btn-fucsia-top:hover {
            background: #b800cf;
        }
    </style>
</head>
<body>

    <div class="dashboard-container">
        
        <?php include("sidebar.php"); ?>

        <main class="main-content">
            <header class="top-header">
                <div class="header-title">
                    <h1>Control de Inventario y Stock</h1>
                    <p>Lista general de mercancía registrada en la base de datos</p>
                </div>
                <a href="productos/registrar.php" class="btn-fucsia-top" style="text-decoration: none;">
                    <i class="fa-solid fa-plus"></i> Registrar Producto
                </a>
            </header>

            <div class="table-container">
                <table class="main-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Producto</th>
                            <th>Categoría</th>
                            <th>Descripción</th>
                            <th>Precio (COP)</th>
                            <th>Stock</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // Recorrer los registros traídos de la base de datos
                        while($row = mysqli_fetch_assoc($resultado)) { 
                            // Lógica visual: Si tiene menos de 10 unidades, marcar en rojo
                            $clase_stock = ($row['stock'] < 10) ? 'stock-low' : 'stock-ok';
                            $estado_texto = ($row['stock'] < 10) ? 'Bajo Stock' : 'Disponible';
                        ?>
                        <tr>
                            <td style="color: #b3b3b3;"><?php echo $row['idProducto']; ?></td>
                            <td><strong><?php echo $row['nombre']; ?></strong></td> 
                            <td><?php echo $row['categoria']; ?></td>
                            <td><?php echo $row['descripcion']; ?></td>
                            <td>$<?php echo number_format($row['precio'], 0, ',', '.'); ?></td>
                            <td><strong><?php echo $row['stock']; ?> u</strong></td>
                            <td><span class="badge-stock <?php echo $clase_stock; ?>"><?php echo $estado_texto; ?></span></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
<?php 
ob_end_flush(); 
?>
