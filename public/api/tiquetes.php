<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Susuerte\Database;
use Susuerte\TiqueteService;
use Susuerte\UsuarioNoExisteException;
use Susuerte\SaldoInsuficienteException;

header('Content-Type: application/json; charset=utf-8');

function responderJson(int $codigo, array $data): void
{
    http_response_code($codigo);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responderJson(405, [
        'error' => 'Metodo no permitido.'
    ]);
}

$body = file_get_contents('php://input');
$data = json_decode($body, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    responderJson(400, [
        'error' => 'JSON invalido.'
    ]);
}

if (
    !isset($data['usuario_id']) ||
    !isset($data['monto']) ||
    !is_numeric($data['usuario_id']) ||
    !is_numeric($data['monto'])
) {
    responderJson(400, [
        'error' => 'Campos requeridos: usuario_id y monto.'
    ]);
}

try {
    $pdo = Database::conectar();
    $service = new TiqueteService($pdo);

    $tiquete = $service->crearTiquete(
        (int) $data['usuario_id'],
        (float) $data['monto']
    );

    responderJson(201, [
        'mensaje' => 'Tiquete creado correctamente.',
        'tiquete' => $tiquete
    ]);
} catch (InvalidArgumentException $e) {
    responderJson(400, [
        'error' => $e->getMessage()
    ]);
} catch (UsuarioNoExisteException $e) {
    responderJson(404, [
        'error' => $e->getMessage()
    ]);
} catch (SaldoInsuficienteException $e) {
    responderJson(422, [
        'error' => $e->getMessage()
    ]);
} catch (Throwable $e) {
    responderJson(500, [
        'error' => 'Error inesperado del servidor.'
    ]);
}