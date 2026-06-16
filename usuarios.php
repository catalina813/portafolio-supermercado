<?php
// 1. Activamos el búfer y la sesión en la parte superior de forma limpia
ob_start();
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

// 🛡️ CANDADO 1: Si no hay sesión, al login
if (!isset($_SESSION['nombre_usuario'])) {
    header("Location: login.php");
    exit();
}

// 🛡️ CANDADO 2: Si no es Administrador, lo saca al index (un empleado no puede crear usuarios)
if ($_SESSION['rol'] != 'Administrador') {
    echo "<script>alert('❌ Acceso denegado. Solo los administradores pueden gestionar usuarios.'); window.location.href='index.php';</script>";
    exit();
}

include("conexion.php");

// 📥 PROCESAR EL FORMULARIO CUANDO SE DA CLIC EN GUARDAR
if (isset($_POST['guardar_usuario'])) {
    $cedula = trim($_POST['cedula']); 
    $nombre = trim($_POST['nombre']);
    $telefono = trim($_POST['telefono']);
    $correo = trim($_POST['correo']);
    $contrasena_plana = trim($_POST['contrasena']); 
    $contrasena = password_hash($contrasena_plana, PASSWORD_DEFAULT); // Cifrado seguro
    $rol = $_POST['rol'];

    // 1. Insertar en la tabla persona (en minúscula para el servidor de producción)
    $sql_persona = "INSERT INTO persona (idPersona, nombre, telefono) VALUES ('$cedula', '$nombre', '$telefono')";
    
    if (mysqli_query($conexion, $sql_persona)) {
        
        // 2. Insertar en la tabla usuario (idUsuario se genera automático por el A_I que activamos)
        $sql_usuario = "INSERT INTO usuario (nombreUsuario, contrasena, rol, idPersona) 
                        VALUES ('$correo', '$contrasena', '$rol', '$cedula')";
        
        if (mysqli_query($conexion, $sql_usuario)) {
            echo "<script>alert('✅ Usuario registrado con éxito.'); window.location.href='usuarios.php';</script>";
            exit();
        } else {
            echo "Error en tabla usuario: " . mysqli_error($conexion);
        }
    } else {
        echo "Error en tabla persona: " . mysqli_error($conexion);
    }
}

// 🔍 CONSULTAR LOS USUARIOS EXISTENTES UNIENDO LAS DOS TABLAS (Usando 'persona' en minúscula)
$consulta_usuarios = mysqli_query($conexion, "SELECT u.idUsuario, p.nombre, u.nombreUsuario, u.rol, p.telefono FROM usuario u INNER JOIN persona p ON u.idPersona = p.idPersona") or die("Error al consultar usuarios: " . mysqli_error($conexion));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SuperMarket - Gestión Usuarios</title>
    <link rel="stylesheet" href="./styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Estilos rápidos para acomodar el formulario y la tabla de usuarios */
        .grid-usuarios { display: grid; grid-template-columns: 1fr 2fr; gap: 20px; margin-top: 20px; }
        .form-box, .table-box { background: #4e4e50; padding: 20px; border-radius: 12px; color: white; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; color: #ffffff; font-size: 0.9rem; }
        .form-control { width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #959494; background: #333338; color: white; box-sizing: border-box; }
        .btn-fucsia { background: #e100ff; color: white; border: none; padding: 12px; width: 100%; border-radius: 6px; font-weight: bold; cursor: pointer; transition: 0.3s; }
        .btn-fucsia:hover { background: #b800cf; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #444; }
        th { color: #e100ff; }
        .badge-rol { padding: 4px 8px; border-radius: 12px; font-size: 0.8rem; font-weight: bold; }
        .admin-badge { background: rgba(225, 0, 255, 0.2); color: #e100ff; }
        .empleado-badge { background: rgba(0, 200, 255, 0.2); color: #00c8ff; }
    </style>
</head>
<body>

    <div class="dashboard-container">
        <?php include("sidebar.php"); ?>
        <main class="main-content">
            <header class="top-header">
                <div class="header-title">
                    <h1>Gestión de Usuarios del Sistema</h1>
                    <p>Registra y controla los accesos de Administradores y Empleados</p>
                </div>
            </header>

            <div class="grid-usuarios">
                <div class="form-box">
                    <h3 style="color: #e100ff; margin-top:0;"><i class="fa-solid fa-user-plus"></i> Nuevo Usuario</h3>
                    <hr style="border-color: #444;">
                    <form method="POST">
                        <div class="form-group">
                            <label>Cédula / Identificación:</label>
                            <input type="text" name="cedula" maxlength="10" pattern="[0-9]+" class="form-control" placeholder="Ej. 10203040" required>
                        </div>
                        <div class="form-group">
                            <label>Nombre Completo:</label>
                            <input type="text" name="nombre" maxlength="80" pattern="^[A-Za-zÑñÁáÉéÍíÓóÚúÜü\s]+$" onkeypress="return soloLetras(event)"class="form-control" placeholder="Ej. Natalia Medina" required>
                        </div>
                        <div class="form-group">
                            <label>Teléfono:</label>
                            <input type="text" name="telefono" maxlength="10" pattern="[0-9]+" class="form-control" placeholder="Ej. 3124567890">
                        </div>
                        <div class="form-group">
                            <label>Correo Electrónico (Usuario):</label>
                            <input type="email" name="correo" class="form-control" placeholder="usuario@gmail.com" required>
                        </div>
                        <div class="form-group">
                            <label>Contraseña de Acceso:</label>
                            <input type="password" name="contrasena" maxlength="30" class="form-control" placeholder="••••••••" required>
                        </div>
                        <div class="form-group">
                            <label>Rol / Permisos:</label>
                            <select name="rol" class="form-control" required>
                                <option value="Empleado">Empleado / Cajero</option>
                                <option value="Administrador">Administrador</option>
                            </select>
                        </div>
                        <button type="submit" name="guardar_usuario" class="btn-fucsia">
                            <i class="fa-solid fa-save"></i> Guardar Cuenta
                        </button>
                    </form>
                </div>

                <div class="table-box">
                    <h3 style="color: #00c8ff; margin-top:0;"><i class="fa-solid fa-users-viewfinder"></i> Cuentas Activas</h3>
                    <hr style="border-color: #444;">
                    <table>
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Usuario / Correo</th>
                                <th>Rol</th>
                                <th>Teléfono</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($user = mysqli_fetch_assoc($consulta_usuarios)) { ?>
                            <tr>
                                <td><?php echo $user['nombre']; ?></td>
                                <td><?php echo $user['nombreUsuario']; ?></td>
                                <td>
                                    <span class="badge-rol <?php echo ($user['rol'] == 'Administrador') ? 'admin-badge' : 'empleado-badge'; ?>">
                                        <?php echo $user['rol']; ?>
                                    </span>
                                </td>
                                <td><?php echo $user['telefono']; ?></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <script>
            function soloNumeros(e) {
                var key = e.keyCode || e.which;
                var teclado = String.fromCharCode(key);
                var numeros = "0123456789";
                var especiales = [8, 9, 37, 39, 46]; 
                var tecla_especial = false;

                for (var i in especiales) {
                    if (key == especiales[i]) {
                        tecla_especial = true;
                        break;
                    }
                }

                if (numeros.indexOf(teclado) == -1 && !tecla_especial) {
                    return false;
                }
            }
            </script>
        </main>
    </div>
</body>
</html>
<?php
ob_end_flush();
?>
