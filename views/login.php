<?php
// views/login.php
?>
<h2>Iniciar Sesión</h2>
<?php if (!empty($error)): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>
<form method="post" action="/login">
    <label>Email: <input type="email" name="email" required></label><br>
    <label>Contraseña: <input type="password" name="password" required></label><br>
    <button type="submit">Entrar</button>
</form>
<p>¿No tienes cuenta? <a href="/register">Regístrate aquí</a></p>
