<?php
session_start();
session_destroy(); // cierra la sesión
header("Location: login.php");
?>
