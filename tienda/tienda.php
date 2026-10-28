<?php
include "conexion.php";
include "funciones.php";

// Si no ha iniciado sesión, lo mandamos al login
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

$usuario_id = (int) $_SESSION["usuario_id"];

// Cuando se envía el formulario, guardamos el pedido
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $marca_id  = (int) $_POST["marca_id"];
    $producto  = $conexion->real_escape_string($_POST["producto"]);
    $cantidad  = max(1, (int) $_POST["cantidad"]);
    $direccion = $conexion->real_escape_string($_POST["direccion"]);

    if ($marca_id > 0) {
        $conexion->query("INSERT INTO pedidos (usuario_id, marca_id, producto, cantidad, direccion)
                          VALUES ($usuario_id, $marca_id, '$producto', $cantidad, '$direccion')");
        // Redirigimos para que al recargar la página no se repita el pedido
        header("Location: tienda.php?pedido=ok#mis-pedidos");
        exit;
    }
}

$mensaje = isset($_GET["pedido"]) ? "¡Pedido guardado correctamente!" : "";

// Cargamos el catálogo: cada categoría con sus motos
$catalogo = [];
$total = 0;
$categorias = $conexion->query("SELECT * FROM categorias ORDER BY id");
while ($cat = $categorias->fetch_assoc()) {
    $modelos = $conexion->query("SELECT * FROM marcas WHERE categoria_id = " . (int) $cat["id"] . " ORDER BY id");
    $cat["modelos"] = $modelos->fetch_all(MYSQLI_ASSOC);
    $total += count($cat["modelos"]);
    $catalogo[] = $cat;
}

// Pedidos del usuario que inició sesión
$pedidos = $conexion->query("SELECT p.*, m.nombre AS moto
                             FROM pedidos p JOIN marcas m ON m.id = p.marca_id
                             WHERE p.usuario_id = $usuario_id ORDER BY p.id DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Moto Sport · Motos deportivas</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@400;500&family=Marcellus&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <header class="cabecera">
        <div class="contenedor">
            <a class="marca" href="tienda.php">Moto Sport</a>
            <nav>
                <a href="#catalogo">Catálogo</a>
                <a href="#pedido">Hacer pedido</a>
                <a href="#mis-pedidos">Mis pedidos</a>
                <span class="usuario">Hola, <?php echo e($_SESSION["nombre"]); ?></span>
                <a class="salir" href="logout.php">Cerrar sesión</a>
            </nav>
        </div>
    </header>

    <section class="portada">
        <div class="contenedor">
            <div>
                <h1>Elige tu deportiva</h1>
                <p><?php echo $total; ?> modelos de Yamaha, Ducati, Kawasaki, BMW y más. Escoge la tuya y haz tu pedido en un minuto.</p>
                <a class="boton" href="#catalogo">Ver el catálogo</a>
            </div>
            <img src="imagenes/portada.svg" alt="Moto deportiva roja">
        </div>
    </section>

    <section class="seccion" id="catalogo">
        <div class="contenedor">
            <h2>Catálogo</h2>
            <p class="subtitulo">Pulsa “Pedir esta moto” en la que te guste y completa tu pedido.</p>

            <?php foreach ($catalogo as $cat) { ?>
                <div class="categoria">
                    <header>
                        <h3><?php echo e($cat["nombre"]); ?></h3>
                        <span class="cuenta"><?php echo count($cat["modelos"]); ?> modelos</span>
                    </header>
                    <div class="rejilla">
                        <?php foreach ($cat["modelos"] as $mod) { ?>
                            <article class="moto">
                                <div class="foto">
                                    <img src="<?php echo e(imagen_moto($mod["nombre"])); ?>" alt="<?php echo e($mod["nombre"]); ?>" loading="lazy">
                                </div>
                                <div class="datos">
                                    <h4><?php echo e($mod["nombre"]); ?></h4>
                                    <button type="button" class="boton-fino" data-pedir="<?php echo (int) $mod["id"]; ?>">Pedir esta moto</button>
                                </div>
                            </article>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    </section>

    <section class="seccion" id="pedido">
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
                                    <option value="<?php echo (int) $mod["id"]; ?>" data-foto="<?php echo e(imagen_moto($mod["nombre"])); ?>"><?php echo e($mod["nombre"]); ?></option>
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

    <section class="seccion" id="mis-pedidos">
        <div class="contenedor">
            <h2>Mis pedidos</h2>
            <div class="tabla-caja">
                <?php if ($pedidos->num_rows == 0) { ?>
                    <p class="vacio">Aún no tienes pedidos. Elige una moto del catálogo para hacer el primero.</p>
                <?php } else { ?>
                    <table>
                        <tr>
                            <th></th>
                            <th>Moto</th>
                            <th>Versión</th>
                            <th>Cant.</th>
                            <th>Dirección</th>
                            <th>Fecha</th>
                        </tr>
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

    <p class="pie">Moto Sport · <?php echo date("Y"); ?></p>

    <script>
        // Al elegir una moto, se muestra su imagen junto al formulario
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

        // Los botones del catálogo eligen la moto y bajan al formulario
        document.querySelectorAll("[data-pedir]").forEach(function (boton) {
            boton.addEventListener("click", function () {
                select.value = boton.getAttribute("data-pedir");
                actualizarVista();
                document.getElementById("pedido").scrollIntoView();
                document.getElementById("producto").focus({ preventScroll: true });
            });
        });
    </script>
</body>
</html>
