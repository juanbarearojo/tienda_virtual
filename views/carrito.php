<?php
// views/carrito.php
?>

<div class="container py-4">
  <h1>Mi Carrito de Compra</h1>

  <?php if (isset($_SESSION['mensaje'])): ?>
    <div class="alert alert-<?= $_SESSION['mensaje']['tipo'] ?> alert-dismissible fade show" role="alert">
      <?= $_SESSION['mensaje']['texto'] ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['mensaje']); ?>
  <?php endif; ?>

  <?php if (empty($items)): ?>
    <div class="alert alert-info">
      Tu carrito está vacío. <a href="/" class="alert-link">Volver al catálogo</a>
    </div>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table table-striped">
        <thead class="table-dark">
          <tr>
            <th>Producto</th>
            <th>Precio unitario</th>
            <th>Cantidad</th>
            <th>Subtotal</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($items as $item): ?>
            <tr class="align-middle" data-item-id="<?= $item->id_item ?>">
              <td>
                <div class="d-flex align-items-center">
                  <?php if ($item->producto->imagen_url): ?>
                    <img 
                      src="<?= htmlspecialchars($item->producto->getRutaImagen()) ?>" 
                      alt="<?= htmlspecialchars($item->producto->nombre) ?>"
                      class="me-3"
                      style="width:64px; height:64px; object-fit:cover; border-radius:4px;"
                    >
                  <?php else: ?>
                    <div 
                      class="me-3"
                      style="width:64px; height:64px; background:#e9ecef; display:flex; align-items:center; justify-content:center; color:#6c757d; font-size:.8rem; border-radius:4px;"
                    >
                      Sin imagen
                    </div>
                  <?php endif; ?>
                  <div>
                    <h5 class="mb-0"><?= htmlspecialchars($item->producto->nombre) ?></h5>
                    <?php if ($item->producto->categoria): ?>
                      <small class="text-muted"><?= htmlspecialchars($item->producto->categoria) ?></small>
                    <?php endif; ?>
                  </div>
                </div>
              </td>
              <td>€ <?= $item->producto->getPrecioFormateado() ?></td>
              <td style="width: 150px;">
                <div class="input-group">
                  <button 
                    class="btn btn-outline-secondary btn-sm btn-decrement" 
                    type="button"
                    <?= $item->cantidad <= 1 ? 'disabled' : '' ?>
                  >-</button>
                  <input 
                    type="number" 
                    class="form-control form-control-sm text-center cantidad-input" 
                    value="<?= $item->cantidad ?>" 
                    min="1" 
                    max="<?= $item->producto->stock ?>"
                    data-item-id="<?= $item->id_item ?>"
                  >
                  <button 
                    class="btn btn-outline-secondary btn-sm btn-increment" 
                    type="button"
                    <?= $item->cantidad >= $item->producto->stock ? 'disabled' : '' ?>
                  >+</button>
                </div>
                <small class="d-block text-muted mt-1">
                  Stock disponible: <?= $item->producto->stock ?>
                </small>
              </td>
              <td class="fw-bold">
                € <?= number_format($item->producto->precio * $item->cantidad, 2, ',', '.') ?>
              </td>
              <td>
                <button 
                  class="btn btn-danger btn-sm btn-remove-item"
                  data-item-id="<?= $item->id_item ?>"
                >
                  <i class="bi bi-trash"></i> Eliminar
                </button>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="3" class="text-end fw-bold">Total:</td>
            <td class="fw-bold fs-5">€ <?= number_format($total, 2, ',', '.') ?></td>
            <td></td>
          </tr>
        </tfoot>
      </table>
    </div>

    <div class="d-flex justify-content-between mt-4">
      <div>
        <a href="/" class="btn btn-secondary">
          <i class="bi bi-arrow-left"></i> Seguir comprando
        </a>
        <button id="btn-empty-cart" class="btn btn-outline-danger ms-2">
          <i class="bi bi-trash"></i> Vaciar carrito
        </button>
      </div>
      <a href="/checkout" class="btn btn-primary">
        <i class="bi bi-cart-check"></i> Finalizar compra
      </a>
    </div>
  <?php endif; ?>
</div>

