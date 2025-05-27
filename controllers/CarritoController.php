<?php
// controllers/CarritoController.php

require_once __DIR__ . '/../models/Carrito.php';
require_once __DIR__ . '/../models/ItemCarrito.php';
require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../models/users.php';

class CarritoController {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Helper para mostrar un pop-up y redirigir atrás
     * Usado solo en addToCart()
     */
    private function popupResponse(array $data, int $statusCode = 200) {
        http_response_code($statusCode);
        $_SESSION['popup'] = [
            'tipo'    => $data['success'] ? 'success' : 'error',
            'mensaje' => $data['message'] ?? $data['error'] ?? 'Operación completada.'
        ];
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        header("Location: $referer");
        exit;
    }

    /**
     * Helper para enviar respuestas JSON
     */
    private function jsonResponse(array $data, int $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    // Mostrar el carrito del usuario
    public function index() {
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $userId  = $_SESSION['user_id'];
        $carrito = Carrito::getActive($this->db, $userId);
        $items   = $carrito->getItems($this->db);
        $total   = $carrito->getTotal($this->db);

        include __DIR__ . '/../views/header.php';
        include __DIR__ . '/../views/carrito.php';
        include __DIR__ . '/../views/footer.php';
    }

    // Añadir un producto al carrito (pop-up en vez de JSON)
    public function addToCart() {
        if (empty($_SESSION['user_id'])) {
            $this->popupResponse(['success' => false, 'error' => 'No autorizado'], 401);
        }

        if (empty($_POST['producto_id']) || !isset($_POST['cantidad'])) {
            $this->popupResponse(['success' => false, 'error' => 'Datos incompletos'], 400);
        }

        $productoId = (int) $_POST['producto_id'];
        $cantidad   = (int) $_POST['cantidad'];
        if ($cantidad <= 0) {
            $this->popupResponse(['success' => false, 'error' => 'Cantidad inválida'], 400);
        }

        // Verificar existencia y stock
        $stmt = $this->db->prepare("SELECT stock FROM Productos WHERE id_producto = :id");
        $stmt->execute(['id' => $productoId]);
        $prod = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$prod) {
            $this->popupResponse(['success' => false, 'error' => 'Producto no encontrado'], 404);
        }
        if ($prod['stock'] < $cantidad) {
            $this->popupResponse([
                'success'         => false,
                'error'           => 'No hay suficiente stock disponible',
                'stockDisponible' => $prod['stock']
            ], 400);
        }

        // Añadir al carrito
        $carrito = Carrito::getActive($this->db, $_SESSION['user_id']);
        $success = ItemCarrito::add($this->db, $carrito->id_carrito, $productoId, $cantidad);

        if ($success) {
            $this->popupResponse([
                'success' => true,
                'message' => '¡Producto añadido al carrito!'
            ]);
        } else {
            $this->popupResponse(['success' => false, 'error' => 'Error al añadir producto'], 500);
        }
    }

    // Actualizar cantidad de un producto en el carrito (JSON)
    public function updateCartItem() {
        if (empty($_SESSION['user_id'])) {
            $this->jsonResponse(['success' => false, 'error' => 'No autorizado'], 401);
        }
        if (empty($_POST['item_id']) || !isset($_POST['cantidad'])) {
            $this->jsonResponse(['success' => false, 'error' => 'Datos incompletos'], 400);
        }

        $itemId   = (int) $_POST['item_id'];
        $cantidad = (int) $_POST['cantidad'];
        $item     = ItemCarrito::get($this->db, $itemId);

        if (!$item) {
            $this->jsonResponse(['success' => false, 'error' => 'Item no encontrado'], 404);
        }
        $carrito = Carrito::getActive($this->db, $_SESSION['user_id']);
        if ($item->id_carrito !== $carrito->id_carrito) {
            $this->jsonResponse(['success' => false, 'error' => 'No autorizado'], 401);
        }

        if ($cantidad <= 0) {
            $success = ItemCarrito::remove($this->db, $itemId);
        } else {
            $stmt = $this->db->prepare("SELECT stock FROM Productos WHERE id_producto = :id");
            $stmt->execute(['id' => $item->id_producto]);
            $prod = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($prod && $prod['stock'] < $cantidad) {
                $this->jsonResponse([
                    'success'         => false,
                    'error'           => 'No hay suficiente stock disponible',
                    'stockDisponible' => $prod['stock']
                ], 400);
            }
            $success = ItemCarrito::updateQuantity($this->db, $itemId, $cantidad);
        }

        if ($success) {
            $itemCount = $carrito->getItemCount($this->db);
            $total     = $carrito->getTotal($this->db);
            $items     = $carrito->getItems($this->db);
            $this->jsonResponse([
                'success'   => true,
                'message'   => 'Carrito actualizado',
                'itemCount' => $itemCount,
                'total'     => $total,
                'items'     => $items
            ]);
        } else {
            $this->jsonResponse(['success' => false, 'error' => 'Error al actualizar carrito'], 500);
        }
    }

