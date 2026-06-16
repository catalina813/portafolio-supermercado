<?php
// 1. Abrimos la sesión actual
session_start();

// 2. Destruimos todas las variables de la sesión
session_unset();
session_destroy();

// 3. Lo mandamos de patitas a la calle (al Login)
header("Location: login.php");
exit();
?>
