<?php
// config/database.php
function getConnection(): PDO {
    $db = new PDO('sqlite:' . __DIR__ . '/../db/tienda.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $db;
}
