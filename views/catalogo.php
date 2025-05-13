<?php
// views/catalogo.php
// Variables disponibles: $por_categoria, $categorias, $term, $category
?>

<div class="container py-4">

  <!-- Enlace al carrito -->
  <div class="d-flex justify-content-end mb-3">
    <a href="/carrito" class="btn btn-primary">
      <i class="bi bi-cart-fill"></i> Ver carrito
    </a>
  </div>

  <!-- Formulario de búsqueda y filtro -->
  <form method="get" class="row g-2 mb-4 align-items-end">
    <div class="col-sm">
      <label for="term" class="form-label">Buscar por nombre</label>
      <input
        type="text"
        id="term"
        name="term"
        value="<?= htmlspecialchars($term ?? '') ?>"
        class="form-control"
        placeholder="Ej. ‘camiseta’"
      >
    </div>
    <div class="col-sm">
      <label for="category" class="form-label">Filtrar categoría</label>
      <select id="category" name="category" class="form-select">
        <option value="">Todas</option>
        <?php foreach ($categorias as $catOption): ?>
          <option
            value="<?= htmlspecialchars($catOption) ?>"
            <?= ($catOption === ($category ?? '')) ? 'selected' : '' ?>
          >
            <?= htmlspecialchars($catOption) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-sm-auto">
      <button type="submit" class="btn btn-primary">Buscar</button>
    </div>
  </form>

  <h1>Catálogo de Productos</h1>

  <?php if (empty($por_categoria)): ?>
    <p class="text-muted">No se han encontrado productos para esos criterios.</p>
  <?php endif; ?>

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
                <?php $ruta = $item->getRutaImagen(); ?>
                <img
                  src="<?= htmlspecialchars($ruta) ?>"
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
            <td>€ <?= $item->getPrecioFormateado() ?></td>
            <td><?= $item->stock ?></td>
            <td style="width:150px;">
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
