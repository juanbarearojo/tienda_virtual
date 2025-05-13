<?php
// controllers/CatalogoController.php

require_once __DIR__ . '/../models/Producto.php';

class CatalogoController {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    // Mostrar catálogo de productos
    public function index() {
        // Crear tabla si no existe
        $this->db->exec(
            "CREATE TABLE IF NOT EXISTS Productos (
                id_producto INTEGER PRIMARY KEY AUTOINCREMENT,
                nombre TEXT NOT NULL,
                descripcion TEXT,
                precio REAL NOT NULL,
                stock INTEGER NOT NULL DEFAULT 0,
                categoria TEXT,
                imagen TEXT
            );"
        );

        // Poblar con datos iniciales si está vacía
        Producto::seedIfEmpty($this->db);

        // Obtener todos los productos
        $productos = Producto::all($this->db);

        // Agrupar por categoría
        $por_categoria = [];
        foreach ($productos as $p) {
            $por_categoria[$p->categoria][] = $p;
        }

        // Renderizar vistas
        include __DIR__ . '/../views/header.php';
        include __DIR__ . '/../views/catalogo.php';
        include __DIR__ . '/../views/footer.php';
    }
}
