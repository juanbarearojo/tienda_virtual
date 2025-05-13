<?php
// models/Carrito.php

class Carrito {
    public int $id_carrito;
    public int $id_usuario;
    public string $fecha_creacion;
    public bool $activo;
    
    // Obtener el carrito activo de un usuario
    public static function getActive(PDO $db, int $userId): ?Carrito {
        $sql = "
            SELECT 
                id_carrito,
                id_usuario,
                fecha_creacion,
                activo
            FROM Carritos
            WHERE id_usuario = :id_usuario AND activo = 1
            ORDER BY fecha_creacion DESC
            LIMIT 1
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute(['id_usuario' => $userId]);
        $stmt->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, self::class);
        
        $carrito = $stmt->fetch();
        
        // Si no existe carrito activo, crear uno nuevo
        if (!$carrito) {
            return self::create($db, $userId);
        }
        
        return $carrito;
    }
    
    // Crear un nuevo carrito
    public static function create(PDO $db, int $userId): Carrito {
        $sql = "
            INSERT INTO Carritos (id_usuario, activo)
            VALUES (:id_usuario, 1)
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute(['id_usuario' => $userId]);
        
        // Obtener el carrito recién creado
        return self::getActive($db, $userId);
    }
    
    // Finalizar un carrito (marcar como inactivo)
    public function finalize(PDO $db): bool {
        $sql = "
            UPDATE Carritos
            SET activo = 0
            WHERE id_carrito = :id_carrito
        ";
        $stmt = $db->prepare($sql);
        return $stmt->execute(['id_carrito' => $this->id_carrito]);
    }
    
    // Obtener todos los items del carrito
    public function getItems(PDO $db): array {
        return ItemCarrito::getAllByCarrito($db, $this->id_carrito);
    }
    
    // Obtener el total del carrito
    public function getTotal(PDO $db): float {
        $sql = "
            SELECT SUM(p.precio * ic.cantidad) as total
            FROM Items_Carrito ic
            JOIN Productos p ON ic.id_producto = p.id_producto
            WHERE ic.id_carrito = :id_carrito
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute(['id_carrito' => $this->id_carrito]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }
    
    // Obtener cantidad total de items en el carrito
    public function getItemCount(PDO $db): int {
        $sql = "
            SELECT SUM(cantidad) as count
            FROM Items_Carrito
            WHERE id_carrito = :id_carrito
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute(['id_carrito' => $this->id_carrito]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($result['count'] ?? 0);
    }
}