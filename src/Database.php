<?php

namespace Susuerte;

use PDO;
use PDOException;

class Database
{
    private const HOST = 'localhost';
    private const DB_NAME = 'susuerte_prueba';
    private const USER = 'root';
    private const PASSWORD = '';

    public static function conectar(): PDO
    {
        try {
            $dsn = 'mysql:host=' . self::HOST . ';dbname=' . self::DB_NAME . ';charset=utf8mb4';

            $pdo = new PDO($dsn, self::USER, self::PASSWORD);

            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            return $pdo;
        } catch (PDOException $e) {
            throw new PDOException('Error al conectar con la base de datos: ' . $e->getMessage());
        }
    }
}