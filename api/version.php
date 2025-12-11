<?php
require_once __DIR__ . "/../config.php";

header('Content-Type: application/json; charset=utf-8');

echo json_encode([
    "version" => API_VERSION,
    "descripcion" => "API del e-commerce TechUniverse",
]);