<!-- JavaScript para manejar interacciones del carrito -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Incrementar cantidad
  document.querySelectorAll('.btn-increment').forEach(btn => {
    btn.addEventListener('click', function() {
      const input = this.parentNode.querySelector('.cantidad-input');
      const max = parseInt(input.getAttribute('max'), 10);
      let value = parseInt(input.value, 10);
      
      if (value < max) {
        value++;
        input.value = value;
        updateCartItem(input.dataset.itemId, value);
      }
      
      // Actualizar estado de botones
      updateButtonState(this.parentNode, value, max);
    });
  });
  
  // Decrementar cantidad
  document.querySelectorAll('.btn-decrement').forEach(btn => {
    btn.addEventListener('click', function() {
      const input = this.parentNode.querySelector('.cantidad-input');
      let value = parseInt(input.value, 10);
      const max = parseInt(input.getAttribute('max'), 10);
      
      if (value > 1) {
        value--;
        input.value = value;
        updateCartItem(input.dataset.itemId, value);
      }
      
      // Actualizar estado de botones
      updateButtonState(this.parentNode, value, max);
    });
  });
  
  // Actualizar al cambiar directamente el input
  document.querySelectorAll('.cantidad-input').forEach(input => {
    input.addEventListener('change', function() {
      let value = parseInt(this.value, 10);
      const max = parseInt(this.getAttribute('max'), 10);
      
      // Validar valor
      if (isNaN(value) || value < 1) value = 1;
      if (value > max) value = max;
      
      this.value = value;
      updateCartItem(this.dataset.itemId, value);
      
      // Actualizar estado de botones
      updateButtonState(this.parentNode, value, max);
    });
  });
  
  // Eliminar item
  document.querySelectorAll('.btn-remove-item').forEach(btn => {
    btn.addEventListener('click', function() {
      if (confirm('¿Estás seguro de que deseas eliminar este producto del carrito?')) {
        removeFromCart(this.dataset.itemId);
      }
    });
  });
  
  // Vaciar carrito
  document.getElementById('btn-empty-cart')?.addEventListener('click', function() {
    if (confirm('¿Estás seguro de que deseas vaciar el carrito?')) {
      emptyCart();
    }
  });
  
  // Función auxiliar para actualizar estado de botones
  function updateButtonState(parent, value, max) {
    const decrementBtn = parent.querySelector('.btn-decrement');
    const incrementBtn = parent.querySelector('.btn-increment');
    
    decrementBtn.disabled = value <= 1;
    incrementBtn.disabled = value >= max;
  }
  
  // Actualizar item en el carrito
  function updateCartItem(itemId, cantidad) {
    const formData = new FormData();
    formData.append('item_id', itemId);
    formData.append('cantidad', cantidad);
    
    fetch('/actualizar-carrito', {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        // Actualizar totales en la página
        updateCartUI(data);
      } else {
        alert(data.error || 'Error al actualizar el carrito');
        location.reload(); // Recargar en caso de error
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Error al comunicarse con el servidor');
    });
  }
  
  // Eliminar del carrito
  function removeFromCart(itemId) {
    const formData = new FormData();
    formData.append('item_id', itemId);
    
    fetch('/eliminar-del-carrito', {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        // Eliminar la fila de la tabla
        const row = document.querySelector(`tr[data-item-id="${itemId}"]`);
        if (row) row.remove();
        
        // Actualizar totales o recargar si ya no hay items
        if (data.itemCount > 0) {
          updateCartUI(data);
        } else {
          location.reload(); // Recargar para mostrar mensaje de carrito vacío
        }
      } else {
        alert(data.error || 'Error al eliminar el producto');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Error al comunicarse con el servidor');
    });
  }
  
  // Vaciar carrito
  function emptyCart() {
    fetch('/vaciar-carrito', {
      method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        location.reload(); // Recargar para mostrar mensaje de carrito vacío
      } else {
        alert(data.error || 'Error al vaciar el carrito');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Error al comunicarse con el servidor');
    });
  }
  
  // Actualizar interfaz con nuevos valores
  function updateCartUI(data) {
    // Actualizar el total
    if (data.total !== undefined) {
      const totalCell = document.querySelector('tfoot td:nth-child(2)');
      if (totalCell) {
        totalCell.textContent = `€ ${formatNumber(data.total)}`;
      }
    }
    
    // Actualizar subtotales y otros elementos si es necesario
    if (data.items) {
      data.items.forEach(item => {
        const row = document.querySelector(`tr[data-item-id="${item.id_item}"]`);
        if (row) {
          const subtotalCell = row.querySelector('td:nth-child(4)');
          if (subtotalCell) {
            const subtotal = item.producto.precio * item.cantidad;
            subtotalCell.textContent = `€ ${formatNumber(subtotal)}`;
          }
        }
      });
    }
  }
  
  // Función auxiliar para formatear números
  function formatNumber(number) {
    return new Intl.NumberFormat('es-ES', {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2
    }).format(number);
  }
});
</script>