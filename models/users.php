<?php
// models/User.php

class User {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Crear un usuario nuevo con NIF, teléfono y domicilio opcionales.
     *
     * @param string      $name
     * @param string      $email
     * @param string      $passwordHash
     * @param string|null $nif
     * @param string|null $telefono
     * @param string|null $domicilio
     * @return bool
     */
    public function create(
        string $name,
        string $email,
        string $passwordHash,
        ?string $nif = null,
        ?string $telefono = null,
        ?string $domicilio = null
    ): bool {
        $sql = "
            INSERT INTO Usuarios
                (nombre, email, password, NIF, telefono, domicilio)
            VALUES
                (:n, :e, :p, :nif, :tel, :dom)
        ";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':n'    => $name,
            ':e'    => $email,
            ':p'    => $passwordHash,
            ':nif'  => $nif,
            ':tel'  => $telefono,
            ':dom'  => $domicilio,
        ]);
    }

    /**
     * Busca un usuario por email.
     *
     * @param string $email
     * @return array|false
     */
    public function findByEmail(string $email) {
        $stmt = $this->db->prepare(
            "SELECT * FROM Usuarios WHERE email = :e"
        );
        $stmt->execute([':e' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Busca un usuario por NIF.
     *
     * @param string $nif
     * @return array|false
     */
    public function findByNIF(string $nif) {
        $stmt = $this->db->prepare(
            "SELECT * FROM Usuarios WHERE NIF = :nif"
        );
        $stmt->execute([':nif' => $nif]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Busca un usuario por teléfono.
     *
     * @param string $telefono
     * @return array|false
     */
    public function findByTelefono(string $telefono) {
        $stmt = $this->db->prepare(
            "SELECT * FROM Usuarios WHERE telefono = :tel"
        );
        $stmt->execute([':tel' => $telefono]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
