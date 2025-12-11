<?php
header("Content-Type: application/json; charset=utf-8");

//Carga automáticamente todas las clases de las librerías que has instalado
require __DIR__ . "/../vendor/autoload.php";
require __DIR__ . "/../config.php";
require __DIR__ . "/../database.php";
require __DIR__ . "/../controller/login_controlador.php";
require __DIR__ . "/../modelo/login_modelo.php";

use Firebase\JWT\JWT;

// Crear conexión, modelo y controlador.
$db = Database::getConnection();
$modelo = new LoginModelo($db);
$controlador = new LoginControlador($modelo);

// Aceptar solo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["error" => "Método no permitido. Usa POST."]);
    exit;
}

// Leer JSON de entrada
$input = json_decode(file_get_contents("php://input"), true) ?? [];
$resultado = $controlador->infoToken($input);

// Si hay error, devolver y terminar
if (isset($resultado["error"])) {
    echo json_encode($resultado);
    exit;
}

// Usuario válido, generar token
$payload = [
    "id"    => $resultado["id"],
    "correo" => $resultado["correo"], 
    "rol"   => $resultado["rol"],
    "iat"   => time(),
    "exp"   => time() + 3600,
];

// Crear token
$token = JWT::encode($payload, JWT_SECRET, 'HS256');

// Devolver token al cliente
http_response_code(200);
echo json_encode([
    "token" => $token,
]);

