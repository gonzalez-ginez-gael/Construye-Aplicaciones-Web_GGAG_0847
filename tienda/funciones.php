<?php
// Funciones de ayuda compartidas por las páginas

// Escribe texto de forma segura dentro del HTML
function e($texto) {
    return htmlspecialchars((string) $texto, ENT_QUOTES, "UTF-8");
}

// Devuelve la imagen de una moto. Orden de búsqueda:
// 1) tu foto en imagenes/fotos/  (ej: ducati-panigale-v4.jpg)
// 2) el dibujo en imagenes/modelos/
// 3) una imagen genérica
function imagen_moto($nombre) {
    $slug = trim(preg_replace('/[^a-z0-9]+/', '-', strtolower($nombre)), '-');
    foreach (["jpg", "jpeg", "png", "webp"] as $ext) {
        if (file_exists(__DIR__ . "/imagenes/fotos/$slug.$ext")) {
            return "imagenes/fotos/$slug.$ext";
        }
    }
    if (file_exists(__DIR__ . "/imagenes/modelos/$slug.svg")) {
        return "imagenes/modelos/$slug.svg";
    }
    return "imagenes/moto-generica.svg";
}

// Versiones que se pueden elegir al pedir una moto
function versiones() {
    return ["Estándar", "ABS", "Racing", "Edición Limitada"];
}

// Líneas del carrito (se guardan en la sesión)
function carrito_obtener() {
    return isset($_SESSION["carrito"]) ? $_SESSION["carrito"] : [];
}

// Total de motos en el carrito
function carrito_contar() {
    $total = 0;
    foreach (carrito_obtener() as $linea) {
        $total += $linea["cantidad"];
    }
    return $total;
}

// Líneas del carrito con el nombre real de cada moto (descarta las que ya no existen)
function carrito_detalle($conexion) {
    $lineas = carrito_obtener();
    if (count($lineas) == 0) return [];

    $ids = [];
    foreach ($lineas as $l) {
        $ids[] = (int) $l["marca_id"];
    }
    $lista = implode(",", array_unique($ids));

    $nombres = [];
    $res = $conexion->query("SELECT id, nombre FROM marcas WHERE id IN ($lista)");
    while ($fila = $res->fetch_assoc()) {
        $nombres[$fila["id"]] = $fila["nombre"];
    }

    $detalle = [];
    foreach ($lineas as $l) {
        if (!isset($nombres[$l["marca_id"]])) continue;
        $l["nombre"] = $nombres[$l["marca_id"]];
        $detalle[] = $l;
    }
    return $detalle;
}

?>
