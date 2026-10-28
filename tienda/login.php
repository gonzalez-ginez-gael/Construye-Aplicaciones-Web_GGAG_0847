<?php
include "conexion.php";
include "funciones.php";
$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo   = $conexion->real_escape_string($_POST["correo"]);
    $password = $_POST["password"];

    // Buscamos al usuario por su correo
    $resultado = $conexion->query("SELECT * FROM usuarios WHERE correo = '$correo'");
    $usuario = $resultado->fetch_assoc();

    // Si existe y la contraseña coincide, guardamos sus datos en la sesión
    if ($usuario && password_verify($password, $usuario["password"])) {
        $_SESSION["usuario_id"] = $usuario["id"];
        $_SESSION["nombre"] = $usuario["nombre"];
        header("Location: tienda.php");
        exit;
    } else {
        $mensaje = "Correo o contraseña incorrectos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar sesión · Moto Sport</title>
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
                <h2>Tu próxima deportiva te espera</h2>
                <p>Entra para ver el catálogo y hacer tu pedido.</p>
            </div>
        </div>

        <div class="panel">
            <div class="formulario">
                <h1>Iniciar sesión</h1>
                <p class="nota">Entra con tu correo y contraseña.</p>

                <?php
                if (isset($_GET["registrado"])) {
                    echo "<p class='ok'>Registro exitoso, ya puedes entrar.</p>";
                }
                if ($mensaje) {
                    echo "<p class='error'>" . e($mensaje) . "</p>";
                }
                ?>

                <form method="POST">
                    <label for="correo">Correo</label>
                    <input type="email" name="correo" id="correo" required>

                    <label for="password">Contraseña</label>
                    <input type="password" name="password" id="password" required>

                    <button type="submit">Entrar</button>
                </form>

                <p class="alterno">¿No tienes cuenta? <a href="registro.php">Regístrate</a></p>
            </div>
        </div>
    </div>
</body>
</html>
