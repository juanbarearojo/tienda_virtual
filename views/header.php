<?php
// views/header.php

// Inicia la sesión para usar $_SESSION
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Productos</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php if (!empty($_SESSION['popup'])): ?>
    <script>
        // Tipo: 'success' o 'error' (por si quieres personalizar estilos más adelante)
        const tipo = <?= json_encode($_SESSION['popup']['tipo']) ?>;
        const msg  = <?= json_encode($_SESSION['popup']['mensaje']) ?>;
        // Alerta básica; puedes sustituir por SweetAlert, modales de Bootstrap, etc.
        alert(msg);
    </script>
    <?php unset($_SESSION['popup']); ?>
<?php endif; ?>

<!-- Barra de navegación -->
<nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
  <div class="container-fluid">
    <!-- Aquí hemos eliminado el enlace de marca "Mi Tienda" -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Alternar navegación">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarMain">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link" href="/catalogo">Catálogo</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/carrito">Carrito 
            <?php if (!empty($_SESSION['user_id'])): ?>
              <span class="badge bg-secondary">
                <?= isset($_SESSION['cart_count']) ? (int)$_SESSION['cart_count'] : '' ?>
              </span>
            <?php endif; ?>
          </a>
        </li>
      </ul>

      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <?php if (!empty($_SESSION['user_id'])): ?>
          <li class="nav-item">
            <a class="nav-link" href="/perfil"><?= htmlspecialchars($_SESSION['user_name'] ?? 'Perfil') ?></a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/logout">Cerrar sesión</a>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a class="nav-link" href="/login">Iniciar sesión</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/register">Registrarse</a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

