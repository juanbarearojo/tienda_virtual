<?php
// models/User.php

class User {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    // Registrar un usuario
    public function create(string $name, string $email, string $passwordHash): bool {
        $stmt = $this->db->prepare(
            "INSERT INTO Usuarios (nombre, email, password) VALUES (:n, :e, :p)"
        );
        return $stmt->execute([
            ':n' => $name,
            ':e' => $email,
            ':p' => $passwordHash,
        ]);
    }

    // Buscar usuario por email
    public function findByEmail(string $email) {
        $stmt = $this->db->prepare(
            "SELECT * FROM Usuarios WHERE email = :e"
        );
        $stmt->execute([':e' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
