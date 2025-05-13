<?php
// controllers/CarritoController.php

require_once __DIR__ . '/../models/Carrito.php';
require_once __DIR__ . '/../models/ItemCarrito.php';
require_once __DIR__ . '/../models/Producto.php';

class CarritoController {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    // Mostrar el carrito del usuario
    public function index() {
        // Verificar que el usuario está autenticado
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        $carrito = Carrito::getActive($this->db, $userId);
        $items = $carrito->getItems($this->db);
        $total = $carrito->getTotal($this->db);
        
        // Incluir vista
        include __DIR__ . '/../views/header.php';
        include __DIR__ . '/../views/carrito.php';
        include __DIR__ . '/../views/footer.php';
    }
    
    // Añadir un producto al carrito
    public function addToCart() {
        // Verificar que el usuario está autenticado
        if (empty($_SESSION['user_id'])) {
            $this->jsonResponse(['success' => false, 'error' => 'No autorizado'], 401);
            return;
        }
        
        // Verificar datos de entrada
        if (empty($_POST['producto_id']) || !isset($_POST['cantidad'])) {
            $this->jsonResponse(['success' => false, 'error' => 'Datos incompletos'], 400);
            return;
        }
        
        $productoId = (int)$_POST['producto_id'];
        $cantidad = (int)$_POST['cantidad'];
        
        if ($cantidad <= 0) {
            $this->jsonResponse(['success' => false, 'error' => 'Cantidad inválida'], 400);
            return;
        }
        
        // Verificar que el producto existe y tiene stock suficiente
        $stmt = $this->db->prepare("SELECT id_producto, stock FROM Productos WHERE id_producto = :id");
        $stmt->execute(['id' => $productoId]);
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$producto) {
            $this->jsonResponse(['success' => false, 'error' => 'Producto no encontrado'], 404);
            return;
        }
        
        if ($producto['stock'] < $cantidad) {
            $this->jsonResponse([
                'success' => false, 
                'error' => 'No hay suficiente stock disponible',
                'stockDisponible' => $producto['stock']
            ], 400);
            return;
        }
        
        // Obtener carrito activo del usuario
        $userId = $_SESSION['user_id'];
        $carrito = Carrito::getActive($this->db, $userId);
        
        // Añadir producto al carrito
        $success = ItemCarrito::add($this->db, $carrito->id_carrito, $productoId, $cantidad);
        
