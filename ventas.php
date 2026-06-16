<?php
// 1. Unificamos el búfer y el inicio de sesión en la cima de forma limpia
ob_start();
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

include("conexion.php");

// Candado de seguridad: si no hay sesión activa, al login directo
if (!isset($_SESSION['nombre_usuario'])) {
    header("Location: login.php");
    exit();
}

// 2. Cargamos los productos usando el nombre en minúscula 'producto'
$consulta_productos = mysqli_query($conexion, "SELECT * FROM producto WHERE stock > 0") 
                      or die("❌ Error al cargar productos: " . mysqli_error($conexion));

if (isset($_POST['procesar_venta'])) {
    $idProducto = $_POST['idProducto'];
    $cantidad = $_POST['cantidad'];

    // 3. Buscamos el precio y stock actual en 'producto' (en minúscula)
    $producto_req = mysqli_query($conexion, "SELECT precio, stock FROM producto WHERE idProducto = '$idProducto'") 
                    or die("❌ Error al buscar en producto: " . mysqli_error($conexion));
    $producto = mysqli_fetch_assoc($producto_req);

    if ($producto) {
        $stockActual = $producto['stock'];
        
        // Validación manual estricta de stock antes de operar
        if ($stockActual < $cantidad) {
            echo "<script>alert('❌ Error: Stock insuficiente. Solo quedan {$stockActual} unidades.'); window.location.href='ventas.php';</script>";
            exit();
        }

        $precio_venta = $producto['precio'];
        $total = $precio_venta * $cantidad;

        // 4. Insertamos la venta principal usando la tabla 'venta' en minúscula
        $sql_venta = "INSERT INTO venta (fecha, total) VALUES (NOW(), '$total')";
        $resultado_venta = mysqli_query($conexion, $sql_venta) 
                           or die("❌ Error crítico en tabla venta: " . mysqli_error($conexion));
        
        if ($resultado_venta) {
            $idVenta = mysqli_insert_id($conexion);

            // 5. Insertamos el desglose usando 'detalle_venta' en minúscula
            // Nota: Agregué la estructura común de detalle_venta (idVenta, idProducto, cantidad, subtotal)
            $sql_detalle = "INSERT INTO detalle_venta (idVenta, idProducto, cantidad, subtotal) 
                            VALUES ('$idVenta', '$idProducto', '$cantidad', '$total')";
            $resultado_detalle = mysqli_query($conexion, $sql_detalle);
            
            // Si tu tabla de detalle maneja la columna 'precioUnitario', intentamos con esta variante:
            if (!$resultado_detalle) {
                $sql_detalle = "INSERT INTO detalle_venta (idVenta, idProducto, cantidad, precioUnitario, subtotal) 
                                VALUES ('$idVenta', '$idProducto', '$cantidad', '$precio_venta', '$total')";
                mysqli_query($conexion, $sql_detalle) or die("❌ Error crítico en detalle_venta: " . mysqli_error($conexion));
            }

            // 6. Restamos el stock en la tabla 'producto' en minúscula
            $nuevo_stock = $stockActual - $cantidad;
            mysqli_query($conexion, "UPDATE producto SET stock = '$nuevo_stock' WHERE idProducto = '$idProducto'") 
            or die("❌ Error al actualizar el STOCK: " . mysqli_error($conexion));

            // Redirección limpia al index para ver los cambios reflejados
            header("Location: index.php");
            exit();
        }
    } else {
        echo "<script>alert('❌ Error: El producto seleccionado no existe.'); window.location.href='ventas.php';</script>";
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SuperMarket - Procesar Venta</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .form-container {
            background: #242333; 
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.02);
            max-width: 600px;
            margin: 40px auto 0 auto;
            color: white;
        }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #b3b3b3; }
        .form-control { 
            width: 100%; 
            padding: 12px; 
            border-radius: 8px; 
            border: 1px solid #444; 
            background-color: #1a1924; 
            color: white; 
            font-size: 0.95rem; 
            outline: none; 
            box-sizing: border-box;
        }
        .form-control option {
            background-color: #1a1924;
            color: white;
        }
        .btn-fucsia {
            width: 100%; 
            background-color: #e100ff; 
            color: white; 
            border: none; 
            padding: 12px; 
            border-radius: 8px; 
            font-weight: bold; 
            font-size: 1rem; 
            cursor: pointer; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            gap: 10px; 
            transition: 0.3s;
        }
        .btn-fucsia:hover {
            background-color: #b800cf;
        }
    </style>
</head>
<body>

    <div class="dashboard-container">
        
        <?php include("sidebar.php"); ?>

        <main class="main-content">
            <header class="top-header">
                <div class="header-title">
                    <h1>Procesar Nueva Venta</h1>
                    <p>Registra la salida de productos y calcula el total de la compra</p>
                </div>
            </header>

            <div class="form-container">
                <form method="POST" action="">
                    <div class="form-group">
                        <label>Seleccionar Producto:</label>
                        <select name="idProducto" class="form-control" required>
                            <option value="" disabled selected>-- Elige un artículo --</option>
                            <?php while($prod = mysqli_fetch_assoc($consulta_productos)) { ?>
                                <option value="<?php echo $prod['idProducto']; ?>">
                                    <?php echo $prod['nombre']; ?> (Stock: <?php echo $prod['stock']; ?>u)
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Cantidad a Vender:</label>
                        <input type="number" name="cantidad" class="form-control" min="1" placeholder="Ej. 2" required>
                    </div>

                    <button type="submit" name="procesar_venta" class="btn-fucsia">
                        <i class="fa-solid fa-cart-shopping"></i> Registrar y Descontar Venta
                    </button>
                </form>
            </div>
        </main>
    </div>

</body>
</html>
<?php 
ob_end_flush(); 
?>
