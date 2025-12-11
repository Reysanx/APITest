<?php
//https://asanjar.alwaysdata.net/api_techuniverse/api/productos
//http://localhost:8080/api/productos

const DB_DSN = "mysql:host=db;dbname=techstore;charset=utf8";
const DB_USER = 'root';
const DB_PASS = 'usuario1';
const API_VERSION = '1.0.0';

// Clave secreta para firmar los tokens JWT
define("JWT_SECRET", "techuniverseAPI");