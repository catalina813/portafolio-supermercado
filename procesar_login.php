<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Conexión a la base de datos
require_once("conexion.php");

if (isset($_POST['ingresar'])) {

    $usuario = mysqli_real_escape_string($conexion, trim($_POST['usuario']));
    $clave = trim($_POST['clave']);

    // Buscar solo por usuario
    $consulta = "SELECT * FROM usuario WHERE nombreUsuario = '$usuario'";
    $resultado = mysqli_query($conexion, $consulta);

    if (!$resultado) {
        die("Error en SQL: " . mysqli_error($conexion));
    }

    if (mysqli_num_rows($resultado) == 1) {

        $datos_usuario = mysqli_fetch_assoc($resultado);

        // Verificar contraseña encriptada
        if (password_verify($clave, $datos_usuario['contrasena'])) {

            $_SESSION['idUsuario'] = $datos_usuario['idUsuario'];
            $_SESSION['nombre_usuario'] = $datos_usuario['nombreUsuario'];
            $_SESSION['rol'] = $datos_usuario['rol'];

            header("Location: index.php");
            exit();

        } else {

            echo "<script>
                    alert('Usuario o contraseña incorrectos');
                    window.location.href='login.php';
                  </script>";
        }

    } else {

        echo "<script>
                alert('Usuario o contraseña incorrectos');
                window.location.href='login.php';
              </script>";
    }
}
?>
