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
                <tr class="table-light">
                  <td colspan="3" class="text-end fw-bold fs-6">Total:</td>
                  <td class="fw-bold fs-5 text-primary">€ <?= number_format($total, 2, ',', '.') ?></td>
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
          <h5 class="mb-0"><i class="bi bi-credit-card me-2"></i>Información de Pago</h5>
        </div>
        <div class="card-body">
          <form id="checkout-form" action="/confirmar-checkout" method="post">
            
            <!-- Datos personales -->
            <div class="mb-4">
              <h6 class="text-muted mb-3"><i class="bi bi-person me-1"></i>Datos Personales</h6>
              
              <div class="mb-3">
                <label for="nombre" class="form-label">Nombre completo *</label>
                <input 
                  type="text" 
                  class="form-control" 
                  id="nombre" 
                  name="nombre" 
                  placeholder="<?= htmlspecialchars($usuario->nombre ?? 'Ej: Juan Pérez García') ?>"
                  value="<?= htmlspecialchars($usuario->nombre ?? '') ?>"
                  required
                >
              </div>
              
              <div class="mb-3">
                <label for="email" class="form-label">Correo electrónico *</label>
                <input 
                  type="email" 
                  class="form-control" 
                  id="email" 
                  name="email" 
                  placeholder="<?= htmlspecialchars($usuario->email ?? 'correo@ejemplo.com') ?>"
                  value="<?= htmlspecialchars($usuario->email ?? '') ?>"
                  required
                  readonly
                >
                <small class="form-text text-muted">Este campo no se puede modificar</small>
              </div>
              
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="telefono" class="form-label">Teléfono</label>
                  <input 
                    type="tel" 
                    class="form-control" 
                    id="telefono" 
                    name="telefono" 
                    placeholder="<?= htmlspecialchars($usuario->telefono ?? '+34 600 123 456') ?>"
                    value="<?= htmlspecialchars($usuario->telefono ?? '') ?>"
                  >
                </div>
                <div class="col-md-6 mb-3">
                  <label for="nif" class="form-label">NIF/DNI</label>
                  <input 
                    type="text" 
                    class="form-control" 
                    id="nif" 
                    name="nif" 
                    placeholder="<?= htmlspecialchars($usuario->NIF ?? '12345678A') ?>"
                    value="<?= htmlspecialchars($usuario->NIF ?? '') ?>"
                  >
                </div>
              </div>
            </div>
            
            <hr>
            
            <!-- Dirección de envío -->
            <div class="mb-4">
              <h6 class="text-muted mb-3"><i class="bi bi-geo-alt me-1"></i>Dirección de Envío</h6>
              
              <div class="mb-3">
                <label for="direccion" class="form-label">Dirección completa *</label>
                <input 
                  type="text" 
                  class="form-control" 
                  id="direccion" 
                  name="direccion" 
                  placeholder="<?= htmlspecialchars($usuario->domicilio ?? 'Calle Principal, 123, 1º A') ?>"
                  value="<?= htmlspecialchars($usuario->domicilio ?? '') ?>"
                  required
                >
              </div>
              
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="ciudad" class="form-label">Ciudad *</label>
                  <input 
                    type="text" 
                    class="form-control" 
                    id="ciudad" 
                    name="ciudad" 
                    placeholder="Madrid"
                    required
                  >
                </div>
                <div class="col-md-6 mb-3">
                  <label for="codigo_postal" class="form-label">Código Postal *</label>
                  <input 
                    type="text" 
                    class="form-control" 
                    id="codigo_postal" 
                    name="codigo_postal" 
                    placeholder="28001"
                    pattern="[0-9]{5}"
                    maxlength="5"
                    required
                  >
                </div>
              </div>
            </div>
            
            <hr>
            
            <!-- Método de pago -->
            <div class="mb-4">
              <h6 class="text-muted mb-3"><i class="bi bi-credit-card me-1"></i>Método de Pago</h6>
              
              <div class="form-check mb-2">
                <input class="form-check-input" type="radio" name="metodo_pago" id="tarjeta" value="tarjeta" checked>
                <label class="form-check-label d-flex align-items-center" for="tarjeta">
                  <i class="bi bi-credit-card me-2 text-primary"></i>
                  <div>
                    <strong>Tarjeta de crédito/débito</strong>
                    <small class="d-block text-muted">Visa, Mastercard, American Express</small>
                  </div>
                </label>
              </div>
              
              <div class="form-check mb-2">
                <input class="form-check-input" type="radio" name="metodo_pago" id="transferencia" value="transferencia">
                <label class="form-check-label d-flex align-items-center" for="transferencia">
                  <i class="bi bi-bank me-2 text-success"></i>
                  <div>
                    <strong>Transferencia bancaria</strong>
                    <small class="d-block text-muted">Recibirás los datos por email</small>
                  </div>
                </label>
              </div>
              
              <div class="form-check mb-3">
                <input class="form-check-input" type="radio" name="metodo_pago" id="paypal" value="paypal">
                <label class="form-check-label d-flex align-items-center" for="paypal">
                  <i class="bi bi-paypal me-2 text-info"></i>
                  <div>
                    <strong>PayPal</strong>
                    <small class="d-block text-muted">Pago seguro con tu cuenta PayPal</small>
                  </div>
                </label>
              </div>
            </div>
            
            <!-- Notas adicionales -->
            <div class="mb-3">
              <label for="notas" class="form-label">Notas del pedido (opcional)</label>
              <textarea 
                class="form-control" 
                id="notas" 
                name="notas" 
                rows="3" 
                placeholder="Instrucciones especiales, observaciones para la entrega, etc."
              ></textarea>
            </div>
            
            <hr>
            
            <!-- Términos y condiciones -->
            <div class="form-check mb-3">
              <input class="form-check-input" type="checkbox" id="terminos" name="terminos" required>
              <label class="form-check-label" for="terminos">
                Acepto los <a href="/terminos" target="_blank">términos y condiciones</a> y la 
                <a href="/privacidad" target="_blank">política de privacidad</a> *
              </label>
            </div>
            
            <div class="form-check mb-4">
              <input class="form-check-input" type="checkbox" id="newsletter" name="newsletter">
              <label class="form-check-label" for="newsletter">
                Deseo recibir ofertas y novedades por email
              </label>
            </div>
            
            <!-- Resumen del total en el botón -->
            <div class="card bg-light mb-3">
              <div class="card-body text-center">
                <div class="fw-bold text-muted mb-1">Total a pagar:</div>
                <div class="fs-4 fw-bold text-primary">€ <?= number_format($total, 2, ',', '.') ?></div>
              </div>
            </div>
            
            <button type="submit" class="btn btn-primary btn-lg w-100 mb-2">
              <i class="bi bi-lock me-2"></i>Confirmar y Pagar
            </button>
            
            <div class="text-center">
              <small class="text-muted">
                <i class="bi bi-shield-check me-1"></i>
                Pago 100% seguro y protegido
              </small>
            </div>
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
  const form = document.getElementById('checkout-form');
  
  // Validación mejorada del formulario
  form.addEventListener('submit', function(event) {
    let valid = true;
    let firstInvalidField = null;
    
    // Limpiar validaciones anteriores
    form.querySelectorAll('.is-invalid').forEach(el => {
      el.classList.remove('is-invalid');
    });
    
    // Validar campos requeridos
    form.querySelectorAll('[required]').forEach(input => {
      if (!input.value.trim()) {
        valid = false;
        input.classList.add('is-invalid');
        if (!firstInvalidField) firstInvalidField = input;
      }
    });
    
    // Validación específica del código postal
    const codigoPostal = document.getElementById('codigo_postal');
    if (codigoPostal.value && !/^\d{5}$/.test(codigoPostal.value)) {
      valid = false;
      codigoPostal.classList.add('is-invalid');
      if (!firstInvalidField) firstInvalidField = codigoPostal;
    }
    
    // Validación del email
    const email = document.getElementById('email');
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (email.value && !emailRegex.test(email.value)) {
      valid = false;
      email.classList.add('is-invalid');
      if (!firstInvalidField) firstInvalidField = email;
    }
    
    // Validar términos y condiciones
    const terminos = document.getElementById('terminos');
    if (!terminos.checked) {
      valid = false;
      terminos.classList.add('is-invalid');
      if (!firstInvalidField) firstInvalidField = terminos;
    }
    
    if (!valid) {
      event.preventDefault();
      
      // Hacer scroll al primer campo inválido
      if (firstInvalidField) {
        firstInvalidField.scrollIntoView({ 
          behavior: 'smooth', 
          block: 'center' 
        });
        setTimeout(() => firstInvalidField.focus(), 500);
      }
      
      // Mostrar mensaje de error más específico
      let errorMsg = 'Por favor, revisa los siguientes errores:\n';
      const invalidFields = form.querySelectorAll('.is-invalid');
      
      invalidFields.forEach(field => {
        const label = form.querySelector(`label[for="${field.id}"]`);
        if (label) {
          errorMsg += `• ${label.textContent.replace(' *', '')}\n`;
        }
      });
      
      alert(errorMsg);
    }
  });
  
  // Validación en tiempo real
  form.querySelectorAll('input, textarea').forEach(input => {
    input.addEventListener('input', function() {
      if (this.classList.contains('is-invalid')) {
        if (this.type === 'checkbox') {
          if (this.checked) this.classList.remove('is-invalid');
        } else if (this.value.trim()) {
          this.classList.remove('is-invalid');
        }
      }
    });
    
    input.addEventListener('blur', function() {
      // Validación específica por tipo de campo
      if (this.id === 'codigo_postal' && this.value) {
        if (!/^\d{5}$/.test(this.value)) {
          this.classList.add('is-invalid');
        }
      }
      
      if (this.id === 'email' && this.value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(this.value)) {
          this.classList.add('is-invalid');
        }
      }
    });
  });
  
  // Formatting automático del código postal
  document.getElementById('codigo_postal').addEventListener('input', function() {
    this.value = this.value.replace(/\D/g, '').substring(0, 5);
  });
  
  // Formatting del teléfono
  document.getElementById('telefono').addEventListener('input', function() {
    let value = this.value.replace(/\D/g, '');
    if (value.length > 0) {
      if (value.startsWith('34')) {
        value = '+' + value;
      } else if (!value.startsWith('+')) {
        value = '+34' + value;
      }
    }
    this.value = value;
  });
});
</script>