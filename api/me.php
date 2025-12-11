<?php

header("Content-Type: application/json; charset=utf-8");

require_once __DIR__ . "/../database.php";
require_once __DIR__ . "/../config.php";
require __DIR__ . "/../controller/login_controlador.php";
require_once __DIR__ . "/auth.php";
require __DIR__ . "/../modelo/login_modelo.php";


$db = Database::getConnection();
$modelo = new LoginModelo($db);
$controller = new LoginControlador($modelo);
// 1. Verificamos el token y obtenemos los datos
$user = requireAuth();


// 2. El ID y rol del usuario vienen desde el token
$userId = $user['id'];

$user = $controller->infoUser($userId);

if (!$user) {
    http_response_code(404);
    echo json_encode(["error" => "Usuario no encontrado"]);
    exit;
}

http_response_code(200);
echo json_encode([
    "nombre" => $user["nombre"],
    "correo" => $user["correo"],
    "rol" => $user["rol"],
]);
