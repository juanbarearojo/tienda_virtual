
<?php

// models/ItemCarrito.php

require_once __DIR__ . '/Producto.php';

class ItemCarrito {
    public int $id_item;
    public int $id_carrito;
    public int $id_producto;
    public int $cantidad;
    public ?Producto $producto = null;
    
    // Añadir un producto al carrito
    public static function add(PDO $db, int $carritoId, int $productoId, int $cantidad = 1): bool {
        // Primero verificar si el producto ya está en el carrito
        $sql = "
            SELECT id_item, cantidad
            FROM Items_Carrito
            WHERE id_carrito = :id_carrito AND id_producto = :id_producto
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            'id_carrito' => $carritoId,
            'id_producto' => $productoId
        ]);
        
        $existingItem = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Si ya existe, actualizar la cantidad
        if ($existingItem) {
            $newCantidad = $existingItem['cantidad'] + $cantidad;
            return self::updateQuantity($db, $existingItem['id_item'], $newCantidad);
        }
        
        // Si no existe, añadir nuevo item
        $sql = "
            INSERT INTO Items_Carrito (id_carrito, id_producto, cantidad)
            VALUES (:id_carrito, :id_producto, :cantidad)
        ";
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            'id_carrito' => $carritoId,
            'id_producto' => $productoId,
            'cantidad' => $cantidad
        ]);
    }
    
    // Actualizar cantidad de un item
    public static function updateQuantity(PDO $db, int $itemId, int $cantidad): bool {
        // Si cantidad es 0 o negativa, eliminar el item
        if ($cantidad <= 0) {
            return self::remove($db, $itemId);
        }
        
        $sql = "
            UPDATE Items_Carrito
            SET cantidad = :cantidad
            WHERE id_item = :id_item
        ";
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            'id_item' => $itemId,
            'cantidad' => $cantidad
        ]);
    }
    
    // Eliminar un item del carrito
    public static function remove(PDO $db, int $itemId): bool {
        $sql = "
            DELETE FROM Items_Carrito
            WHERE id_item = :id_item
        ";
        $stmt = $db->prepare($sql);
        return $stmt->execute(['id_item' => $itemId]);
    }
    
    // Obtener todos los items de un carrito con información de producto
    public static function getAllByCarrito(PDO $db, int $carritoId): array {
        $sql = "
            SELECT 
                ic.id_item,
                ic.id_carrito,
                ic.id_producto,
                ic.cantidad,
                p.id_producto as 'producto_id',
                p.nombre,
                p.descripcion,
                p.precio,
                p.stock,
                p.categoria,
                p.imagen_url
            FROM Items_Carrito ic
            JOIN Productos p ON ic.id_producto = p.id_producto
            WHERE ic.id_carrito = :id_carrito
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute(['id_carrito' => $carritoId]);
        
        $items = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $item = new ItemCarrito();
            $item->id_item = $row['id_item'];
            $item->id_carrito = $row['id_carrito'];
            $item->id_producto = $row['id_producto'];
            $item->cantidad = $row['cantidad'];
            
            // Agregar información del producto
            $producto = new Producto();
            $producto->id_producto = $row['producto_id'];
            $producto->nombre = $row['nombre'];
            $producto->descripcion = $row['descripcion'];
            $producto->precio = $row['precio'];
            $producto->stock = $row['stock'];
            $producto->categoria = $row['categoria'];
            $producto->imagen_url = $row['imagen_url'];
            
            $item->producto = $producto;
            $items[] = $item;
        }
        
        return $items;
    }
    
    // Obtener un item específico
    public static function get(PDO $db, int $itemId): ?ItemCarrito {
        $sql = "
            SELECT 
                ic.id_item,
                ic.id_carrito,
                ic.id_producto,
                ic.cantidad
            FROM Items_Carrito ic
            WHERE ic.id_item = :id_item
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute(['id_item' => $itemId]);
        
        $stmt->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, self::class);
        return $stmt->fetch() ?: null;
    }
    
    // Vaciar carrito
    public static function emptyCart(PDO $db, int $carritoId): bool {
        $sql = "
            DELETE FROM Items_Carrito
            WHERE id_carrito = :id_carrito
        ";
        $stmt = $db->prepare($sql);
        return $stmt->execute(['id_carrito' => $carritoId]);
    }
}