        if ($success) {
            // Obtener datos actualizados del carrito
            $itemCount = $carrito->getItemCount($this->db);
            $total = $carrito->getTotal($this->db);
            
            $this->jsonResponse([
                'success' => true,
                'message' => 'Producto añadido al carrito',
                'itemCount' => $itemCount,
                'total' => $total
            ]);
        } else {
            $this->jsonResponse(['success' => false, 'error' => 'Error al añadir producto'], 500);
        }
    }
    
    // Actualizar cantidad de un producto en el carrito
    public function updateCartItem() {
        // Verificar que el usuario está autenticado
        if (empty($_SESSION['user_id'])) {
            $this->jsonResponse(['success' => false, 'error' => 'No autorizado'], 401);
            return;
        }
        
        // Verificar datos de entrada
        if (empty($_POST['item_id']) || !isset($_POST['cantidad'])) {
            $this->jsonResponse(['success' => false, 'error' => 'Datos incompletos'], 400);
            return;
        }
        
        $itemId = (int)$_POST['item_id'];
        $cantidad = (int)$_POST['cantidad'];
        
        // Obtener el item
        $item = ItemCarrito::get($this->db, $itemId);
        
        if (!$item) {
            $this->jsonResponse(['success' => false, 'error' => 'Item no encontrado'], 404);
            return;
        }
        
        // Verificar que el item pertenece al carrito del usuario
        $userId = $_SESSION['user_id'];
        $carrito = Carrito::getActive($this->db, $userId);
        
        if ($item->id_carrito !== $carrito->id_carrito) {
            $this->jsonResponse(['success' => false, 'error' => 'No autorizado'], 401);
            return;
        }
        
        // Si la cantidad es 0, eliminar el item
        if ($cantidad <= 0) {
            $success = ItemCarrito::remove($this->db, $itemId);
        } else {
            // Verificar stock disponible
            $stmt = $this->db->prepare("SELECT stock FROM Productos WHERE id_producto = :id");
            $stmt->execute(['id' => $item->id_producto]);
            $producto = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($producto && $producto['stock'] < $cantidad) {
                $this->jsonResponse([
                    'success' => false, 
                    'error' => 'No hay suficiente stock disponible',
                    'stockDisponible' => $producto['stock']
                ], 400);
                return;
            }
            
            // Actualizar cantidad
            $success = ItemCarrito::updateQuantity($this->db, $itemId, $cantidad);
        }
        
        if ($success) {
            // Obtener datos actualizados del carrito
            $itemCount = $carrito->getItemCount($this->db);
            $total = $carrito->getTotal($this->db);
            $items = $carrito->getItems($this->db);
            
            $this->jsonResponse([
                'success' => true,
                'message' => 'Carrito actualizado',
                'itemCount' => $itemCount,
                'total' => $total,
                'items' => $items
            ]);
        } else {
            $this->jsonResponse(['success' => false, 'error' => 'Error al actualizar carrito'], 500);
        }
    }
    
    // Eliminar un producto del carrito
    public function removeFromCart() {
        // Verificar que el usuario está autenticado
        if (empty($_SESSION['user_id'])) {
            $this->jsonResponse(['success' => false, 'error' => 'No autorizado'], 401);
            return;
        }
        
        // Verificar datos de entrada
        if (empty($_POST['item_id'])) {
            $this->jsonResponse(['success' => false, 'error' => 'Datos incompletos'], 400);
            return;
        }
        
        $itemId = (int)$_POST['item_id'];
        
        // Obtener el item
        $item = ItemCarrito::get($this->db, $itemId);
        
        if (!$item) {
            $this->jsonResponse(['success' => false, 'error' => 'Item no encontrado'], 404);
            return;
        }
        
        // Verificar que el item pertenece al carrito del usuario
        $userId = $_SESSION['user_id'];
        $carrito = Carrito::getActive($this->db, $userId);
        
        if ($item->id_carrito !== $carrito->id_carrito) {
            $this->jsonResponse(['success' => false, 'error' => 'No autorizado'], 401);
            return;
        }
        
        // Eliminar item
        $success = ItemCarrito::remove($this->db, $itemId);
        
        if ($success) {
            // Obtener datos actualizados del carrito
            $itemCount = $carrito->getItemCount($this->db);
            $total = $carrito->getTotal($this->db);
            
            $this->jsonResponse([
                'success' => true,
                'message' => 'Producto eliminado del carrito',
                'itemCount' => $itemCount,
                'total' => $total
            ]);
        } else {
            $this->jsonResponse(['success' => false, 'error' => 'Error al eliminar producto'], 500);
        }
    }
    
    // Vaciar carrito
    public function emptyCart() {
        // Verificar que el usuario está autenticado
        if (empty($_SESSION['user_id'])) {
            $this->jsonResponse(['success' => false, 'error' => 'No autorizado'], 401);
            return;
        }
        
        $userId = $_SESSION['user_id'];
        $carrito = Carrito::getActive($this->db, $userId);
        
        // Vaciar carrito
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
    
    // Finalizar compra
    public function checkout() {
        // Verificar que el usuario está autenticado
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        $carrito = Carrito::getActive($this->db, $userId);
        $items = $carrito->getItems($this->db);
        
        if (empty($items)) {
            $_SESSION['mensaje'] = [
                'tipo' => 'warning',
                'texto' => 'Tu carrito está vacío'
            ];
            header('Location: /carrito');
            exit;
        }
        
        // En un sistema real, aquí se procesaría el pago, se actualizaría el stock, etc.
        // Para este ejemplo, sólo mostraremos la página de confirmación
        
        $total = $carrito->getTotal($this->db);
        
        // Incluir vista
        include __DIR__ . '/../views/header.php';
        include __DIR__ . '/../views/checkout.php';
        include __DIR__ . '/../views/footer.php';
    }
    
    // Confirmar compra (procesar pago)
    public function confirmCheckout() {
        // Verificar que el usuario está autenticado
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        $carrito = Carrito::getActive($this->db, $userId);
        $items = $carrito->getItems($this->db);
        
        if (empty($items)) {
            $_SESSION['mensaje'] = [
                'tipo' => 'warning',
                'texto' => 'Tu carrito está vacío'
            ];
            header('Location: /carrito');
            exit;
        }
        
        // En un sistema real, aquí procesaríamos el pago con un gateway
        // y crearíamos un registro de pedido en la base de datos
        
        // Por ahora, simplemente actualizamos el stock y cerramos el carrito
        $this->db->beginTransaction();
        
        try {
            // Actualizar stock
            foreach ($items as $item) {
                $sql = "
                    UPDATE Productos
                    SET stock = stock - :cantidad
                    WHERE id_producto = :id_producto
                ";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([
                    'cantidad' => $item->cantidad,
                    'id_producto' => $item->id_producto
                ]);
            }
            
            // Finalizar carrito
            $carrito->finalize($this->db);
            
            // Confirmar transacción
            $this->db->commit();
            
            // Mostrar mensaje de éxito
            $_SESSION['mensaje'] = [
                'tipo' => 'success',
                'texto' => '¡Gracias por tu compra! Tu pedido ha sido procesado correctamente.'
            ];
            
            header('Location: /catalogo');
            exit;
            
        } catch (Exception $e) {
            // Revertir cambios si hay error
            $this->db->rollBack();
            
            $_SESSION['mensaje'] = [
                'tipo' => 'danger',
                'texto' => 'Ha ocurrido un error al procesar tu pedido: ' . $e->getMessage()
            ];
            
            header('Location: /carrito');
            exit;
        }
    }
    
    // Helper para enviar respuestas JSON
    private function jsonResponse(array $data, int $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}