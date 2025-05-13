<?php
// controllers/CatalogoController.php

require_once __DIR__ . '/../models/Producto.php';

class CatalogoController {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    // Mostrar catálogo de productos
    public function index() {
        // Obtener productos agrupados por categoría directamente del modelo
        $por_categoria = Producto::allGroupedByCategoria($this->db);
        
        // Renderizar vistas
        include __DIR__ . '/../views/catalogo.php';
    }
}