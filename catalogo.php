<?php
// catalogo.php

// 1. Intentar conectar y comprobar conexión
try {
    $db = new PDO('sqlite:' . __DIR__ . '/db/tienda.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p class='text-success'>✔ Conexión a SQLite OK (db/tienda.db)</p>";
} catch (PDOException $e) {
    die("<p class='text-danger'>✖ Error de conexión: " . htmlspecialchars($e->getMessage()) . "</p>");
}

// 2. Crear tabla y datos si no existen
$db->exec("
    CREATE TABLE IF NOT EXISTS productos (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nombre TEXT,
        imagen TEXT,
        precio REAL,
        categoria TEXT
    )
");

// 3. Insertar datos de ejemplo si la tabla está vacía
$count = $db->query("SELECT COUNT(*) FROM productos")->fetchColumn();
if ($count == 0) {
    $productos = [
        ['Camiseta Azul', 'img/camiseta_azul.jpg', 19.99, 'Ropa'],
        ['Taza Logo',     'img/taza_logo.jpg',     7.50, 'Accesorios'],
        ['Sudadera',      'img/sudadera.jpg',    29.90, 'Ropa'],
        ['Mochila',       'img/mochila.jpg',     45.00, 'Accesorios'],
    ];
    $stmt = $db->prepare("INSERT INTO productos (nombre, imagen, precio, categoria) VALUES (?, ?, ?, ?)");
    foreach ($productos as $p) {
        $stmt->execute($p);
    }
}

// 4. Leer y agrupar productos por categoría
$stmt = $db->query("SELECT * FROM productos");
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
$por_categoria = [];
foreach ($productos as $p) {
    $por_categoria[$p['categoria']][] = $p;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Catálogo de Productos</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <div class="container py-4">
    <h1 class="mb-4">Catálogo de Productos</h1>

    <?php foreach ($por_categoria as $categoria => $lista): ?>
      <h2 class="mt-5"><?= htmlspecialchars($categoria) ?></h2>
      <div class="row">
        <?php foreach ($lista as $item): ?>
          <div class="col-sm-6 col-md-4 col-lg-3 mb-4">
            <div class="card h-100">
              <img src="<?= htmlspecialchars($item['imagen']) ?>"
                   class="card-img-top"
                   alt="<?= htmlspecialchars($item['nombre']) ?>">
              <div class="card-body d-flex flex-column">
                <h5 class="card-title"><?= htmlspecialchars($item['nombre']) ?></h5>
                <p class="card-text mt-auto fw-bold">
                  € <?= number_format($item['precio'], 2, ',', '.') ?>
                </p>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endforeach; ?>

  </div>

  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
