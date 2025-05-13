<?php
// controllers/CatalogoController.php

require_once __DIR__ . '/../models/Producto.php';

class CatalogoController {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    // Mostrar catálogo (con o sin filtros)
    public function index() {
        // Recoger filtros de la query string
        $term     = trim($_GET['term']     ?? '');
        $category = trim($_GET['category'] ?? '');

        // Si hay filtros, buscamos, si no, todo agrupado
        if ($term !== '' || $category !== '') {
            $por_categoria = Producto::searchGroupedByCategoria($this->db, $term, $category);
        } else {
            $por_categoria = Producto::allGroupedByCategoria($this->db);
        }

        // Para el <select> de categorías
        $categorias = Producto::getAllCategories($this->db);

        // Renderizar la vista
        include __DIR__ . '/../views/catalogo.php';
    }
}
