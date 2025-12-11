<?php

class LoginModelo
{
    private PDO $pdo;

    public function __construct(PDO $db)
    {
        $this->pdo = $db;
    }

    public function obtenerTodos(?int $limit, ?int $offset): array {
        $sql = "SELECT * FROM Usuario LIMIT :limit OFFSET :offset";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':limit', $limit ,PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset,  PDO::PARAM_INT);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $stmt->execute(); // No hay parámetros, pero igual se ejecuta
        return $stmt->fetchAll();
    }

    public function obtenerPorEmail(array $data) {
        $sql = "SELECT id, nombre, rol, correo
                FROM Usuario
                WHERE correo = :correo AND contrasena = :password";

        $stmt = $this->pdo->prepare($sql);
        // Enlazar parámetros
        $stmt->bindParam(':correo', $data["correo"]);
        $stmt->bindParam(':password', $data["password"]);

        // Ejecutar consulta
        $stmt->execute();

        // fetch() devuelve false si no encuentra datos.
        $usuarios = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuarios === false) {
            return null;
        }

        return $usuarios;
    }

    public function obtenerPorid(int $id) {
        $sql = "SELECT id, nombre, rol, correo
                FROM Usuario
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        // Enlazar parámetros
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Ejecutar consulta
        $stmt->execute();

        // fetch() devuelve false si no encuentra datos.
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario === false) {
            return null;
        }

        return $usuario;
    }
}