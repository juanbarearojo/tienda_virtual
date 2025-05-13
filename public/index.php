<?php
// public/index.php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/CatalogoController.php';

$db = getConnection();   // usa la función de config/database.php
session_start();

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

switch ($uri) {
    case '/login':
        $auth = new AuthController($db);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth->login($_POST);
        } else {
            $auth->showLogin();
        }
        break;

    case '/register':
        $auth = new AuthController($db);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth->register($_POST);
        } else {
            $auth->showRegister();
        }
        break;

    case '/logout':
        (new AuthController($db))->logout();
        break;

    default:
        // Ruta por defecto: si no hay sesión, mostrar login
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        // Usuario autenticado: mostrar catálogo u otras rutas privadas
        (new CatalogoController($db))->index();
        break;
}
