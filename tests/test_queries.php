<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Susuerte\Database;

$pdo = Database::conectar();

echo "Top 3 usuarios por monto apostado en tiquetes ganadores:" . PHP_EOL;

$sqlTopUsuarios = "
    SELECT
        u.nombre,
        SUM(t.monto_apostado) AS total_apostado_ganador
    FROM usuarios u
    INNER JOIN tiquetes t ON t.usuario_id = u.id
    WHERE t.es_ganador = 1
    GROUP BY u.id, u.nombre
    ORDER BY total_apostado_ganador DESC
    LIMIT 3
";

$stmt = $pdo->query($sqlTopUsuarios);
$usuarios = $stmt->fetchAll();

foreach ($usuarios as $usuario) {
    echo $usuario['nombre'] . ' - Total: ' . $usuario['total_apostado_ganador'] . PHP_EOL;
}

echo PHP_EOL;
echo "Usuarios sin tiquetes registrados:" . PHP_EOL;

$sqlUsuariosSinTiquetes = "
    SELECT
        u.id,
        u.nombre,
        u.saldo
    FROM usuarios u
    LEFT JOIN tiquetes t ON t.usuario_id = u.id
    WHERE t.id IS NULL
";

$stmt = $pdo->query($sqlUsuariosSinTiquetes);
$usuariosSinTiquetes = $stmt->fetchAll();

foreach ($usuariosSinTiquetes as $usuario) {
    echo $usuario['id'] . ' - ' . $usuario['nombre'] . ' - Saldo: ' . $usuario['saldo'] . PHP_EOL;
}