USE susuerte_prueba;

INSERT INTO usuarios (nombre, saldo) VALUES
('Juan Perez', 50000.00),
('Maria Gomez', 10000.00),
('Carlos Ruiz', 0.00),
('Laura Martinez', 30000.00),
('Ana Torres', 20000.00);

INSERT INTO tiquetes (usuario_id, monto_apostado, estado, es_ganador) VALUES
(1, 5000.00, 'GANADOR', 1),
(1, 2000.00, 'PERDEDOR', 0),
(1, 3000.00, 'GANADOR', 1),

(2, 1000.00, 'GANADOR', 1),
(2, 1500.00, 'PERDEDOR', 0),

(4, 7000.00, 'GANADOR', 1),
(4, 2000.00, 'GANADOR', 1),

(5, 4000.00, 'PERDEDOR', 0);