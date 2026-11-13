<?php
include "conexion.php";
include "funciones.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Moto Sport · Inicio</title>
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
                <a href="index.php" aria-current="page">Inicio</a>
                <a href="supersport.php">Supersport</a>
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

    <section class="portada">
        <div class="contenedor">
            <div>
                <h1>Moto Sport</h1>
                <p>Motos deportivas de las mejores marcas. Elige tu modelo y recíbela en tu casa.</p>
                <?php if (isset($_SESSION["usuario_id"])) { ?>
                    <a class="boton" href="carrito.php">Ver mi carrito</a>
                <?php } else { ?>
                    <a class="boton" href="registro.php">Crear cuenta</a>
                <?php } ?>
            </div>
            <img src="imagenes/portada.svg" alt="Moto deportiva roja">
        </div>
    </section>

    <section class="seccion">
        <div class="contenedor">
            <h2>Quiénes somos</h2>
            <p class="subtitulo" style="max-width:700px">Moto Sport es una tienda en línea de Ciudad Juárez, Chihuahua, dedicada a las motos deportivas de marcas como Yamaha, Ducati, Kawasaki, Honda y BMW. Reunimos en un solo lugar los mejores modelos, con entrega a domicilio.</p>
        </div>
    </section>

    <section class="seccion">
        <div class="contenedor">
            <h2>Categorías</h2>
            <p class="subtitulo">Más de 10 modelos en cada sección. Elige una para ver el catálogo.</p>
            <div class="losetas">
                <a class="loseta" href="supersport.php">Supersport</a>
                <a class="loseta" href="naked.php">Naked</a>
                <a class="loseta" href="touring.php">Touring</a>
                <a class="loseta" href="adventure.php">Adventure</a>
            </div>
        </div>
    </section>

    <section class="seccion">
        <div class="contenedor">
            <h2>Cómo comprar</h2>
            <div class="pasos">
                <div><b>1. Explora</b><p>Recorre las categorías y encuentra tu moto.</p></div>
                <div><b>2. Crea tu cuenta</b><p>Regístrate para poder agregar motos al carrito.</p></div>
                <div><b>3. Finaliza tu compra</b><p>Revisa tu carrito, escribe tu dirección y recíbela en casa.</p></div>
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