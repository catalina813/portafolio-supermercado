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

// 2. Lógica simple para Registrar el Proveedor en la Base de Datos
if (isset($_POST['registrar_proveedor'])) {
    $nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
    $contacto = mysqli_real_escape_string($conexion, $_POST['contacto']);
    $telefono = mysqli_real_escape_string($conexion, $_POST['telefono']);

    // Inserta directo en la tabla independiente sin tocar productos
    $sql = "INSERT INTO proveedor (nombre, contacto, telefono) VALUES ('$nombre', '$contacto', '$telefono')";
    if (mysqli_query($conexion, $sql)) {
        header("Location: proveedores.php");
        exit();
    } else {
        echo "<script>alert('❌ Error al registrar: " . mysqli_error($conexion) . "');</script>";
    }
}

// 3. Consulta simple para listarlos en la tabla de la derecha
$consulta_proveedores = mysqli_query($conexion, "SELECT * FROM proveedor ORDER BY nombre ASC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SuperMarket - Proveedores</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .flex-container { display: flex; gap: 20px; margin-top: 20px; align-items: flex-start; }
        .form-container { background: #515052; padding: 25px; border-radius: 12px; width: 35%; color: white; }
        .table-container { background: #666668; padding: 25px; border-radius: 12px; width: 65%; color: white; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 6px; color: #f3efef; font-size: 0.9rem; }
        .form-control { width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #444; background: #3a3942; color: white; box-sizing: border-box; }
        .btn-fucsia { width: 100%; background: #e100ff; color: white; border: none; padding: 12px; border-radius: 6px; font-weight: bold; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; }
        .main-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .main-table th { background-color: #3e3e42; color: #e100ff; padding: 12px; text-align: left; font-size: 0.9rem; }
        .main-table td { padding: 12px; border-bottom: 1px solid #444; font-size: 0.85rem; color: #e0e0e0; }
    </style>
</head>
<body>

    <div class="dashboard-container">
        <?php include("sidebar.php"); ?>

        <main class="main-content">
            <header class="top-header">
                <div class="header-title">
                    <h1>Gestión de Proveedores</h1>
                    <p>Directorio y agenda de contactos de empresas aliadas</p>
                </div>
            </header>

            <div class="flex-container">
                <div class="form-container">
                    <h3 style="color: #e100ff; margin-top: 0; display:flex; align-items:center; gap:8px;">
                        <i class="fa-solid fa-truck"></i> Nuevo Proveedor
                    </h3>
                    <form method="POST" action="">
                        <div class="form-group">
                            <label>Nombre de la Empresa:</label>
                            <input type="text" name="nombre" class="form-control" placeholder="Ej. Colanta S.A." required>
                        </div>
                        <div class="form-group">
                            <label>Nombre del Asesor / Contacto:</label>
                            <input type="text" name="contacto" maxlength="50" pattern="^[A-Za-zÑñÁáÉéÍíÓóÚúÜü\s]+$"onkeypress="return soloLetras(event)" class="form-control" placeholder="Ej. Carlos Mendoza">
                        </div>
                        <div class="form-group">
                            <label>Teléfono (Máx. 10 dígitos):</label>
                            <input type="text" name="telefono" class="form-control" placeholder="Ej. 3154433221" maxlength="10" pattern="[0-9]+" title="Solo se permiten números">
                        </div>
                        <button type="submit" name="registrar_proveedor" class="btn-fucsia">
                            <i class="fa-solid fa-floppy-disk"></i> Guardar Proveedor
                        </button>
                    </form>
                </div>

                <div class="table-container">
                    <h3 style="color: #00e673; margin-top: 0; display:flex; align-items:center; gap:8px;">
                        <i class="fa-solid fa-list"></i> Proveedores Registrados
                    </h3>
                    <table class="main-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Empresa</th>
                                <th>Contacto</th>
                                <th>Teléfono</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($consulta_proveedores) > 0) {
                                while($row = mysqli_fetch_assoc($consulta_proveedores)) { ?>
                                <tr>
                                    <td><?php echo $row['idProveedor']; ?></td>
                                    <td><strong><?php echo $row['nombre']; ?></strong></td>
                                    <td><?php echo $row['contacto'] ? $row['contacto'] : '<span style="color:#777;">N/A</span>'; ?></td>
                                    <td><?php echo $row['telefono'] ? $row['telefono'] : '<span style="color:#777;">N/A</span>'; ?></td>
                                </tr>
                            <?php } } else { ?>
                                <tr><td colspan="4" style="text-align: center; color: #777; padding: 20px;">No hay proveedores guardados.</td></tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

</body>
</html>
<?php ob_end_flush(); ?>
