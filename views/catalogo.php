<?php
// views/catalogo.php
// No need to include header.php here as it's included in the controller
?>

<div class="container py-4">
  <h1>Catálogo de Productos</h1>

  <?php foreach ($por_categoria as $categoria => $lista): ?>
    <h2><?= htmlspecialchars($categoria) ?></h2>
    <table class="table table-striped">
      <thead class="table-dark">
        <tr>
          <th>Imagen</th>
          <th>Nombre</th>
          <th>Descripción</th>
          <th>Precio</th>
          <th>Stock</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($lista as $item): ?>
          <tr class="align-middle">
            <td class="text-center" style="width:120px;">
              <?php if ($item->imagen_url): ?>
                <?php 
                  // Usar la ruta tal como está en la base de datos, pero normalizando barras
                  $rutaImagen = str_replace('\\', '/', $item->imagen_url);
                  // Si no comienza con barra, añadirla
                  if (substr($rutaImagen, 0, 1) !== '/') {
                    $rutaImagen = '/' . $rutaImagen;
                  }
                ?>
                <img
                  src="<?= htmlspecialchars($rutaImagen) ?>"
                  alt="<?= htmlspecialchars($item->nombre) ?>"
                  style="width:100px; height:100px; object-fit:cover; display:block; margin:auto; border-radius:4px;"
                >
                <!-- Debug: mostramos la ruta generada -->
                <div style="text-align:center; font-size:.75rem; color:#6c757d; margin-top:.25rem;">
                  <?= htmlspecialchars($rutaImagen) ?>
                </div>
              <?php else: ?>
                <div style="width:100px; height:100px; background:#e9ecef; display:flex; align-items:center; justify-content:center; color:#6c757d; font-size:.8rem; margin:auto; border-radius:4px;">
                  Sin imagen
                </div>
              <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($item->nombre) ?></td>
            <td><?= nl2br(htmlspecialchars($item->descripcion ?? '—')) ?></td>
            <td>€ <?= number_format($item->precio, 2, ',', '.') ?></td>
            <td><?= $item->stock ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endforeach; ?>
</div>

<?php // No need to include footer.php here as it's included in the controller ?>