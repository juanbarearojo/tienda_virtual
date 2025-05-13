<?php
// Actualización del public/index.php para incluir las rutas del carrito

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/CatalogoController.php';
require_once __DIR__ . '/../controllers/CarritoController.php';

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
        
    // Rutas del carrito
    case '/carrito':
        $carritoController = new CarritoController($db);
        $carritoController->index();
        break;
        
    case '/agregar-al-carrito':
        $carritoController = new CarritoController($db);
        $carritoController->addToCart();
        break;
        
    case '/actualizar-carrito':
        $carritoController = new CarritoController($db);
        $carritoController->updateCartItem();
        break;
        
    case '/eliminar-del-carrito':
        $carritoController = new CarritoController($db);
        $carritoController->removeFromCart();
        break;
        
    case '/vaciar-carrito':
        $carritoController = new CarritoController($db);
        $carritoController->emptyCart();
        break;
        
    case '/checkout':
        $carritoController = new CarritoController($db);
        $carritoController->checkout();
        break;
        
    case '/confirmar-checkout':
        $carritoController = new CarritoController($db);
        $carritoController->confirmCheckout();
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