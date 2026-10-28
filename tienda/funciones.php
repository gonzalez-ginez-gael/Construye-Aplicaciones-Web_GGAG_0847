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
?>
