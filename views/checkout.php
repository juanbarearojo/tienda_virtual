<?php
// views/checkout.php
?>

<div class="container py-4">
  <h1>Finalizar Compra</h1>
  
  <?php if (isset($_SESSION['mensaje'])): ?>
    <div class="alert alert-<?= $_SESSION['mensaje']['tipo'] ?> alert-dismissible fade show" role="alert">
      <?= $_SESSION['mensaje']['texto'] ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['mensaje']); ?>
  <?php endif; ?>

  <div class="row">
    <!-- Resumen del pedido -->
    <div class="col-lg-8">
      <div class="card mb-4">
        <div class="card-header bg-primary text-white">
          <h5 class="mb-0">Resumen del Pedido</h5>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table">
              <thead>
                <tr>
                  <th>Producto</th>
                  <th>Precio</th>
                  <th>Cantidad</th>
                  <th>Subtotal</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($items as $item): ?>
                  <tr>
                    <td>
                      <div class="d-flex align-items-center">
                        <?php if ($item->producto->imagen_url): ?>
                          <img 
                            src="<?= htmlspecialchars($item->producto->getRutaImagen()) ?>" 
                            alt="<?= htmlspecialchars($item->producto->nombre) ?>"
                            class="me-2"
                            style="width:48px; height:48px; object-fit:cover; border-radius:4px;"
                          >
                        <?php endif; ?>
                        <div>
                          <?= htmlspecialchars($item->producto->nombre) ?>
                          <?php if ($item->producto->categoria): ?>
                            <small class="d-block text-muted"><?= htmlspecialchars($item->producto->categoria) ?></small>
                          <?php endif; ?>
                        </div>
                      </div>
                    </td>
                    <td>€ <?= $item->producto->getPrecioFormateado() ?></td>
                    <td><?= $item->cantidad ?></td>
                    <td class="fw-bold">
                      € <?= number_format($item->producto->precio * $item->cantidad, 2, ',', '.') ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="3" class="text-end fw-bold">Total:</td>
                  <td class="fw-bold fs-5">€ <?= number_format($total, 2, ',', '.') ?></td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Formulario de pago -->
    <div class="col-lg-4">
      <div class="card">
        <div class="card-header bg-secondary text-white">
          <h5 class="mb-0">Información de Pago</h5>
        </div>
        <div class="card-body">
          <form id="checkout-form" action="/confirmar-checkout" method="post">
            <!-- Datos personales -->
            <div class="mb-3">
              <label for="nombre" class="form-label">Nombre completo *</label>
              <input type="text" class="form-control" id="nombre" name="nombre" required>
            </div>
            
            <div class="mb-3">
              <label for="email" class="form-label">Correo electrónico *</label>
              <input type="email" class="form-control" id="email" name="email" required>
            </div>
            
            <div class="mb-3">
              <label for="telefono" class="form-label">Teléfono</label>
              <input type="tel" class="form-control" id="telefono" name="telefono">
            </div>
            
            <hr>
            
            <!-- Dirección de envío -->
            <div class="mb-3">
              <label for="direccion" class="form-label">Dirección *</label>
              <input type="text" class="form-control" id="direccion" name="direccion" required>
            </div>
            
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="ciudad" class="form-label">Ciudad *</label>
                <input type="text" class="form-control" id="ciudad" name="ciudad" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="codigo_postal" class="form-label">Código Postal *</label>
                <input type="text" class="form-control" id="codigo_postal" name="codigo_postal" required>
              </div>
            </div>
            
            <hr>
            
            <!-- Método de pago (simplificado) -->
            <div class="mb-3">
              <label class="form-label">Método de pago *</label>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="metodo_pago" id="tarjeta" value="tarjeta" checked>
                <label class="form-check-label" for="tarjeta">
                  Tarjeta de crédito/débito
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="metodo_pago" id="transferencia" value="transferencia">
                <label class="form-check-label" for="transferencia">
                  Transferencia bancaria
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="metodo_pago" id="paypal" value="paypal">
                <label class="form-check-label" for="paypal">
                  PayPal
                </label>
              </div>
            </div>
            
            <hr>
            
            <div class="form-check mb-3">
              <input class="form-check-input" type="checkbox" id="terminos" name="terminos" required>
              <label class="form-check-label" for="terminos">
                Acepto los términos y condiciones *
              </label>
            </div>
            
            <button type="submit" class="btn btn-primary btn-lg w-100">
              Confirmar y Pagar
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
  
  <div class="mt-4">
    <a href="/carrito" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left"></i> Volver al carrito
    </a>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Validación del formulario
  const form = document.getElementById('checkout-form');
  
  form.addEventListener('submit', function(event) {
    let valid = true;
    
    // Validar campos requeridos
    form.querySelectorAll('[required]').forEach(input => {
      if (!input.value.trim()) {
        valid = false;
        input.classList.add('is-invalid');
      } else {
        input.classList.remove('is-invalid');
      }
    });
    
    // Validar el checkbox de términos
    const terminos = document.getElementById('terminos');
    if (!terminos.checked) {
      valid = false;
      terminos.classList.add('is-invalid');
    } else {
      terminos.classList.remove('is-invalid');
    }
    
    if (!valid) {
      event.preventDefault();
      alert('Por favor, completa todos los campos requeridos y acepta los términos y condiciones.');
    }
  });
  
  // Quitar validaciones al editar
  form.querySelectorAll('input').forEach(input => {
    input.addEventListener('input', function() {
      this.classList.remove('is-invalid');
    });
  });
});
</script>