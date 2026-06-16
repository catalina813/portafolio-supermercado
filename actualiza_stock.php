<?php
// Corrección de la ruta: conexion.php está al mismo nivel de este archivo
include("conexion.php");

if (isset($_POST['actualizar_inventario'])) {
    $idProducto = $_POST['idProducto'];
    $unidades_nuevas = $_POST['cantidad_nuevas'];

    // Buscamos cuánto stock tiene actualmente ese producto (Comillas corregidas)
    $consulta = mysqli_query($conexion, "SELECT stock FROM producto WHERE idProducto = '$idProducto'");
    $fila = mysqli_fetch_assoc($consulta);

    if ($fila) {
        // Sumamos lo que ya había en la base de datos + lo que acaba de llegar en el camión
        $stock_final = $fila['stock'] + $unidades_nuevas;

        // Guardamos el nuevo valor total (Comillas corregidas)
        $update = "UPDATE producto SET stock = '$stock_final' WHERE idProducto = '$idProducto'";
        mysqli_query($conexion, $update);

        // Corrección de la ruta de redirección
        header("Location: inventario.php");
        exit();
    }
}
?>
