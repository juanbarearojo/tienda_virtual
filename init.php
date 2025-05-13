<?php
// init_db.php

// 1. Crear carpeta 'db' si no existe
$dbDir = __DIR__ . '/db';
if (!is_dir($dbDir)) {
    mkdir($dbDir, 0755, true);
}

// 2. Ruta al fichero de base de datos
$dbFile = $dbDir . '/tienda.db';

// 3. Crear o abrir el fichero de base de datos
try {
    $db = new PDO('sqlite:' . $dbFile);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo file_exists($dbFile)
        ? "<p style='color:green;'>✔ Base de datos abierta en: {$dbFile}</p>"
        : "<p style='color:green;'>✔ Base de datos creada en: {$dbFile}</p>";

    // 4. Crear tablas si no existen
    $db->exec(
        "CREATE TABLE IF NOT EXISTS Usuarios (
            id_usuario INTEGER PRIMARY KEY AUTOINCREMENT,
            nombre TEXT NOT NULL,
            email TEXT UNIQUE NOT NULL,
            password TEXT NOT NULL,
            fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP
        );"
    );
    echo "<p style='color:green;'>✔ Tabla Usuarios lista.</p>";

    $db->exec(
            "CREATE TABLE IF NOT EXISTS Productos (
            id_producto INTEGER PRIMARY KEY AUTOINCREMENT,
            nombre TEXT NOT NULL,
            descripcion TEXT,
            precio REAL NOT NULL,
            stock INTEGER NOT NULL DEFAULT 0,
            categoria TEXT,
            imagen_url TEXT
        );"
    );
    echo "<p style='color:green;'>✔ Tabla Productos lista.</p>";

    $db->exec(
        "CREATE TABLE IF NOT EXISTS Carritos (
            id_carrito INTEGER PRIMARY KEY AUTOINCREMENT,
            id_usuario INTEGER NOT NULL,
            fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
            activo BOOLEAN NOT NULL DEFAULT 1,
            FOREIGN KEY (id_usuario) REFERENCES Usuarios(id_usuario)
        );"
    );
    echo "<p style='color:green;'>✔ Tabla Carritos lista.</p>";

    $db->exec(
        "CREATE TABLE IF NOT EXISTS Items_Carrito (
            id_item INTEGER PRIMARY KEY AUTOINCREMENT,
            id_carrito INTEGER NOT NULL,
            id_producto INTEGER NOT NULL,
            cantidad INTEGER NOT NULL DEFAULT 1,
            FOREIGN KEY (id_carrito) REFERENCES Carritos(id_carrito),
            FOREIGN KEY (id_producto) REFERENCES Productos(id_producto)
        );"
    );
    echo "<p style='color:green;'>✔ Tabla Items_Carrito lista.</p>";

} catch (PDOException $e) {
    die("<p style='color:red;'>✖ Error: " . htmlspecialchars($e->getMessage()) . "</p>");
}
?>

