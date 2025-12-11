<?php
//Puerta de entrada
header("Content-Type: application/json; charset=utf-8");

require_once __DIR__ . '/../controller/producto_controllador.php';
require_once __DIR__ . '/../modelo/producto_modelo.php';
require_once __DIR__ . "/../database.php";
require_once __DIR__ . "/../config.php";
require_once __DIR__ . "/auth.php";

// Crear conexión, modelo y controlador.
$db = Database::getConnection();
$modelo = new ProductoModelo($db);
$controlador = new ProductoControlador($modelo);


// Método HTTP de la petición (GET, POST, PUT, DELETE, ...)
$metodo = $_SERVER["REQUEST_METHOD"];
$include = $_GET['include'] ?? null;
$count = $_GET['count'] ?? null;
$buscar = $_GET['search'] ?? null;
$ordenar = $_GET['order'] ?? null;

// id_categoria por query string: ?id_categoria=2
$id_categoria = isset($_GET["id_categoria"]) ? (int) $_GET["id_categoria"] : null;

// id por query string: ?id=5
$id = isset($_GET["id"]) ? (int) $_GET["id"] : null;

// Parámetros de paginación: ?page=1&limit=10
$page = isset($_GET["page"]) ? (int) $_GET["page"] : 1;
$limit = isset($_GET["limit"]) ? (int) $_GET["limit"] : 2; // Cambiar el valor por defecto

// Calcular el offset para la paginación
$offset = ($page - 1) * $limit;

switch ($metodo) {
    case "GET":
        if ($count !== null) {
            // Si viene ?count → devolver total de productos
            echo json_encode(["total" => $controlador->contar()]);
            break;
        }

        if ($ordenar !== null) {
            echo json_encode($controlador->ordenarPrecio($ordenar, $limit, $offset));
            break;
        }

        if ($buscar !== null) {
            echo json_encode($controlador->buscar($buscar));
            break;
        }

        // GET /productos.php  → listar empleados
        if ($id === null) {
            // Sin id → devolvemos la lista completa
            echo json_encode($controlador->listar($include, $limit, $offset, $id_categoria));
            break;
        } else {
            // Con id → devolvemos un solo empleado (o error)
            echo json_encode($controlador->ver($id, $include));
            break;
        }
    case "POST":
        // Comprueba el token.
        $user = requireAuth(); // Obtenemos el payload.
        try {
            if ($user["rol"] === "admin" || $user["rol"] === "usuario") {
                // POST /producto.php → crear producto
                $input = json_decode(file_get_contents("php://input"), true) ?? [];
                echo json_encode($controlador->crear($input));
            } else {
                http_response_code(403); // Forbidden
                echo json_encode(["error" => "No tienes permisos para realizar esta acción"]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error al procesar la solicitud: " . $e->getMessage()]);
        }
        break;
    case "PUT":
        // Comprueba el token.
        $user = requireAuth(); // Obtenemos el payload.
        try {
            if ($user["rol"] === "admin") {
                // PUT /productos.php?id=5 → actualizar producto
                $input = json_decode(file_get_contents("php://input"), true) ?? [];
                echo json_encode($controlador->insertar($id, $input));
            } else {
                http_response_code(403); // Forbidden
                echo json_encode(["error" => "No tienes permisos para realizar esta acción"]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error al procesar la solicitud: " . $e->getMessage()]);
        }
        break;
    case "DELETE":
        // Comprueba el token.
        $user = requireAuth(); // Obtenemos el payload.
        try {
            if ($user["rol"] === "admin") {
                echo json_encode($controlador->borrar($id));
            } else {
                http_response_code(403); // Forbidden
                echo json_encode(["error" => "No tienes permisos para realizar esta acción"]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error al procesar la solicitud: " . $e->getMessage()]);
        }
        break;
}
