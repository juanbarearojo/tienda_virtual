<?php
// init_db.php

// 1. Crear carpeta 'db' si no existe
$dbDir = __DIR__ . '/db';
if (!is_dir($dbDir)) {
    mkdir($dbDir, 0755, true);
}

// 2. Ruta al fichero de base de datos
$dbFile = $dbDir . '/tienda.db';

// 3. Crear el fichero si no existe
if (!file_exists($dbFile)) {
    try {
        $db = new PDO('sqlite:' . $dbFile);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "<p style='color:green;'>✔ Base de datos creada en: {$dbFile}</p>";
    } catch (PDOException $e) {
        die("<p style='color:red;'>✖ Error creando la base de datos: " . htmlspecialchars($e->getMessage()) . "</p>");
    }
} else {
    echo "<p style='color:orange;'>ℹ La base de datos ya existe en: {$dbFile}</p>";
}
?>
