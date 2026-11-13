<?php
include "conexion.php";
include "funciones.php";

// Para usar el carrito hay que haber iniciado sesión
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

if (!isset($_SESSION["carrito"])) {
    $_SESSION["carrito"] = [];
}

// Acciones del carrito (agregar, sumar, restar, quitar, vaciar)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $accion   = $_POST["accion"] ?? "";
    $marca_id = (int) ($_POST["marca_id"] ?? 0);
    $version  = $_POST["version"] ?? "";
    $clave    = $marca_id . "|" . $version;

    if ($accion == "agregar") {
        // Solo se agregan motos que existen y versiones válidas
        $stmt = $conexion->prepare("SELECT id FROM marcas WHERE id = ?");
        $stmt->bind_param("i", $marca_id);
        $stmt->execute();
        $existe = $stmt->get_result()->num_rows > 0;

        if ($existe && in_array($version, versiones(), true)) {
            if (isset($_SESSION["carrito"][$clave])) {
                $_SESSION["carrito"][$clave]["cantidad"] = min(99, $_SESSION["carrito"][$clave]["cantidad"] + 1);
            } else {
                $_SESSION["carrito"][$clave] = ["marca_id" => $marca_id, "version" => $version, "cantidad" => 1];
            }
        }

        // Regresar a la categoría desde donde se agregó
        $volver = $_POST["volver"] ?? "";
        if (!in_array($volver, ["supersport.php", "naked.php", "touring.php", "adventure.php"], true)) {
            header("Location: carrito.php");
        } else {
            header("Location: $volver?agregado=1");
        }
        exit;
    }

    if (isset($_SESSION["carrito"][$clave])) {
        if ($accion == "sumar")  $_SESSION["carrito"][$clave]["cantidad"] = min(99, $_SESSION["carrito"][$clave]["cantidad"] + 1);
        if ($accion == "restar") $_SESSION["carrito"][$clave]["cantidad"] = max(1, $_SESSION["carrito"][$clave]["cantidad"] - 1);
        if ($accion == "quitar") unset($_SESSION["carrito"][$clave]);
    }
    if ($accion == "vaciar") $_SESSION["carrito"] = [];

    header("Location: carrito.php");
    exit;
}

$lineas = carrito_detalle($conexion);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Carrito · Moto Sport</title>
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
                <a href="carrito.php" aria-current="page">Carrito (<?php echo carrito_contar(); ?>)</a>
                <a href="tienda.php">Pedido</a>
                <span class="usuario">Hola, <?php echo e($_SESSION["nombre"]); ?></span>
                <a class="salir" href="logout.php">Cerrar sesión</a>
            </nav>
        </div>
    </header>

    <section class="seccion">
        <div class="contenedor">
            <h2>Tu carrito</h2>
            <p class="subtitulo">Revisa tus motos antes de finalizar la compra.</p>

            <div class="tabla-caja">
                <?php if (count($lineas) == 0) { ?>
                    <p class="vacio">Tu carrito está vacío. <a href="supersport.php">Explora el catálogo</a>.</p>
                <?php } else { ?>
                    <table>
                        <tr><th></th><th>Moto</th><th>Versión</th><th>Cantidad</th></tr>
                        <?php foreach ($lineas as $l) { ?>
                            <tr>
                                <td><img class="mini" src="<?php echo e(imagen_moto($l["nombre"])); ?>" alt=""></td>
                                <td><?php echo e($l["nombre"]); ?></td>
                                <td><?php echo e($l["version"]); ?></td>
                                <td>
                                    <form method="POST" class="cantidad">
                                        <input type="hidden" name="marca_id" value="<?php echo (int) $l["marca_id"]; ?>">
                                        <input type="hidden" name="version" value="<?php echo e($l["version"]); ?>">
                                        <button type="submit" name="accion" value="restar" aria-label="Quitar una">−</button>
                                        <span><?php echo (int) $l["cantidad"]; ?></span>
                                        <button type="submit" name="accion" value="sumar" aria-label="Agregar una">+</button>
                                        <button type="submit" name="accion" value="quitar" class="suave">Quitar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php } ?>
                    </table>
                <?php } ?>
            </div>

            <?php if (count($lineas) > 0) { ?>
                <div class="resumen">
                    <p><b>Total de motos:</b> <?php echo carrito_contar(); ?></p>
                    <a class="boton" href="tienda.php">Finalizar compra</a>
                    <form method="POST">
                        <button type="submit" name="accion" value="vaciar" class="suave">Vaciar carrito</button>
                    </form>
                </div>
            <?php } ?>
        </div>
    </section>

    <footer class="pie-alumno">
        <p>Andres Gael Gonzalez Ginez · 24308051280847</p>
        <p>5J · Programación · CBTIS 128</p>
        <p>© <?php echo date("Y"); ?> Moto Sport · Ciudad Juárez, Chihuahua</p>
    </footer>
</body>
</html>