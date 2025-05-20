<?php
// views/register.php
?>
<div class="container py-4">
  <h2>Registro de Usuario</h2>

  <?php if (!empty($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <?= htmlspecialchars($error) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
  <?php endif; ?>

  <form method="post" action="/register">
    <div class="mb-3">
      <label for="name" class="form-label">Nombre</label>
      <input 
        type="text" 
        id="name" 
        name="name" 
        class="form-control" 
        value="<?= htmlspecialchars($post['name'] ?? '') ?>" 
        required
      >
    </div>

    <div class="mb-3">
      <label for="email" class="form-label">Email</label>
      <input 
        type="email" 
        id="email" 
        name="email" 
        class="form-control" 
        value="<?= htmlspecialchars($post['email'] ?? '') ?>" 
        required
      >
    </div>

    <div class="mb-3">
      <label for="password" class="form-label">Contraseña</label>
      <input 
        type="password" 
        id="password" 
        name="password" 
        class="form-control" 
        required
      >
    </div>

    <div class="mb-3">
      <label for="nif" class="form-label">NIF</label>
      <input 
        type="text" 
        id="nif" 
        name="nif" 
        class="form-control" 
        value="<?= htmlspecialchars($post['nif'] ?? '') ?>"
      >
    </div>

    <div class="mb-3">
      <label for="telefono" class="form-label">Teléfono</label>
      <input 
        type="tel" 
        id="telefono" 
        name="telefono" 
        class="form-control" 
        value="<?= htmlspecialchars($post['telefono'] ?? '') ?>"
      >
    </div>

    <div class="mb-3">
      <label for="domicilio" class="form-label">Domicilio</label>
      <input 
        type="text" 
        id="domicilio" 
        name="domicilio" 
        class="form-control" 
        value="<?= htmlspecialchars($post['domicilio'] ?? '') ?>"
      >
    </div>

    <button type="submit" class="btn btn-primary">Registrar</button>
  </form>

  <p class="mt-3">
    ¿Ya tienes cuenta? <a href="/login">Inicia sesión</a>
  </p>
</div>
