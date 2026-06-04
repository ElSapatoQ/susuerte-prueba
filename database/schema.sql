CREATE DATABASE IF NOT EXISTS susuerte_prueba
CHARACTER SET utf8mb4
COLLATE utf8mb4_general_ci;

USE susuerte_prueba;

DROP TABLE IF EXISTS tiquetes;
DROP TABLE IF EXISTS usuarios;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    saldo DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_usuarios_nombre (nombre)
);

CREATE TABLE tiquetes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    monto_apostado DECIMAL(10, 2) NOT NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'PENDIENTE',
    es_ganador TINYINT(1) NOT NULL DEFAULT 0,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_tiquetes_usuario
        FOREIGN KEY (usuario_id)
        REFERENCES usuarios(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    INDEX idx_tiquetes_usuario_id (usuario_id),
    INDEX idx_tiquetes_es_ganador (es_ganador),
    INDEX idx_tiquetes_usuario_ganador (usuario_id, es_ganador)
);