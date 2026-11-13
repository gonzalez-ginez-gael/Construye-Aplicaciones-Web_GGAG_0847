<?php
include "conexion.php";
include "funciones.php";

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}
$usuario_id = (int) $_SESSION["usuario_id"];
$lineas = carrito_detalle($conexion);
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $direccion = trim($_POST["direccion"] ?? "");

    if (count($lineas) == 0) {
        $error = "Tu carrito está vacío.";
    } elseif (mb_strlen($direccion) < 10) {
        $error = "Escribe una dirección completa (calle, número y colonia).";
    } else {
        // Una fila de pedidos por cada moto del carrito, todas con la misma dirección
        $insertar = $conexion->prepare("INSERT INTO pedidos (usuario_id, marca_id, producto, cantidad, direccion) VALUES (?, ?, ?, ?, ?)");
        $conexion->begin_transaction();
        $bien = true;
        foreach ($lineas as $l) {
            $insertar->bind_param("iisis", $usuario_id, $l["marca_id"], $l["version"], $l["cantidad"], $direccion);
            if (!$insertar->execute()) $bien = false;
        }

        if ($bien) {
            $conexion->commit();
            $_SESSION["carrito"] = [];
            header("Location: tienda.php?pedido=ok");
            exit;
        }
        $conexion->rollback();
        $error = "No se pudo guardar el pedido. Inténtalo de nuevo.";
    }
}

$mensaje = isset($_GET["pedido"]) ? "¡Pedido guardado correctamente!" : "";

$pedidos = $conexion->query("SELECT p.*, m.nombre AS moto
                             FROM pedidos p JOIN marcas m ON m.id = p.marca_id
                             WHERE p.usuario_id = $usuario_id ORDER BY p.id DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pedido · Moto Sport</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@400;500&family=Marcellus&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <header class="cabecera">
        <div class="contenedor">
            <a class="marca" href="index.php">Moto Sport</a>
            <nav>
                <a href="index.php">Inicio</a>
                <a href="supersport.php">Supersport</a>
                <a href="naked.php">Naked</a>
                <a href="touring.php">Touring</a>
                <a href="adventure.php">Adventure</a>
                <a href="carrito.php">Carrito (<?php echo carrito_contar(); ?>)</a>
                <a href="tienda.php" aria-current="page">Pedido</a>
                <span class="usuario">Hola, <?php echo e($_SESSION["nombre"]); ?></span>
                <a class="salir" href="logout.php">Cerrar sesión</a>
            </nav>
        </div>
    </header>

    <section class="seccion">
        <div class="contenedor">
            <h2>Finalizar compra</h2>
            <p class="subtitulo">Confirma tus motos y dinos dónde quieres recibirlas.</p>

            <?php if ($mensaje) { echo "<p class='aviso'>" . e($mensaje) . "</p>"; } ?>

            <?php if (count($lineas) == 0) { ?>
                <p class="vacio">No tienes motos en el carrito. <a href="supersport.php">Explora el catálogo</a>.</p>
            <?php } else { ?>
                <div class="tabla-caja">
                    <table>
                        <tr><th></th><th>Moto</th><th>Versión</th><th>Cant.</th></tr>
                        <?php foreach ($lineas as $l) { ?>
                            <tr>
                                <td><img class="mini" src="<?php echo e(imagen_moto($l["nombre"])); ?>" alt=""></td>
                                <td><?php echo e($l["nombre"]); ?></td>
                                <td><?php echo e($l["version"]); ?></td>
                                <td><?php echo (int) $l["cantidad"]; ?></td>
                            </tr>
                        <?php } ?>
                    </table>
                </div>

                <form method="POST" class="envio" onsubmit="return confirm('¿Confirmar el pedido de <?php echo carrito_contar(); ?> moto(s)?');">
                    <?php if ($error) { echo "<p class='error'>" . e($error) . "</p>"; } ?>

                    <label for="direccion">Dirección de entrega</label>
                    <input type="text" name="direccion" id="direccion" minlength="10" required
                           value="<?php echo e($_POST["direccion"] ?? ""); ?>">

                    <button type="submit">Confirmar pedido</button>
                    <p class="nota-enlace"><a href="carrito.php">← Volver al carrito</a></p>
                </form>
            <?php } ?>
        </div>
    </section>

    <section class="seccion">
        <div class="contenedor">
            <h2>Mis pedidos</h2>
            <div class="tabla-caja">
                <?php if ($pedidos->num_rows == 0) { ?>
                    <p class="vacio">Aún no tienes pedidos.</p>
                <?php } else { ?>
                    <table>
                        <tr><th></th><th>Moto</th><th>Versión</th><th>Cant.</th><th>Dirección</th><th>Fecha</th></tr>
                        <?php while ($p = $pedidos->fetch_assoc()) { ?>
                            <tr>
                                <td><img class="mini" src="<?php echo e(imagen_moto($p["moto"])); ?>" alt=""></td>
                                <td><?php echo e($p["moto"]); ?></td>
                                <td><?php echo e($p["producto"]); ?></td>
                                <td><?php echo (int) $p["cantidad"]; ?></td>
                                <td><?php echo e($p["direccion"]); ?></td>
                                <td><?php echo e($p["fecha"]); ?></td>
                            </tr>
                        <?php } ?>
                    </table>
                <?php } ?>
            </div>
        </div>
    </section>

    <footer class="pie-alumno">
        <p>Andres Gael Gonzalez Ginez · 24308051280847</p>
        <p>5J · Programación · CBTIS 128</p>
        <p>© <?php echo date("Y"); ?> Moto Sport · Ciudad Juárez, Chihuahua</p>
    </footer>
</body>
</html>