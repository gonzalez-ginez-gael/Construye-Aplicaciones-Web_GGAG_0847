<?php
include "conexion.php";
include "funciones.php";
$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Guardamos lo que escribió el usuario en variables
    $nombre   = $conexion->real_escape_string($_POST["nombre"]);
    $correo   = $conexion->real_escape_string($_POST["correo"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT); // contraseña encriptada

    // Revisamos si el correo ya existe
    $buscar = $conexion->query("SELECT id FROM usuarios WHERE correo = '$correo'");

    if ($buscar->num_rows > 0) {
        $mensaje = "Ese correo ya está registrado.";
    } else {
        // Guardamos el usuario en la base de datos
        $conexion->query("INSERT INTO usuarios (nombre, correo, password) VALUES ('$nombre', '$correo', '$password')");
        header("Location: login.php?registrado=1");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Crear cuenta · Moto Sport</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@400;500&family=Marcellus&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <div class="acceso">
        <div class="visual">
            <span class="marca">Moto Sport</span>
            <img src="imagenes/portada.svg" alt="Moto deportiva roja">
            <div>
                <h2>Crea tu cuenta y elige tu moto</h2>
                <p>Con tu cuenta puedes hacer pedidos y consultar su historial.</p>
            </div>
        </div>

        <div class="panel">
            <div class="formulario">
                <h1>Crear cuenta</h1>
                <p class="nota">Solo te toma un momento.</p>

                <?php
                if ($mensaje) {
                    echo "<p class='error'>" . e($mensaje) . "</p>";
                }
                ?>

                <form method="POST">
                    <label for="nombre">Nombre</label>
                    <input type="text" name="nombre" id="nombre" required>

                    <label for="correo">Correo</label>
                    <input type="email" name="correo" id="correo" required>

                    <label for="password">Contraseña (mínimo 6 caracteres)</label>
                    <input type="password" name="password" id="password" minlength="6" required>

                    <button type="submit">Registrarme</button>
                </form>

                <p class="alterno">¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a></p>
            </div>
        </div>
    </div>
</body>
</html>
