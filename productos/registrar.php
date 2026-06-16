<?php 
// 1. Activar el búfer para evitar errores de redirección
ob_start();
session_start(); // Es recomendable tenerlo por si usas el sidebar o necesitas proteger la ruta

error_reporting(E_ALL);
ini_set('display_errors', 1);

// CONEXIÓN (¡Excelente! Está muy bien el ../conexion.php para salir de la carpeta productos)
include("../conexion.php");

// Consulta para cargar categorías
$consulta_categorias = mysqli_query($conexion, "SELECT * FROM categoria");

// 🚀 CORRECCIÓN AQUÍ: Cambiado 'Producto' por 'producto' en minúscula
$consulta_nombres_productos = mysqli_query($conexion, "SELECT nombre FROM producto");

if(isset($_POST['guardar'])){

    $nombre = trim($_POST['nombre']); 
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $descripcion = $_POST['descripcion'];
    $idCategoria = $_POST['idCategoria']; 

    // 🔒 CANDADO 1: Validar números negativos
    if ($stock < 0 || $precio < 1) {
        echo "<script>
                alert('❌ Error: El precio debe ser mayor a 0 y la cantidad inicial no puede ser negativa.');
                window.history.back();
              </script>";
        exit();
    }
    


// ... aquí sigue tu código normal de inserción ...

    // 🔍 BUSCADOR INTELIGENTE: Ver si el producto ya existe (ignorando mayúsculas/minúsculas)
    $nombre_busqueda = mysqli_real_escape_string($conexion, strtolower($nombre));
    
    // 🚀 CORRECCIÓN 1: 'Producto' cambiado a 'producto' en minúscula
    $buscar_duplicado = mysqli_query($conexion, "SELECT * FROM producto WHERE LOWER(nombre) = '$nombre_busqueda'");
    
    if (mysqli_num_rows($buscar_duplicado) > 0) {
        // 🔄 ¡EXISTE! Traemos los datos actuales del producto que ya está en la base de datos
        $producto_viejo = mysqli_fetch_assoc($buscar_duplicado);
        $idProducto = $producto_viejo['idProducto'];
        $stock_actual = $producto_viejo['stock'];
        
        // Calculamos la nueva suma de mercancía
        $nuevo_stock = $stock_actual + $stock;

        // 🚀 CORRECCIÓN 2: 'Producto' cambiado a 'producto' en minúscula
        $sql_update = "UPDATE producto SET 
                       stock = '$nuevo_stock', 
                       precio = '$precio', 
                       descripcion = '$descripcion',
                       idCategoria = '$idCategoria'
                       WHERE idProducto = '$idProducto'";
                       
        $resultado_update = mysqli_query($conexion, $sql_update);

        if($resultado_update){
            echo "<script>
                    alert('🔄 ¡Stock actualizado! Se sumaron $stock unidades a \'$nombre\'. Nuevo stock: $nuevo_stock');
                    window.location.href = '../inventario.php';
                  </script>";
            exit();
        } else {
            echo "<p style='color: red;'>❌ Error al actualizar stock: " . mysqli_error($conexion) . "</p>";
        }

    } else {
        // 🆕 ¡NO EXISTE! Es un producto completamente nuevo, hacemos el INSERT normal
        // 🚀 CORRECCIÓN 3: 'Producto' cambiado a 'producto' en minúscula
        $sql_insert = "INSERT INTO producto(nombre, descripcion, precio, stock, idCategoria)
                       VALUES('$nombre', '$descripcion', '$precio', '$stock', '$idCategoria')";

        $resultado_insert = mysqli_query($conexion, $sql_insert);

        if($resultado_insert){
            header("Location: ../inventario.php");
            exit();
        } else {
            echo "<p style='color: red;'>❌ Error al guardar nuevo producto: " . mysqli_error($conexion) . "</p>";
        }
    }
}
ob_end_flush();
?>
ob_end_flush();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SuperMarket - Registrar Producto</title>
    <link rel="stylesheet" href="../styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .form-container {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.02);
            max-width: 600px;
            margin: 20px auto 0 auto;
        }
        .form-group { 
            margin-bottom: 20px; 
        }
        .form-group label { 
            display: block; 
            margin-bottom: 8px; 
            font-weight: 600; 
            color: #1e293b; 
        }
        .form-control { 
            width: 100%; 
            padding: 10px 12px; 
            border-radius: 8px; 
            border: 1px solid #cbd5e1; 
            font-size: 0.95rem; 
            outline: none;
        }
    </style>
</head>
<body>

    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="sidebar-logo">
                <i class="fa-solid fa-shop"></i>
                <h2>SuperMarket</h2>
            </div>
            <nav class="sidebar-menu">
                <a href="../index.php"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
                <a href="../ventas.php"><i class="fa-solid fa-cash-register"></i> Procesar Venta</a>
                <a href="../inventario.php" class="active"><i class="fa-solid fa-boxes-stacked"></i> Inventario / Stock</a>
                <a href="#"><i class="fa-solid fa-users"></i> Gestión Clientes</a>
                <a href="#" id="btn-proveedores"><i class="fa-solid fa-truck-field"></i> Proveedores</a>
                <a href="#" id="btn-reportes"><i class="fa-solid fa-file-invoice-dollar"></i> Reportes</a>
            </nav>
        </aside>

        <main class="main-content">
            <header class="top-header">
                <div class="header-title">
                    <h1>Añadir Nuevo Producto</h1>
                    <p>Registra la mercancía en el sistema de inventario</p>
                </div>
                <a href="../inventario.php" class="btn btn-secondary" style="text-decoration: none;">
                    <i class="fa-solid fa-arrow-left"></i> Volver al Inventario
                </a>
            </header>

            <div class="form-container">
                <form method="POST" action="">
                    
                   <div class="form-group">
                  <label>Nombre del Producto:</label>
                 <input type="text" name="nombre" id="nombre" class="form-control" placeholder="Ej. Arroz Diana 1kg" list="productos_existentes" required autocomplete="off">
    
                  <datalist id="productos_existentes">
                    <?php while($prod = mysqli_fetch_assoc($consulta_nombres_productos)) { ?>
                      <option value="<?php echo $prod['nombre']; ?>"></option>
                   <?php } ?>
                    </datalist>
                    </div>
                    <div class="form-group">
                        <label>Descripción / Presentación:</label>
                        <input type="text" name="descripcion" class="form-control" placeholder="Ej. Grano largo Vitamor">
                    </div>

                    <div class="form-group">
                        <label>Categoría del Producto:</label>
                        <select name="idCategoria" class="form-control" required style="background-color: #fff; height: 42px;">
                            <option value="" disabled selected>-- Selecciona una categoría --</option>
                            <?php while($cat = mysqli_fetch_assoc($consulta_categorias)) { ?>
                                <option value="<?php echo $cat['idCategoria']; ?>">
                                    <?php echo $cat['nombre']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Precio de Venta (COP):</label>
                        <input type="number" name="precio" class="form-control" min="0" placeholder="Ej. 3800" required>
                    </div>

                    <div class="form-group">
                         <label style="font-weight: 600; margin-bottom: 8px;">Cantidad Inicial (Stock):</label>
                         <input type="number" name="stock" id="stock" min="0" placeholder="Ej. 50" required class="form-control">
                    </div>

                    <button type="submit" name="guardar" class="btn btn-primary" style="width: 100%; justify-content: center; margin-top: 10px;">
                        <i class="fa-solid fa-floppy-disk"></i> Guardar Producto
                    </button>
                </form>
            </div>
        </main>
    </div>

</body>
</html>
