<?php
// views/login.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Iniciar Sesión</title>
  <!-- Bootstrap CSS -->
  <link 
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" 
    rel="stylesheet" 
    integrity="sha384-ENjdO4Dr2bkBIFxQpeoYz1HXUtw4Z9FQcNHb9pSOnnUOmUcww7on3RYdg4Va+PmT" 
    crossorigin="anonymous"
  >
</head>
<body>

<div class="container py-4" style="max-width: 400px;">
  <h2 class="mb-4 text-center">Iniciar Sesión</h2>

  <?php if (!empty($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <?= htmlspecialchars($error) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
  <?php endif; ?>

  <form method="post" action="/login" class="needs-validation" novalidate>
    <div class="mb-3">
      <label for="email" class="form-label">Email</label>
      <input 
        type="email" 
        id="email"
        name="email" 
        class="form-control" 
        required
        placeholder="tu@ejemplo.com"
      >
      <div class="invalid-feedback">
        Por favor, introduce un email válido.
      </div>
    </div>

    <div class="mb-3">
      <label for="password" class="form-label">Contraseña</label>
      <input 
        type="password" 
        id="password"
        name="password" 
        class="form-control" 
        required
        placeholder="••••••••"
      >
      <div class="invalid-feedback">
        La contraseña es obligatoria.
      </div>
    </div>

    <button type="submit" class="btn btn-primary w-100">Entrar</button>
  </form>

  <p class="mt-3 text-center">
    ¿No tienes cuenta?
    <a href="/register" class="link-primary">Regístrate aquí</a>
  </p>
</div>

<!-- Bootstrap JS Bundle (incluye Popper) -->
<script 
  src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" 
  integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+AMvyTG6H72VfN0E0p5V4dKGN4xJR" 
  crossorigin="anonymous"
></script>

<!-- JavaScript de validación Bootstrap -->
<script>
  (function () {
    'use strict'
    document.querySelectorAll('.needs-validation').forEach(form => {
      form.addEventListener('submit', event => {
        if (!form.checkValidity()) {
          event.preventDefault()
          event.stopPropagation()
        }
        form.classList.add('was-validated')
      }, false)
    })
  })()
</script>

</body>
</html>
