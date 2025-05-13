<?php
// views/catalogo.php
// No need to include header.php here as it's included in the controller
?>

<div class="container py-4">

  <!-- Enlace al carrito -->
  <div class="d-flex justify-content-end mb-3">
    <a href="/carrito" class="btn btn-primary">
      <i class="bi bi-cart-fill"></i> Ver carrito
    </a>
  </div>

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
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($lista as $item): ?>
          <tr class="align-middle">
            <td class="text-center" style="width:120px;">
              <?php if ($item->imagen_url): ?>
                <?php 
                  $rutaImagen = str_replace('\\', '/', $item->imagen_url);
                  if (substr($rutaImagen, 0, 1) !== '/') {
                    $rutaImagen = '/' . $rutaImagen;
                  }
                ?>
                <img
                  src="<?= htmlspecialchars($rutaImagen) ?>"
                  alt="<?= htmlspecialchars($item->nombre) ?>"
                  style="width:100px; height:100px; object-fit:cover; display:block; margin:auto; border-radius:4px;"
                >
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
            <td style="width:150px;">
              <!-- Formulario para añadir al carrito -->
              <form action="/agregar-al-carrito" method="post" class="d-flex">
                <input type="hidden" name="producto_id" value="<?= (int)$item->id_producto ?>">
                <input type="hidden" name="cantidad" value="1">
                <button type="submit" class="btn btn-sm btn-success flex-grow-1">
                  <i class="bi bi-cart-plus"></i> Agregar
                </button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endforeach; ?>
</div>

<?php // No need to include footer.php here as it's included in the controller ?>
