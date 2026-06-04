USE susuerte_prueba;

INSERT INTO usuarios (nombre, saldo) VALUES
('Juan Perez', 50000.00),
('Maria Gomez', 10000.00),
('Carlos Ruiz', 0.00),
('Laura Martinez', 30000.00),
('Ana Torres', 20000.00);

INSERT INTO tiquetes (usuario_id, monto, estado) VALUES
(1, 5000.00, 'ganador'),
(1, 2000.00, 'perdedor'),
(1, 3000.00, 'ganador'),

(2, 1000.00, 'ganador'),
(2, 1500.00, 'perdedor'),

(4, 7000.00, 'ganador'),
(4, 2000.00, 'ganador'),

(5, 4000.00, 'perdedor');