    // Eliminar un producto del carrito (JSON)
    public function removeFromCart() {
        if (empty($_SESSION['user_id'])) {
            $this->jsonResponse(['success' => false, 'error' => 'No autorizado'], 401);
        }
        if (empty($_POST['item_id'])) {
            $this->jsonResponse(['success' => false, 'error' => 'Datos incompletos'], 400);
        }

        $itemId = (int) $_POST['item_id'];
        $item   = ItemCarrito::get($this->db, $itemId);
        if (!$item) {
            $this->jsonResponse(['success' => false, 'error' => 'Item no encontrado'], 404);
        }

        $carrito = Carrito::getActive($this->db, $_SESSION['user_id']);
        if ($item->id_carrito !== $carrito->id_carrito) {
            $this->jsonResponse(['success' => false, 'error' => 'No autorizado'], 401);
        }

        $success = ItemCarrito::remove($this->db, $itemId);
        if ($success) {
            $this->jsonResponse([
                'success'   => true,
                'message'   => 'Producto eliminado del carrito',
                'itemCount' => $carrito->getItemCount($this->db),
                'total'     => $carrito->getTotal($this->db)
            ]);
        } else {
            $this->jsonResponse(['success' => false, 'error' => 'Error al eliminar producto'], 500);
        }
    }

    // Vaciar carrito (JSON)
    public function emptyCart() {
        if (empty($_SESSION['user_id'])) {
            $this->jsonResponse(['success' => false, 'error' => 'No autorizado'], 401);
        }

        $carrito = Carrito::getActive($this->db, $_SESSION['user_id']);
        $success = ItemCarrito::emptyCart($this->db, $carrito->id_carrito);

        if ($success) {
            $this->jsonResponse([
                'success' => true,
                'message' => 'Carrito vaciado correctamente'
            ]);
        } else {
            $this->jsonResponse(['success' => false, 'error' => 'Error al vaciar carrito'], 500);
        }
    }

