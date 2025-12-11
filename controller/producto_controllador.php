<?php

class ProductoControlador {
    private ProductoModelo $producto;

    public function __construct(ProductoModelo $producto)
    {
        $this->producto = $producto;
    }

    // GET /productos
    // Solo para ver TODOS los productos
    public function listar(?string $include, int $limit, int $offset, ?int $id_categoria): array
    {
        if (($include !== null) && ($include !== "categoria")) {
            http_response_code(400); // 400 Bad Request
            return [
                "error" => "Debe indicar una categoria valida o no indicarla",
            ];
        }

        if ($id_categoria !== null) {
            // Obtener productos de una categoria específica
            return $this->producto->obtenerCategoriaTodosProductos($id_categoria);
        }

        if ($limit < 0 || $offset < 0) {
            http_response_code(400); // 400 Bad Request
            return [
                "error" => "Los parámetros limit y offset deben ser números enteros no negativos",
            ];
        }

        // Aquí solo delegamos en el modelo.
        // Por defecto, si no cambiamos nada, el código HTTP será 200 OK.
        if ($include === 'categoria') {
            return $this->producto->obtenerTodosCategoria($limit, $offset);
        } else {
            return $this->producto->obtenerTodos($limit, $offset);
        } 
    }

    // Solo para ver producto por ID
    public function ver(?int $id, ?string $include): array
    {
        // Validar el id
        if ( $id <= 0) {
            http_response_code(400); // 400 Bad Request
            return [
                "error" => "Debe indicar un id de producto válido",
            ];
        }

        if (!(($include === null) || ($include === 'categoria'))) {
            http_response_code(400); // 400 Bad Request
            return [
                "error" => "Debe indicar una categoria valida o no indicarla",
            ];
        }

        // Pedir el producto al modelo
        if ($include === 'categoria') {
            $producto = $this->producto->obtenerProductoCategoriaId($id);
        } else {
            $producto = $this->producto->obtenerPorId($id);
        }
    
        if ($producto === null) {
            // No existe ningún producto con ese id
            http_response_code(404); // 404 Not Found
            return [
                "error" => "Producto no encontrado",
            ];
        }

        // Producto encontrado
        http_response_code(200); // 200 OK
        return $producto;
    }

    public function crear(array $data): array {
        if (empty($data["id_categoria"]) || empty($data["nombre"]) || empty($data["precio"])) {
            http_response_code(400);
            return ["error" => "id_categoria, nombre y precio son obligatorios"];
        }

        // Intentar insertar en la base de datos
        // El valor por defecto de descripcion es "" y el de stock es 0.
        if ($this->producto->insertar($data["id_categoria"], $data["nombre"], $data["descripcion"] ?? "", $data["stock"] ?? 0, $data["precio"])) {
            // Recurso creado → 201 Created
            http_response_code(201);
            return ["mensaje" => "Producto creado correctamente"];
        }

        http_response_code(500);
        return ["error" => "No se pudo insertar el producto"];
    }

    public function insertar(?int $id, array $data): array {
        // Validar que se ha pasado un id
        if ($id === null || $id <= 0) {
            http_response_code(400); // Petición incorrecta
            return ["error" => "Debe indicar un id de producto válido"];
        }

        // Validar datos mínimos (puedes adaptar esto según tus reglas)
        if (empty($data["id_categoria"]) || empty($data["nombre"]) || empty($data["precio"])) {
            http_response_code(400);
            return ["error" => "id_categoria, nombre y precio son obligatorios"];
        }

        // El valor por defecto de descripcion es "" y el de stock es 0. Si no se le pasan esos valores se les vuelve a 0 o "".
        $actualizado = $this->producto->actualizar($id, $data["id_categoria"], $data["nombre"], $data["descripcion"] ?? "", $data["stock"] ?? 0, $data["precio"]);

        if ($actualizado === true) {
            // Actualización correcta → 200 OK
            http_response_code(200);
            return ["mensaje" => "Producto actualizado correctamente"];
        }

        if ($actualizado === null) {
            // Caso opcional: el modelo puede indicar que el producto no existe
            http_response_code(404); // No encontrado
            return ["error" => "Producto no encontrado"];
        }

        // Si llega aquí, ha habido un error interno al actualizar
        http_response_code(500);
        return ["error" => "No se pudo actualizar el producto"];
    }

    public function borrar(int $id) {
        // Validar el id
        if ($id === null || $id <= 0) {
            http_response_code(400); // 400 Bad Request
            return [
                "error" => "Debe indicar un id de producto válido",
            ];
        }

        $producto = $this->producto->borrarProducto($id);

        if ($producto === true) {
            // Eliminado correctamente.
            // Devuelvo 200, para mostrar contenido
            http_response_code(200); 
            return ["mensaje" => "Producto eliminado correctamente"]; 
        }

        if ($producto === null) {
            // No existe ningún producto con ese id
            http_response_code(404); // 404 Not Found
            return [
                "error" => "Producto no encontrado",
            ];
        }

        // Producto encontrado
        http_response_code(200); // 200 OK
        return "Producto Borrado Correctamente";
    }

    // Funcion para contar los productos
    public function contar() {
        $numeros = $this->producto->contarProductos();
        return $numeros;
    }

    // Funcion para buscar por el campo nombre
    public function buscar(string $nombre) {
        if ($nombre === null) {
            http_response_code(400); // 400 Bad Request
            return [
                "error" => "Debe indicar un nombre válido",
            ];
        }

        $busqueda = $this->producto->buscar($nombre);

        if ($busqueda === null) {
            // No existe ningún producto con ese id
            http_response_code(404); // 404 Not Found
            return [
                "error" => "Producto no encontrado",
            ];
        }

        http_response_code(200);
        return $busqueda;
    }

    // Funcion para ordenar por precio
    public function ordenarPrecio(string $ordenar, int $limit, int $offset) {
        $ordenarPrecio = "";
        if ($limit < 0 || $offset < 0) {
            http_response_code(400); // 400 Bad Request
            return [
                "error" => "Los parámetros limit y offset deben ser números enteros no negativos",
            ];
        }

        if ($ordenar !== "precio_asc" && $ordenar !== "precio_desc") {
            http_response_code(400); // 400 Bad Request
            return [
                "error" => "El parametro de ordenar solo puede ser de precio ascendente o descendente",
            ];
        }

        if ($ordenar === "precio_asc") {
            $ordenarPrecio = "ASC";
        } else {
            $ordenarPrecio = "DESC";
        }

        return $this->producto->ordenar($limit, $offset, $ordenarPrecio);
    }

}
