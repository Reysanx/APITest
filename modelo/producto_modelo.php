<?php

class ProductoModelo
{
    private PDO $pdo;

    public function __construct(PDO $db)
    {
        $this->pdo = $db;
    }

    /**
     * Obtener todos los productos.
     *
     * @return array Lista de productos (cada uno como array asociativo).
     */
    public function obtenerTodos(?int $limit, ?int $offset): array
    {
        $sql = "SELECT id, nombre, descripcion, stock, precio FROM Producto LIMIT :limit OFFSET :offset";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':limit', $limit ,PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset,  PDO::PARAM_INT);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $stmt->execute(); // No hay parámetros, pero igual se ejecuta
        return $stmt->fetchAll();
    }

    /**
     * Obtener un único producto por id.
     */
    public function obtenerPorId(int $id): ?array
    {
        $sql = "SELECT id, nombre, descripcion, stock, precio
                FROM Producto
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        // Enlazar parámetros
        $stmt->bindParam(':id', $id);

        // Ejecutar consulta
        $stmt->execute();

        // fetch() devuelve false si no encuentra datos.
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($producto === false) {
            return null;
        }

        return $producto;
    }

    /*
        Obtener todos los productos con su categoria
    */
    public function obtenerTodosCategoria(?int $limit, ?int $offset)
    {
        $sql = "SELECT p.id, p.nombre, c.nombre as nombreCategoria, p.descripcion, p.stock, p.precio
                FROM Producto p 
                JOIN Categoria c
                ON p.id_categoria = c.id
                LIMIT :limit OFFSET :offset";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /*
        Obtener un producto con su categoria
    */
    public function obtenerProductoCategoriaId(int $id)
    {
        $sql = "SELECT p.id, p.nombre, c.nombre as nombreCategoria, p.descripcion, p.stock, p.precio
                FROM Producto p 
                JOIN Categoria c
                ON p.id_categoria = c.id
                WHERE p.id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        // fetch() devuelve false si no encuentra datos.
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($producto === false) {
            return null;
        }

        return $producto;
    }
    // Obtener todo los productos de una categoria
    public function obtenerCategoriaTodosProductos($categoriaId) {
        $sql = "SELECT * FROM Producto WHERE id_categoria = :categoria_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':categoria_id', $categoriaId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertar(int $id_categoria, string $nombre, ?string $descripcion, ?int $stock, float $precio): bool
    {
        $sql = "INSERT INTO Producto (id_categoria, nombre, descripcion, stock, precio)
                VALUES (:id_categoria, :nombre, :descripcion, :stock, :precio)";
                
        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(':id_categoria', $id_categoria);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':stock', $stock);
        $stmt->bindParam(':precio', $precio);

        return $stmt->execute();
    }

    public function actualizar(int $id, int $id_categoria, string $nombre, string $descripcion, int $stock, float $precio)
    {
        $sql = "UPDATE Producto
                SET id_categoria = :id_categoria, nombre = :nombre, descripcion = :descripcion, stock = :stock, precio = :precio
                WHERE id = :id";

        try {
            $stmt = $this->pdo->prepare($sql);

            // bindParam liga la variable por referencia
            $stmt->bindParam(':id_categoria', $id_categoria);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':descripcion', $descripcion);
            $stmt->bindParam(':stock', $stock);
            $stmt->bindParam(':precio', $precio);
            $stmt->bindParam(":id", $id);

            $stmt->execute();

            // rowCount() devuelve cuántas filas se han afectado
            $filas = $stmt->rowCount();

            if ($filas === 0) {
                // Lo tratamos como "no existe" para simplificar.
                return null;
            }
            return true;
        } catch (PDOException $e) {
            // Error interno
            return false;
        }
    }

    public function borrarProducto(int $id): ?bool
    {
        $sql = "DELETE FROM Producto WHERE id = :id";

        try {
            $stmt = $this->pdo->prepare($sql);

            // bindParam enlaza la variable por referencia
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);

            $stmt->execute();

            $filas = $stmt->rowCount();

            if ($filas === 0) {
                return null; // No había ningún producto con ese id
            }

            return true;
        } catch (PDOException $e) {
            return false;
        }
    }


    /*
    Funcion para contar los registros de la tabla
    */
    public function contarProductos(): int {
        $sql = "SELECT COUNT(id) as total FROM Producto";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $resultado['total'];
    }

    /*
    Funcion para buscar en la tabla por el campo nombre
    */
    public function buscar(string $nombre): array {
        $sql = "SELECT  id, nombre, descripcion, stock, precio
                FROM Producto 
                WHERE nombre LIKE :nombre";
        
        $stmt = $this->pdo->prepare($sql);
        $nombreBusqueda = "%{$nombre}%";
        $stmt->bindParam(':nombre', $nombreBusqueda);
        $stmt->execute();
        $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $resultado;
    }

    /*
    Funcion para listar los productos por precio ascendente
    */
    public function ordenar(?int $limit, ?int $offset, string $ordenar): array {
        $sql = "SELECT id, nombre, descripcion, stock, precio 
                FROM Producto 
                ORDER BY precio {$ordenar}
                LIMIT :limit OFFSET :offset";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
