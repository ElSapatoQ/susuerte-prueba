<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Susuerte\Database;
use Susuerte\TiqueteService;

$pdo = Database::conectar();
$service = new TiqueteService($pdo);

try {
    $resultado = $service->crearTiquete(1, 5000);

    echo "Tiquete creado correctamente" . PHP_EOL;
    echo "ID tiquete: " . $resultado['id'] . PHP_EOL;
    echo "Usuario ID: " . $resultado['usuario_id'] . PHP_EOL;
    echo "Monto: " . $resultado['monto'] . PHP_EOL;
    echo "Estado: " . $resultado['estado'] . PHP_EOL;
    echo "Saldo anterior: " . $resultado['saldo_anterior'] . PHP_EOL;
    echo "Saldo actual: " . $resultado['saldo_actual'] . PHP_EOL;
} catch (Throwable $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}