<?php
// models/Producto.php
class Producto {
    public int $id;
    public string $nombre;
    public string $imagen;
    public float $precio;
    public string $categoria;

    public static function all(PDO $db): array {
        $stmt = $db->query("SELECT * FROM productos");
        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    public static function seedIfEmpty(PDO $db): void {
        $count = $db->query("SELECT COUNT(*) FROM productos")->fetchColumn();
        if ($count == 0) {
            // ... inserción de datos de ejemplo ...
        }
    }
}
