<?php
include "conexion.php";
include "funciones.php";

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}
$usuario_id = (int) $_SESSION["usuario_id"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $marca_id  = (int) $_POST["marca_id"];
    $producto  = $conexion->real_escape_string($_POST["producto"]);
    $cantidad  = max(1, (int) $_POST["cantidad"]);
    $direccion = $conexion->real_escape_string($_POST["direccion"]);

    if ($marca_id > 0) {
        $conexion->query("INSERT INTO pedidos (usuario_id, marca_id, producto, cantidad, direccion)
                          VALUES ($usuario_id, $marca_id, '$producto', $cantidad, '$direccion')");
        header("Location: tienda.php?pedido=ok");
        exit;
    }
}

$mensaje = isset($_GET["pedido"]) ? "¡Pedido guardado correctamente!" : "";
$elegida = (int) ($_GET["moto"] ?? 0);

$catalogo = [];
$categorias = $conexion->query("SELECT * FROM categorias ORDER BY id");
while ($cat = $categorias->fetch_assoc()) {
    $modelos = $conexion->query("SELECT * FROM marcas WHERE categoria_id = " . (int) $cat["id"] . " ORDER BY id");
    $cat["modelos"] = $modelos->fetch_all(MYSQLI_ASSOC);
    $catalogo[] = $cat;
}

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
                <a href="tienda.php" aria-current="page">Pedido</a>
                <span class="usuario">Hola, <?php echo e($_SESSION["nombre"]); ?></span>
                <a class="salir" href="logout.php">Cerrar sesión</a>
            </nav>
        </div>
    </header>

    <section class="seccion">
        <div class="contenedor">
            <h2>Haz tu pedido</h2>
            <p class="subtitulo">Elige la moto, la versión y dónde quieres recibirla.</p>

            <div class="pedido">
                <div class="vista">
                    <img id="vista-foto" src="imagenes/moto-generica.svg" alt="">
                    <p id="vista-nombre">Elige una moto</p>
                </div>

                <form method="POST">
                    <?php if ($mensaje) { echo "<p class='aviso'>" . e($mensaje) . "</p>"; } ?>

                    <label for="marca_id">Moto</label>
                    <select name="marca_id" id="marca_id" required>
                        <option value="">Selecciona una moto</option>
                        <?php foreach ($catalogo as $cat) { ?>
                            <optgroup label="<?php echo e($cat["nombre"]); ?>">
                                <?php foreach ($cat["modelos"] as $mod) { ?>
                                    <option value="<?php echo (int) $mod["id"]; ?>" data-foto="<?php echo e(imagen_moto($mod["nombre"])); ?>"<?php if ($mod["id"] == $elegida) echo " selected"; ?>><?php echo e($mod["nombre"]); ?></option>
                                <?php } ?>
                            </optgroup>
                        <?php } ?>
                    </select>

                    <label for="producto">Versión</label>
                    <select name="producto" id="producto" required>
                        <option value="">Selecciona una versión</option>
                        <option value="Estándar">Estándar</option>
                        <option value="ABS">ABS</option>
                        <option value="Racing">Racing</option>
                        <option value="Edición Limitada">Edición Limitada</option>
                    </select>

                    <label for="cantidad">Cantidad</label>
                    <input type="number" name="cantidad" id="cantidad" min="1" value="1" required>

                    <label for="direccion">Dirección de entrega</label>
                    <input type="text" name="direccion" id="direccion" required>

                    <button type="submit">Enviar pedido</button>
                </form>
            </div>
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

    <script>
        var select = document.getElementById("marca_id");
        var foto   = document.getElementById("vista-foto");
        var nombre = document.getElementById("vista-nombre");

        function actualizarVista() {
            var opcion = select.options[select.selectedIndex];
            if (opcion && opcion.value) {
                foto.src = opcion.getAttribute("data-foto");
                foto.alt = opcion.text;
                nombre.textContent = opcion.text;
            } else {
                foto.src = "imagenes/moto-generica.svg";
                foto.alt = "";
                nombre.textContent = "Elige una moto";
            }
        }
        select.addEventListener("change", actualizarVista);
        actualizarVista();
    </script>
</body>
</html>