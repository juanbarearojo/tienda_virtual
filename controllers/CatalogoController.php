<?php
// controllers/CatalogoController.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/producto.php';

$db = getConnection();
$db->exec("
    CREATE TABLE IF NOT EXISTS productos (
        id INTEGER PRIMARY KEY,
        nombre TEXT, imagen TEXT, precio REAL, categoria TEXT
    )
");
Producto::seedIfEmpty($db);

$productos = Producto::all($db);
$por_categoria = [];
foreach ($productos as $p) {
    $por_categoria[$p->categoria][] = $p;
}

require __DIR__ . '/../views/catalogo.php';
