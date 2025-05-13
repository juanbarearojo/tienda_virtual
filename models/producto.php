<?php
// models/Producto.php

class Producto {
    public int     $id_producto;
    public string  $nombre;
    public ?string $descripcion;
    public float   $precio;
    public int     $stock;
    public ?string $categoria;
    public ?string $imagen_url;

    // Método genérico que devuelve TODOS los productos como objetos
    public static function all(PDO $db): array {
        $sql = "
            SELECT
              id_producto,
              nombre,
              descripcion,
              precio,
              stock,
              categoria,
              imagen_url
            FROM Productos
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        // Indicamos que devuelva objetos Producto
        $stmt->setFetchMode(
          PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE,
          self::class
        );
        return $stmt->fetchAll(); // ahora devuelve Producto[]
    }

    // Método que agrupa por categoría
    public static function allGroupedByCategoria(PDO $db): array {
        $productos = self::all($db);
        $porCategoria = [];
        foreach ($productos as $p) {
            $cat = $p->categoria ?: 'Sin categoría';
            $porCategoria[$cat][] = $p;
        }
        return $porCategoria;
    }
    
    // Obtener un producto por su ID
    public static function getById(PDO $db, int $id): ?Producto {
        $sql = "
            SELECT
              id_producto,
              nombre,
              descripcion,
              precio,
              stock,
              categoria,
              imagen_url
            FROM Productos
            WHERE id_producto = :id
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $stmt->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, self::class);
        
        $producto = $stmt->fetch();
        return $producto ?: null;
    }
    
    // Verificar si hay stock suficiente
    public function hasStock(int $cantidad = 1): bool {
        return $this->stock >= $cantidad;
    }
    
    // Actualizar stock después de una compra
    public function updateStock(PDO $db, int $cantidad): bool {
        if (!$this->hasStock($cantidad)) {
            return false;
        }
        
        $sql = "
            UPDATE Productos
            SET stock = stock - :cantidad
            WHERE id_producto = :id_producto
        ";
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            'cantidad' => $cantidad,
            'id_producto' => $this->id_producto
        ]);
    }
    
    // Formatear precio para mostrar
    public function getPrecioFormateado(): string {
        return number_format($this->precio, 2, ',', '.');
    }
    
    // Obtener ruta de imagen normalizada
    public function getRutaImagen(): string {
        if (!$this->imagen_url) {
            return '';
        }
        
        // Normalizar barras
        $rutaImagen = str_replace('\\', '/', $this->imagen_url);
        
        // Si no comienza con barra, añadirla
        if (substr($rutaImagen, 0, 1) !== '/') {
            $rutaImagen = '/' . $rutaImagen;
        }
        
        return $rutaImagen;
    }
}