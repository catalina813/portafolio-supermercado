<?php
ob_start();
session_start();

// Candado de seguridad
if (!isset($_SESSION['nombre_usuario'])) {
    header("Location: login.php");
    exit();
}

include("conexion.php");

// 1. REGISTRAR CLIENTE NUEVO
if (isset($_POST['registrar_cliente'])) {
    $cedula = mysqli_real_escape_string($conexion, $_POST['cedula']);
    $nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
    $telefono = mysqli_real_escape_string($conexion, $_POST['telefono']);
    $tipo = mysqli_real_escape_string($conexion, $_POST['tipo_cliente'] ?? 'Regular');

    // Verificar si ya existe esa cédula para no duplicar
    $check = mysqli_query($conexion, "SELECT * FROM cliente WHERE cedula = '$cedula'");
    if (mysqli_num_rows($check) > 0) {
        echo "<script>alert('❌ Esta cédula ya se encuentra registrada.');</script>";
    } else {
        // Insertar en la nueva tabla cliente
        $sql_insert = "INSERT INTO cliente (cedula, nombre_completo, telefono, tipo_cliente) 
                       VALUES ('$cedula', '$nombre', '$telefono', '$tipo')";
        
        if (mysqli_query($conexion, $sql_insert)) {
            // Redireccionar para limpiar el formulario y refrescar la lista
            header("Location: clientes.php");
            exit();
        } else {
            echo "<script>alert('❌ Error al registrar cliente: " . mysqli_error($conexion) . "');</script>";
        }
    }
}

// 2. CONSULTAR CLIENTES PARA LA TABLA DE LA DERECHA
$consulta_clientes = mysqli_query($conexion, "SELECT * FROM cliente ORDER BY nombre_completo ASC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SuperMarket - Gestión Clientes</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .flex-container {
            display: flex;
            gap: 20px;
            margin-top: 20px;
            align-items: flex-start;
        }
        /* Formulario Izquierda */
        .form-container {
            background: #242333;
            padding: 25px;
            border-radius: 12px;
            width: 35%;
            color: white;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        /* Tabla Derecha */
        .table-container {
            background: #414146;
            padding: 25px;
            border-radius: 12px;
            width: 65%;
            color: white;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 6px; color: #f8f8f8; font-size: 0.9rem; }
        .form-control { 
            width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #d8cdcd; 
            background: #1a1924; color: white; outline: none; box-sizing: border-box;
        }
        .btn-fucsia {
            width: 100%; background: #e100ff; color: white; border: none; padding: 12px;
            border-radius: 6px; font-weight: bold; cursor: pointer; display: flex; 
            align-items: center; justify-content: center; gap: 8px; transition: 0.3s;
        }
        .btn-fucsia:hover { background: #b800cf; }
        
        /* Estilos de la Tabla */
        .main-table { width: 100%; border-collapse: collapse; margin-top: 15px; text-align: left; }
        .main-table th { background-color: #1a1924; color: #e100ff; padding: 12px; font-size: 0.9rem; }
        .main-table td { padding: 12px; border-bottom: 1px solid #444; font-size: 0.85rem; color: #e0e0e0; }
        .main-table tr:hover { background-color: #2c2b3d; }
    </style>
</head>
<body>

    <div class="dashboard-container">
        
        <?php include("sidebar.php"); ?>

        <main class="main-content">
            <header class="top-header">
                <div class="header-title">
                    <h1>Gestión de Clientes</h1>
                    <p>Fidelización y registro de compradores en el sistema</p>
                </div>
            </header>

           <div class="flex-container">
    <div class="form-container">
        <h3 style="color: #e100ff; margin-top: 0; display:flex; align-items:center; gap:8px;">
            <i class="fa-solid fa-user-plus"></i> Nuevo Cliente
        </h3>
        <form method="POST" action="">
            <div class="form-group">
                <label>Cédula del Cliente:</label>
                <input type="text" name="cedula" class="form-control" placeholder="Ej. 10405060" required
                       maxlength="10" 
                       pattern="[0-9]+" 
                       inputmode="numeric" 
                       title="Solo se permiten números (Máx. 10 dígitos)">
            </div>
            
            <div class="form-group">
                <label>Nombre Completo:</label>
                <input type="text" name="nombre" class="form-control" placeholder="Ej. Catalina" required
                       maxlength="80"
                       title="El nombre no puede superar los 80 caracteres">
            </div>
            
            <div class="form-group">
                <label>Teléfono de Contacto:</label>
                <input type="text" name="telefono" class="form-control" placeholder="Ej. 3007654321"
                       maxlength="10" 
                       pattern="[0-9]+" 
                       inputmode="numeric" 
                       title="Solo se permiten números (Máx. 10 dígitos)">
            </div>
            
            <div class="form-group">
                <label>Tipo de Cliente:</label>
                <select name="tipo_cliente" class="form-control">
                    <option value="Regular">Regular</option>
                    <option value="Frecuente">Frecuente</option>
                    <option value="VIP">VIP</option>
                </select>
            </div>
            
            <button type="submit" name="registrar_cliente" class="btn-fucsia">
                <i class="fa-solid fa-floppy-disk"></i> Registrar Cliente
            </button>
        </form>
    </div>
                <div class="table-container">
                    <h3 style="color: #f700ff; margin-top: 0; display:flex; align-items:center; gap:8px;">
                        <i class="fa-solid fa-address-book"></i> Compradores Registrados
                    </h3>
                    <table class="main-table">
                        <thead>
                            <tr>
                                <th>Cédula</th>
                                <th>Nombre Completo</th>
                                <th>Teléfono</th>
                                <th>Tipo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if (mysqli_num_rows($consulta_clientes) > 0) {
                                while($row = mysqli_fetch_assoc($consulta_clientes)) { 
                            ?>
                                <tr>
                                    <td><?php echo $row['cedula']; ?></td>
                                    <td><strong><?php echo $row['nombre_completo']; ?></strong></td>
                                    <td><?php echo $row['telefono'] ? $row['telefono'] : '<span style="color:#777;">N/A</span>'; ?></td>
                                    <td>
                                        <span style="color: <?php echo $row['tipo_cliente'] == 'VIP' ? '#e100ff' : ($row['tipo_cliente'] == 'Frecuente' ? '#00e673' : '#fff'); ?>;">
                                            <?php echo $row['tipo_cliente']; ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php 
                                } 
                            } else { 
                            ?>
                                <tr>
                                    <td colspan="4" style="text-align: center; color: #777; padding: 20px;">
                                        No hay clientes registrados todavía.
                                    </td>
                                </tr>
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
