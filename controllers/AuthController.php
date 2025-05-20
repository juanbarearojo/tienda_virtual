<?php
// controllers/AuthController.php

require_once __DIR__ . '/../models/users.php';

class AuthController {
    private $userModel;

    public function __construct(PDO $db) {
        $this->userModel = new User($db);

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Mostrar formulario de login
     */
    public function showLogin(): void {
        include __DIR__ . '/../views/header.php';
        include __DIR__ . '/../views/login.php';
        include __DIR__ . '/../views/footer.php';
    }

    /**
     * Procesar login
     */
    public function login(array $post): void {
        $email    = trim($post['email']    ?? '');
        $password = $post['password']      ?? '';

        $user = $this->userModel->findByEmail($email);

        if ($user && password_verify($password, $user['password'])) {
            // Guardamos datos de sesión
            $_SESSION['user_id']   = $user['id_usuario'];
            $_SESSION['user_name'] = $user['nombre'];

            // Redirigimos al área privada
            header('Location: /');
            exit;
        }

        // Credenciales inválidas
        $error = 'Email o contraseña incorrecta.';
        include __DIR__ . '/../views/header.php';
        include __DIR__ . '/../views/login.php';
        include __DIR__ . '/../views/footer.php';
    }

    /**
     * Mostrar formulario de registro
     */
    public function showRegister(): void {
        include __DIR__ . '/../views/header.php';
        include __DIR__ . '/../views/register.php';
        include __DIR__ . '/../views/footer.php';
    }

    /**
     * Procesar registro
     */
    public function register(array $post): void {
        // Campos básicos
        $name      = trim($post['name']     ?? '');
        $email     = trim($post['email']    ?? '');
        $password  = $post['password']      ?? '';

        // Nuevos campos
        $nif       = trim($post['nif']      ?? '');
        $telefono  = trim($post['telefono'] ?? '');
        $domicilio = trim($post['domicilio']?? '');

        // Reusar datos en la vista en caso de error
        // (la vista register.php usa $post[...] para rellenar valores)
        $_REQUEST = $post;

        // 1) Verificar email único
        if ($this->userModel->findByEmail($email)) {
            $error = 'El email ya está registrado.';
            include __DIR__ . '/../views/header.php';
            include __DIR__ . '/../views/register.php';
            include __DIR__ . '/../views/footer.php';
            return;
        }

        // 2) Verificar NIF único (si se ha proporcionado)
        if ($nif !== '' && $this->userModel->findByNIF($nif)) {
            $error = 'El NIF ya está registrado.';
            include __DIR__ . '/../views/header.php';
            include __DIR__ . '/../views/register.php';
            include __DIR__ . '/../views/footer.php';
            return;
        }

        // 3) Verificar teléfono único (si se ha proporcionado)
        if ($telefono !== '' && $this->userModel->findByTelefono($telefono)) {
            $error = 'El teléfono ya está registrado.';
            include __DIR__ . '/../views/header.php';
            include __DIR__ . '/../views/register.php';
            include __DIR__ . '/../views/footer.php';
            return;
        }

        // Hasheamos la contraseña
        $hash = password_hash($password, PASSWORD_BCRYPT);

        // Insertamos el usuario
        $created = $this->userModel->create(
            $name,
            $email,
            $hash,
            $nif ?: null,
            $telefono ?: null,
            $domicilio ?: null
        );

        if ($created) {
            // Registro exitoso → redirigir a login
            header('Location: /login');
            exit;
        }

        // Error genérico al crear
        $error = 'Error al registrar el usuario.';
        include __DIR__ . '/../views/header.php';
        include __DIR__ . '/../views/register.php';
        include __DIR__ . '/../views/footer.php';
    }

    /**
     * Logout y destrucción de sesión
     */
    public function logout(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();
        session_destroy();
        header('Location: /login');
        exit;
    }
}
