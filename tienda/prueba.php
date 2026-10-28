<?php
// Página para comprobar que todo funciona
echo "<h2>Diagnóstico</h2>";
echo "<p>✔ PHP funciona</p>";

$c = new mysqli("localhost", "root", "", "tienda_db");
if ($c->connect_error) {
    die("<p>✘ No conecta: " . $c->connect_error . "</p>");
}
echo "<p>✔ Conexión a tienda_db correcta</p>";

$tablas = ["usuarios", "categorias", "marcas", "pedidos"];
foreach ($tablas as $t) {
    $r = $c->query("SELECT COUNT(*) AS n FROM $t");
    $fila = $r->fetch_assoc();
    echo "<p>✔ Tabla $t: " . $fila["n"] . " registros</p>";
}
?>
