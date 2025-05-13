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
}