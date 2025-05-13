<?php
// views/register.php
?>
<h2>Registro de Usuario</h2>
<?php if (!empty($error)): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>
<form method="post" action="/register">
    <label>Nombre: <input type="text" name="name" required></label><br>
    <label>Email:  <input type="email" name="email" required></label><br>
    <label>Contraseña: <input type="password" name="password" required></label><br>
    <button type="submit">Registrar</button>
</form>
<p>¿Ya tienes cuenta? <a href="/login">Inicia sesión</a></p>
