<?php

namespace Susuerte;

use PDO;
use Exception;
use InvalidArgumentException;

class UsuarioNoExisteException extends Exception {}
class SaldoInsuficienteException extends Exception {}

class TiqueteService
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function crearTiquete(int $usuarioId, float $monto): array
    {
        if ($monto <= 0) {
            throw new InvalidArgumentException('El monto debe ser mayor que cero.');
        }

        try {
            $this->pdo->beginTransaction();

            $usuario = $this->buscarUsuarioPorId($usuarioId);

            if (!$usuario) {
                throw new UsuarioNoExisteException('El usuario no existe.');
            }

            if ((float) $usuario['saldo'] < $monto) {
                throw new SaldoInsuficienteException('El usuario no tiene saldo suficiente.');
            }

            $saldoAnterior = (float) $usuario['saldo'];
            $saldoActual = $saldoAnterior - $monto;

            $this->actualizarSaldoUsuario($usuarioId, $saldoActual);

            $tiqueteId = $this->registrarTiquete($usuarioId, $monto);

            $this->pdo->commit();

            return [
                'id' => $tiqueteId,
                'usuario_id' => $usuarioId,
                'monto' => $monto,
                'estado' => 'pendiente',
                'saldo_anterior' => $saldoAnterior,
                'saldo_actual' => $saldoActual
            ];
        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $e;
        }
    }

    public function listarTiquetesPorUsuario(int $usuarioId): array
    {
        $usuario = $this->buscarUsuarioSimple($usuarioId);

        if (!$usuario) {
            throw new UsuarioNoExisteException('El usuario no existe.');
        }

        $stmt = $this->pdo->prepare(
            'SELECT id, usuario_id, monto, estado, creado_en
             FROM tiquetes
             WHERE usuario_id = :usuario_id
             ORDER BY creado_en DESC'
        );

        $stmt->execute([
            'usuario_id' => $usuarioId
        ]);

        return $stmt->fetchAll();
    }

    private function buscarUsuarioPorId(int $usuarioId): array|false
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, nombre, saldo
             FROM usuarios
             WHERE id = :id
             FOR UPDATE'
        );

        $stmt->execute([
            'id' => $usuarioId
        ]);

        return $stmt->fetch();
    }

    private function buscarUsuarioSimple(int $usuarioId): array|false
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, nombre, saldo
             FROM usuarios
             WHERE id = :id'
        );

        $stmt->execute([
            'id' => $usuarioId
        ]);

        return $stmt->fetch();
    }

    private function actualizarSaldoUsuario(int $usuarioId, float $saldoActual): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE usuarios
             SET saldo = :saldo
             WHERE id = :id'
        );

        $stmt->execute([
            'saldo' => $saldoActual,
            'id' => $usuarioId
        ]);
    }

    private function registrarTiquete(int $usuarioId, float $monto): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO tiquetes (usuario_id, monto, estado)
             VALUES (:usuario_id, :monto, :estado)'
        );

        $stmt->execute([
            'usuario_id' => $usuarioId,
            'monto' => $monto,
            'estado' => 'pendiente'
        ]);

        return (int) $this->pdo->lastInsertId();
    }
}