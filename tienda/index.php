<?php
include "conexion.php";

// Si ya inició sesión va a la tienda, si no, va al login
if (isset($_SESSION["usuario_id"])) {
    header("Location: tienda.php");
    exit;
} else {
    header("Location: login.php");
    exit;
}
?>