    // Mostrar página de checkout
    public function checkout() {
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $userId  = $_SESSION['user_id'];
        $carrito = Carrito::getActive($this->db, $userId);
        $items   = $carrito->getItems($this->db);

        if (empty($items)) {
            $_SESSION['mensaje'] = [
                'tipo'  => 'warning',
                'texto' => 'Tu carrito está vacío'
            ];
            header('Location: /carrito');
            exit;
        }

        // Obtener datos del usuario para prellenar el formulario
        $stmt = $this->db->prepare("
            SELECT nombre, email, telefono, NIF, domicilio 
            FROM Usuarios 
            WHERE id_usuario = :id_usuario
        ");
        $stmt->execute(['id_usuario' => $userId]);
        $usuario = $stmt->fetchObject();

        $total = $carrito->getTotal($this->db);

        include __DIR__ . '/../views/header.php';
        include __DIR__ . '/../views/checkout.php';
        include __DIR__ . '/../views/footer.php';
    }

    // Confirmar compra (procesar pago)
    public function confirmCheckout() {
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        // Validar datos del formulario
        $requiredFields = ['nombre', 'email', 'direccion', 'ciudad', 'codigo_postal', 'metodo_pago'];
        $missingFields = [];
        
        foreach ($requiredFields as $field) {
            if (empty($_POST[$field])) {
                $missingFields[] = $field;
            }
        }
        
        if (!empty($missingFields)) {
            $_SESSION['mensaje'] = [
                'tipo'  => 'danger',
                'texto' => 'Faltan campos obligatorios: ' . implode(', ', $missingFields)
            ];
            header('Location: /checkout');
            exit;
        }

        // Validar términos y condiciones
        if (empty($_POST['terminos'])) {
            $_SESSION['mensaje'] = [
                'tipo'  => 'danger',
                'texto' => 'Debes aceptar los términos y condiciones para continuar'
            ];
            header('Location: /checkout');
            exit;
        }

        // Validar formato del código postal
        if (!preg_match('/^\d{5}$/', $_POST['codigo_postal'])) {
            $_SESSION['mensaje'] = [
                'tipo'  => 'danger',
                'texto' => 'El código postal debe tener exactamente 5 dígitos'
            ];
            header('Location: /checkout');
            exit;
        }

        $userId  = $_SESSION['user_id'];
        $carrito = Carrito::getActive($this->db, $userId);
        $items   = $carrito->getItems($this->db);

        if (empty($items)) {
            $_SESSION['mensaje'] = [
                'tipo'  => 'warning',
                'texto' => 'Tu carrito está vacío'
            ];
            header('Location: /carrito');
            exit;
        }

        $this->db->beginTransaction();
        try {
            // Verificar stock antes de procesar
            foreach ($items as $item) {
                $stmt = $this->db->prepare("SELECT stock FROM Productos WHERE id_producto = :id");
                $stmt->execute(['id' => $item->id_producto]);
                $producto = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$producto || $producto['stock'] < $item->cantidad) {
                    throw new Exception("Stock insuficiente para el producto: " . $item->producto->nombre);
                }
            }

            // Actualizar stock
            foreach ($items as $item) {
                $stmt = $this->db->prepare("
                    UPDATE Productos
                    SET stock = stock - :cantidad
                    WHERE id_producto = :id_producto
                ");
                $stmt->execute([
                    'cantidad'    => $item->cantidad,
                    'id_producto' => $item->id_producto
                ]);
            }

            // Actualizar datos del usuario si han cambiado
            $stmt = $this->db->prepare("
                UPDATE Usuarios 
                SET nombre = :nombre, 
                    telefono = :telefono, 
                    NIF = :nif, 
                    domicilio = :domicilio
                WHERE id_usuario = :id_usuario
            ");
            $stmt->execute([
                'nombre'     => $_POST['nombre'],
                'telefono'   => $_POST['telefono'] ?? null,
                'nif'        => $_POST['nif'] ?? null,
                'domicilio'  => $_POST['direccion'],
                'id_usuario' => $userId
            ]);

            // Aquí podrías crear una tabla de pedidos para guardar la información completa del pedido
            // Por ahora solo finalizamos el carrito
            $carrito->finalize($this->db);
            
            $this->db->commit();

            // Preparar mensaje de confirmación personalizado
            $metodoPago = $this->getMetodoPagoTexto($_POST['metodo_pago']);
            $mensajeConfirmacion = "¡Gracias por tu compra, " . htmlspecialchars($_POST['nombre']) . "! ";
            $mensajeConfirmacion .= "Tu pedido ha sido procesado correctamente. ";
            $mensajeConfirmacion .= "Método de pago seleccionado: " . $metodoPago . ". ";
            
            if ($_POST['metodo_pago'] === 'transferencia') {
                $mensajeConfirmacion .= "Recibirás un email con los datos bancarios para realizar la transferencia.";
            } else {
                $mensajeConfirmacion .= "Recibirás un email de confirmación en breve.";
            }

            $_SESSION['mensaje'] = [
                'tipo'  => 'success',
                'texto' => $mensajeConfirmacion
            ];
            
            // Si hay newsletter, aquí podrías procesarla
            if (!empty($_POST['newsletter'])) {
                // Lógica para suscribir al newsletter
            }
            
            header('Location: /catalogo');
            exit;

        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['mensaje'] = [
                'tipo'  => 'danger',
                'texto' => 'Ha ocurrido un error al procesar tu pedido: ' . $e->getMessage()
            ];
            header('Location: /checkout');
            exit;
        }
    }

    /**
     * Helper para obtener el texto del método de pago
     */
    private function getMetodoPagoTexto($metodo) {
        switch ($metodo) {
            case 'tarjeta':
                return 'Tarjeta de crédito/débito';
            case 'transferencia':
                return 'Transferencia bancaria';
            case 'paypal':
                return 'PayPal';
            default:
                return 'Método desconocido';
        }
    }
}