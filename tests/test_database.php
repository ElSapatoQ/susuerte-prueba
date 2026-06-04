<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Susuerte\Database;

try {
    $pdo = Database::conectar();

    echo "Conexion exitosa a la base de datos" . PHP_EOL;
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}