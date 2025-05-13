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

    // Mostrar formulario de login
    public function showLogin() {
        include __DIR__ . '/../views/header.php';
        include __DIR__ . '/../views/login.php';
        include __DIR__ . '/../views/footer.php';
    }

    // Procesar login
    public function login(array $post) {
        $email    = trim($post['email'] ?? '');
        $password = $post['password'] ?? '';

        $user = $this->userModel->findByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']   = $user['id_usuario'];
            $_SESSION['user_name'] = $user['nombre'];
            header('Location: /');
            exit;
        }

        $error = 'Email o contraseña incorrecta.';
        include __DIR__ . '/../views/header.php';
        include __DIR__ . '/../views/login.php';
        include __DIR__ . '/../views/footer.php';
    }

    // Mostrar formulario de registro
    public function showRegister() {
        include __DIR__ . '/../views/header.php';
        include __DIR__ . '/../views/register.php';
        include __DIR__ . '/../views/footer.php';
    }

    // Procesar registro
    public function register(array $post) {
        $name     = trim($post['name']  ?? '');
        $email    = trim($post['email'] ?? '');
        $password = $post['password']   ?? '';

        if ($this->userModel->findByEmail($email)) {
            $error = 'El email ya está registrado.';
            include __DIR__ . '/../views/header.php';
            include __DIR__ . '/../views/register.php';
            include __DIR__ . '/../views/footer.php';
            return;
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);
        if ($this->userModel->create($name, $email, $hash)) {
            header('Location: /login');
            exit;
        }

        $error = 'Error al registrar el usuario.';
        include __DIR__ . '/../views/header.php';
        include __DIR__ . '/../views/register.php';
        include __DIR__ . '/../views/footer.php';
    }

    // Logout
    public function logout() {
        session_start();
        session_unset();
        session_destroy();
        header('Location: /login');
        exit;
    }
}
