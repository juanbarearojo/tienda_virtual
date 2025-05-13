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

    // Devuelve todos los productos
    public static function all(PDO $db): array {
        $sql = "
            SELECT id_producto, nombre, descripcion, precio, stock, categoria, imagen_url
            FROM Productos
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, self::class);
        return $stmt->fetchAll();
    }

    // Agrupa todos los productos por categoría
    public static function allGroupedByCategoria(PDO $db): array {
        $productos = self::all($db);
        $porCategoria = [];
        foreach ($productos as $p) {
            $cat = $p->categoria ?: 'Sin categoría';
            $porCategoria[$cat][] = $p;
        }
        return $porCategoria;
    }

    // Busca productos por nombre (LIKE) y/o categoría (igual)
    public static function search(PDO $db, ?string $term, ?string $category): array {
        $sql = "
            SELECT id_producto, nombre, descripcion, precio, stock, categoria, imagen_url
            FROM Productos
            WHERE 1=1
        ";
        $params = [];

        if ($term !== null && $term !== '') {
            $sql .= " AND nombre LIKE :term";
            $params['term'] = "%{$term}%";
        }
        if ($category !== null && $category !== '') {
            $sql .= " AND categoria = :category";
            $params['category'] = $category;
        }

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, self::class);
        return $stmt->fetchAll();
    }

    // Igual que allGroupedByCategoria, pero tras aplicar search()
    public static function searchGroupedByCategoria(PDO $db, ?string $term, ?string $category): array {
        $productos = self::search($db, $term, $category);
        $porCategoria = [];
        foreach ($productos as $p) {
            $cat = $p->categoria ?: 'Sin categoría';
            $porCategoria[$cat][] = $p;
        }
        return $porCategoria;
    }

    // Obtener lista de categorías distintas
    public static function getAllCategories(PDO $db): array {
        $sql = "SELECT DISTINCT categoria FROM Productos WHERE categoria IS NOT NULL";
        $stmt = $db->query($sql);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($r) => $r['categoria'], $rows);
    }

    // Obtener un producto por su ID
    public static function getById(PDO $db, int $id): ?Producto {
        $sql = "
            SELECT id_producto, nombre, descripcion, precio, stock, categoria, imagen_url
            FROM Productos
            WHERE id_producto = :id
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, self::class);
        $p = $stmt->fetch();
        return $p ?: null;
    }

    // Verificar stock
    public function hasStock(int $cantidad = 1): bool {
        return $this->stock >= $cantidad;
    }

    // Reducir stock tras compra
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
            'cantidad'     => $cantidad,
            'id_producto'  => $this->id_producto,
        ]);
    }

    // Precio formateado
    public function getPrecioFormateado(): string {
        return number_format($this->precio, 2, ',', '.');
    }

    // Ruta de imagen normalizada
    public function getRutaImagen(): string {
        if (!$this->imagen_url) {
            return '';
        }
        $ruta = str_replace('\\', '/', $this->imagen_url);
        if (substr($ruta, 0, 1) !== '/') {
            $ruta = '/' . $ruta;
        }
        return $ruta;
    }
}
