<?php

$servidor = "sql310.infinityfree.com";
$usuario = "if0_42090500";
$password = "F1LwYfTpYl";
$base_datos = "if0_42090500_supermercado";

$conexion = mysqli_connect($servidor, $usuario, $password, $base_datos);
mysqli_set_charset($conexion, "utf8mb4");

if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}

?>
