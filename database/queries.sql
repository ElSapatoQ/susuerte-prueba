USE susuerte_prueba;

-- 2.2 Consulta que retorna los 3 usuarios con mayor monto total apostado
-- en tiquetes ganadores.
SELECT
    u.nombre,
    SUM(t.monto) AS total_apostado_ganador
FROM usuarios u
INNER JOIN tiquetes t ON t.usuario_id = u.id
WHERE t.estado = 'ganador'
GROUP BY u.id, u.nombre
ORDER BY total_apostado_ganador DESC
LIMIT 3;

-- 2.3 Consulta que lista los usuarios sin ningún tiquete registrado.
SELECT
    u.id,
    u.nombre,
    u.saldo
FROM usuarios u
LEFT JOIN tiquetes t ON t.usuario_id = u.id
WHERE t.id IS NULL;