<?php include 'header.php'; ?>
<div class="container py-4">
  <h1>Catálogo de Productos</h1>
  <?php foreach ($por_categoria as $cat => $lista): ?>
    <h2><?= htmlspecialchars($cat) ?></h2>
    <div class="row">
      <?php foreach ($lista as $item): ?>
        <div class="col-md-3 mb-4">
          <div class="card h-100">
            <img src="<?= htmlspecialchars($item->imagen) ?>" class="card-img-top">
            <div class="card-body d-flex flex-column">
              <h5 class="card-title"><?= htmlspecialchars($item->nombre) ?></h5>
              <p class="mt-auto fw-bold">€ <?= number_format($item->precio,2,',','.') ?></p>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endforeach; ?>
</div>
<?php include 'footer.php'; ?>
