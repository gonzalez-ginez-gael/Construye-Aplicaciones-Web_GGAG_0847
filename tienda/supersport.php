<?php
include "conexion.php";
include "funciones.php";

$categoria = "Supersport";   // ← única línea que cambia en cada categoría

$stmt = $conexion->prepare("SELECT m.id, m.nombre FROM marcas m
                            JOIN categorias c ON c.id = m.categoria_id
                            WHERE c.nombre = ? ORDER BY m.id");
$stmt->bind_param("s", $categoria);
$stmt->execute();
$modelos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Supersport · Moto Sport</title>
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
                <a href="supersport.php" aria-current="page">Supersport</a>
                <a href="naked.php">Naked</a>
                <a href="touring.php">Touring</a>
                <a href="adventure.php">Adventure</a>
                <?php if (isset($_SESSION["usuario_id"])) { ?>
                    <a href="carrito.php">Carrito (<?php echo carrito_contar(); ?>)</a>
                    <a href="tienda.php">Pedido</a>
                    <span class="usuario">Hola, <?php echo e($_SESSION["nombre"]); ?></span>
                    <a class="salir" href="logout.php">Cerrar sesión</a>
                <?php } else { ?>
                    <a href="login.php">Entrar</a>
                    <a class="salir" href="registro.php">Crear cuenta</a>
                <?php } ?>
            </nav>
        </div>
    </header>

    <section class="seccion">
        <div class="contenedor">
            <p class="subtitulo">Catálogo</p>
            <h2>Supersport</h2>
            <p class="subtitulo"><?php echo count($modelos); ?> modelos</p>

            <?php if (isset($_GET["agregado"])) { echo "<p class='aviso'>Moto agregada al carrito. <a href='carrito.php'>Ver carrito</a></p>"; } ?>

            <div class="rejilla" style="margin-top:24px">
                <?php foreach ($modelos as $mod) { ?>
                    <article class="moto">
                        <div class="foto">
                            <img src="<?php echo e(imagen_moto($mod["nombre"])); ?>" alt="<?php echo e($mod["nombre"]); ?>" loading="lazy">
                        </div>
                        <div class="datos">
                            <h4><?php echo e($mod["nombre"]); ?></h4>
                            <?php if (isset($_SESSION["usuario_id"])) { ?>
                                <form method="POST" action="carrito.php">
                                    <input type="hidden" name="accion" value="agregar">
                                    <input type="hidden" name="marca_id" value="<?php echo (int) $mod["id"]; ?>">
                                    <input type="hidden" name="volver" value="<?php echo e(basename($_SERVER["PHP_SELF"])); ?>">
                                    <select name="version" aria-label="Versión">
                                        <?php foreach (versiones() as $v) { ?>
                                            <option><?php echo e($v); ?></option>
                                        <?php } ?>
                                    </select>
                                    <button type="submit" class="boton-fino">Agregar al carrito</button>
                                </form>
                            <?php } else { ?>
                                <a class="boton boton-fino" href="registro.php">Crea tu cuenta para pedir</a>
                            <?php } ?>
                        </div>
                    </article>
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