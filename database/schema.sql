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
    monto DECIMAL(10, 2) NOT NULL,
    estado ENUM('ganador', 'perdedor', 'pendiente') NOT NULL DEFAULT 'pendiente',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_tiquetes_usuario
        FOREIGN KEY (usuario_id)
        REFERENCES usuarios(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    INDEX idx_tiquetes_usuario_id (usuario_id),
    INDEX idx_tiquetes_estado (estado),
    INDEX idx_tiquetes_usuario_estado (usuario_id, estado)
);