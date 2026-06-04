<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Susuerte\Database;
use Susuerte\TiqueteService;
use Susuerte\UsuarioNoExisteException;

header('Content-Type: application/json; charset=utf-8');

function responderJson(int $codigo, array $data): void
{
    http_response_code($codigo);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    responderJson(405, [
        'error' => 'Metodo no permitido.'
    ]);
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    responderJson(400, [
        'error' => 'Debe enviar un id de usuario valido.'
    ]);
}

try {
    $pdo = Database::conectar();
    $service = new TiqueteService($pdo);

    $usuarioId = (int) $_GET['id'];

    $tiquetes = $service->listarTiquetesPorUsuario($usuarioId);

    responderJson(200, [
        'usuario_id' => $usuarioId,
        'tiquetes' => $tiquetes
    ]);
} catch (UsuarioNoExisteException $e) {
    responderJson(404, [
        'error' => $e->getMessage()
    ]);
} catch (Throwable $e) {
    responderJson(500, [
        'error' => 'Error inesperado del servidor.'
    ]);
}