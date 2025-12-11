<?php

class LoginControlador {
    private LoginModelo $usuario;

    public function __construct(LoginModelo $usuario)
    {
        $this->usuario = $usuario;
    }

    // GET /usuarios
    // Solo para ver TODOS los usuarios
    public function listar(int $limit, int $offset): array
    {
        if ($limit < 0 || $offset < 0) {
            http_response_code(400); // 400 Bad Request
            return [
                "error" => "Los parámetros limit y offset deben ser números enteros no negativos",
            ];
        }

        // Aquí solo delegamos en el modelo.
        // Por defecto, si no cambiamos nada, el código HTTP será 200 OK.
        return $this->usuario->obtenerTodos($limit, $offset);
    }

    public function infoToken(array $data): array {
        if (empty($data["correo"]) || empty($data["password"])) {
            http_response_code(400);
            return ["error" => "correo y contraseña son obligatorios"];
        }
        
        $usuario = $this->usuario->obtenerPorEmail($data);
    
        if ($usuario === null) {
            // No existe ningún usuario con ese id
            http_response_code(404); // 404 Not Found
            return [
                "error" => "usuario no encontrado",
            ];
        }

        // usuario encontrado
        http_response_code(200); // 200 OK
        return $usuario;
    }

    public function infoUser(int $id): array {
        if ($id <= 0) {
            http_response_code(400); // 400 Bad Request
            return [
                "error" => "Debe indicar un id de usuario válido",
            ];
        }

        $usuario = $this->usuario->obtenerPorid($id);
    
        if ($usuario === null) {
            // No existe ningún usuario con ese id
            http_response_code(404); // 404 Not Found
            return [
                "error" => "usuario no encontrado",
            ];
        }

        // usuario encontrado
        http_response_code(200); // 200 OK
        return $usuario;
    }
}