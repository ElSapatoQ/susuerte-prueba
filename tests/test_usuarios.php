<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Susuerte\Database;

$pdo = Database::conectar();

$stmt = $pdo->query('SELECT id, nombre, saldo FROM usuarios');

$usuarios = $stmt->fetchAll();

foreach ($usuarios as $usuario) {
    echo $usuario['id'] . ' - ' . $usuario['nombre'] . ' - Saldo: ' . $usuario['saldo'] . PHP_EOL;
}