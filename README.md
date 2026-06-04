# Prueba técnica Susuerte

Proyecto desarrollado para la prueba técnica del rol de Desarrollador de Software para la página web transaccional de Susuerte.

La solución implementa un sistema simplificado de usuarios y tiquetes de apuestas usando PHP puro, PDO y MySQL/MariaDB. El objetivo principal fue construir una solución funcional, sencilla de ejecutar y fácil de explicar.

## Tecnologías utilizadas

* PHP 8.x
* MySQL / MariaDB
* PDO
* Composer para autoload PSR-4
* HTML, CSS y JavaScript puro
* Servidor embebido de PHP para ejecución local

## Estructura del proyecto

```text
susuerte-prueba/
├── public/
│   ├── index.html
│   └── api/
│       ├── tiquetes.php
│       └── usuario_tiquetes.php
├── src/
│   ├── Database.php
│   ├── Premio.php
│   └── TiqueteService.php
├── database/
│   ├── schema.sql
│   ├── seed.sql
│   └── queries.sql
├── tests/
│   ├── test_premio.php
│   ├── test_queries.php
│   ├── test_database.php
│   ├── test_usuarios.php
│   └── test_crear_tiquete.php
├── composer.json
├── .gitignore
└── README.md
```

> La carpeta `vendor/` no se incluye en el repositorio porque se puede generar nuevamente con Composer.

## Instalación

Clonar el repositorio:

```bash
git https://github.com/ElSapatoQ/susuerte-prueba.git
cd susuerte-prueba
```

Generar el autoload de Composer:

```bash
composer dump-autoload
```

En este proyecto Composer se usa únicamente para el autoload PSR-4. No se instalaron librerías externas.

## Entorno local usado

Durante el desarrollo y las pruebas se usó XAMPP como entorno local.

En XAMPP se utilizaron principalmente:

- MySQL / MariaDB para la base de datos.
- phpMyAdmin para ejecutar `schema.sql`, `seed.sql` y revisar los datos.
- Apache solo como apoyo inicial, aunque finalmente la aplicación se ejecutó con el servidor embebido de PHP para no depender de que el proyecto estuviera dentro de `htdocs`.

Para probar el proyecto localmente, se debe iniciar MySQL desde el panel de XAMPP.

Luego, desde phpMyAdmin, se ejecutan los scripts SQL en este orden:

```text
1. database/schema.sql
2. database/seed.sql
```

## Configuración de base de datos

El proyecto usa una base de datos llamada:

```text
susuerte_prueba
```

Para crear la base de datos, tablas y datos iniciales, se puede usar phpMyAdmin desde XAMPP. Allí se ejecutan los archivos SQL en este orden:

```text
1. database/schema.sql
2. database/seed.sql
```

Esto puede hacerse desde phpMyAdmin o desde terminal si MySQL está disponible en el PATH.

El archivo `schema.sql` crea las tablas `usuarios` y `tiquetes`.
El archivo `seed.sql` carga usuarios y tiquetes de prueba.

Datos principales de prueba:

```text
ID 1 - Juan Perez       - saldo 50000
ID 2 - Maria Gomez      - saldo 10000
ID 3 - Carlos Ruiz      - saldo 0
ID 4 - Laura Martinez   - saldo 30000
ID 5 - Ana Torres       - saldo 20000
```

## Ejecutar el proyecto

Desde la raíz del proyecto:

```bash
php -S localhost:8000 -t public
```

Luego abrir en el navegador:

```text
http://localhost:8000
```

La opción `-t public` indica que la carpeta pública del proyecto es `public`.

---

# Parte 1: PHP y recursividad

## Objetivo

Implementar una función:

```php
calcularPremioAcumulado(array $niveles): float
```

Esta función recibe una estructura de premios anidados, donde cada premio tiene un `monto` y puede tener una lista de `hijos`.

Ejemplo:

```text
Premio 1000
├── Premio 500
└── Premio 250
    └── Premio 100
```

Resultado esperado:

```text
1000 + 500 + 250 + 100 = 1850
```

## Archivo implementado

```text
src/Premio.php
```

## Decisión técnica

Se usó recursividad porque la estructura puede tener varios niveles anidados y el enunciado pedía resolverlo de esa forma.

La función suma:

1. El monto del premio actual.
2. Los hijos del premio actual.
3. El siguiente premio del mismo nivel.

## Caso base

El caso base ocurre cuando el índice llega al final del arreglo:

```php
if ($indice >= count($niveles)) {
    return 0;
}
```

Esto significa que ya no hay más premios por recorrer y la función debe detenerse.

## Estructuras muy profundas

Cada llamada recursiva queda guardada temporalmente en el stack o pila de llamadas de PHP.

Si la estructura tuviera demasiados niveles, podrían generarse muchas llamadas recursivas y alcanzar un límite de memoria o stack. En un caso real con estructuras muy profundas, evaluaría una solución iterativa usando una pila manual.

## Probar la Parte 1

Ejecutar:

```bash
php tests/test_premio.php
```

Resultado esperado:

```text
Resultado: 1850
```

---

# Parte 2: Base de datos y consultas SQL

## Objetivo

Diseñar la base de datos y resolver las consultas solicitadas:

* Crear tablas con claves foráneas e índices.
* Consultar los 3 usuarios con mayor monto total apostado en tiquetes ganadores.
* Consultar usuarios sin tiquetes registrados.
* Explicar el uso de transacciones al registrar tiquetes.

## Archivos relacionados

```text
database/schema.sql
database/seed.sql
database/queries.sql
tests/test_queries.php
```

## Diseño

Se crearon dos tablas principales:

```text
usuarios
tiquetes
```

La relación es de uno a muchos:

```text
Un usuario puede tener muchos tiquetes.
Un tiquete pertenece a un solo usuario.
```

La tabla `tiquetes` tiene una clave foránea hacia `usuarios`:

```sql
FOREIGN KEY (usuario_id)
REFERENCES usuarios(id)
```

Esto evita crear tiquetes asociados a usuarios inexistentes.

## Tipos de datos

Para valores monetarios se usó:

```sql
DECIMAL(10, 2)
```

Se eligió `DECIMAL` porque es más adecuado que `FLOAT` para representar dinero y evitar imprecisiones con decimales.

## Estado del tiquete

Los tiquetes manejan el campo `estado` con los valores:

```text
pendiente
ganador
perdedor
```

Cuando se crea un tiquete desde la API, queda inicialmente como `pendiente`.

## Índices

Se agregaron índices sobre campos usados en búsquedas, relaciones y filtros:

```text
usuarios.nombre
tiquetes.usuario_id
tiquetes.estado
tiquetes.usuario_id, estado
```

El índice combinado `usuario_id, estado` ayuda en consultas donde se relacionan tiquetes con usuarios y se filtra por estado, por ejemplo tiquetes ganadores.

## Consulta: top 3 usuarios con mayor monto apostado en tiquetes ganadores

La consulta está en:

```text
database/queries.sql
```

La lógica es:

1. Unir usuarios con tiquetes.
2. Filtrar tiquetes con estado `ganador`.
3. Sumar el monto apostado por usuario.
4. Ordenar de mayor a menor.
5. Tomar los primeros 3.

Fragmentos principales:

```sql
SUM(t.monto) AS total_apostado_ganador
```

```sql
WHERE t.estado = 'ganador'
```

Resultado esperado con los datos de prueba:

```text
Laura Martinez - Total: 9000.00
Juan Perez - Total: 8000.00
Maria Gomez - Total: 1000.00
```

## Consulta: usuarios sin tiquetes

Se usa `LEFT JOIN` para traer usuarios aunque no tengan tiquetes:

```sql
LEFT JOIN tiquetes t ON t.usuario_id = u.id
WHERE t.id IS NULL
```

Resultado esperado:

```text
Carlos Ruiz
```

## Probar consultas

Ejecutar:

```bash
php tests/test_queries.php
```

Resultado esperado:

```text
Top 3 usuarios por monto apostado en tiquetes ganadores:
Laura Martinez - Total: 9000.00
Juan Perez - Total: 8000.00
Maria Gomez - Total: 1000.00

Usuarios sin tiquetes registrados:
3 - Carlos Ruiz - Saldo: 0.00
```

## Uso de transacciones

Al crear un tiquete se deben realizar varias operaciones relacionadas:

1. Validar que el usuario exista.
2. Validar que tenga saldo suficiente.
3. Descontar saldo.
4. Crear el tiquete.

Estas operaciones deben comportarse como una sola unidad. Si una falla, todas deben revertirse.

Por eso se usa una transacción:

```php
beginTransaction()
commit()
rollBack()
```

Esto evita inconsistencias como descontar saldo sin crear tiquete o crear un tiquete sin descontar saldo.

---

# Parte 3: API de tiquetes

## Objetivo

Implementar endpoints para crear tiquetes y consultar tiquetes por usuario.

## Rama de trabajo

Esta parte se trabajó en la rama:

```text
feature/api-tiquetes
```

Luego se integró a `main` mediante merge, dejando evidencia del flujo de trabajo con Git.

## Archivos relacionados

```text
src/TiqueteService.php
public/api/tiquetes.php
public/api/usuario_tiquetes.php
tests/test_crear_tiquete.php
```

## Endpoint POST: crear tiquete

Endpoint implementado:

```text
POST /api/tiquetes.php
```

El enunciado solicita `POST /api/tiquetes`. Como el proyecto está hecho en PHP puro, sin framework ni router, se implementó como archivo PHP dentro de `public/api`.

Cuerpo esperado:

```json
{
  "usuario_id": 1,
  "monto": 5000
}
```

Flujo:

1. Validar datos recibidos.
2. Validar que el usuario exista.
3. Validar saldo suficiente.
4. Iniciar transacción.
5. Descontar saldo.
6. Crear tiquete con estado `pendiente`.
7. Confirmar transacción.

Respuesta exitosa:

```json
{
  "mensaje": "Tiquete creado correctamente.",
  "tiquete": {
    "id": 18,
    "usuario_id": 1,
    "monto": 5000,
    "estado": "pendiente",
    "saldo_anterior": 45000,
    "saldo_actual": 40000
  }
}
```

## Códigos HTTP del POST

```text
201 = tiquete creado correctamente
400 = JSON inválido, campos faltantes o monto inválido
404 = usuario no existe
422 = saldo insuficiente
500 = error inesperado
```

## Endpoint GET: tiquetes por usuario

El enunciado solicita:

```text
GET /api/usuarios/{id}/tiquetes
```

Como el proyecto no usa router, se implementó el endpoint equivalente:

```text
GET /api/usuario_tiquetes.php?id=1
```

Respuesta exitosa:

```json
{
  "usuario_id": 1,
  "tiquetes": [
    {
      "id": 18,
      "usuario_id": 1,
      "monto": "5000.00",
      "estado": "pendiente",
      "creado_en": "..."
    }
  ]
}
```

Si el usuario no existe, responde `404`.

## Uso de `FOR UPDATE`

Durante la creación del tiquete, el usuario se consulta con `FOR UPDATE`.

Esto bloquea temporalmente la fila del usuario mientras dura la transacción y evita problemas si dos apuestas intentan descontar saldo al mismo tiempo.

Ejemplo:

```text
Saldo: 10000
Petición A: apuesta 8000
Petición B: apuesta 8000
```

Sin bloqueo, ambas podrían leer el saldo inicial y aprobarse incorrectamente.
Con `FOR UPDATE`, una espera a que la otra termine.

## Probar POST exitoso

Con el servidor activo:

```bash
php -S localhost:8000 -t public
```

Ejecutar en otra terminal PowerShell:

```powershell
Invoke-RestMethod -Uri "http://localhost:8000/api/tiquetes.php" -Method POST -ContentType "application/json" -Body '{"usuario_id":1,"monto":5000}'
```

Resultado esperado:

```text
Tiquete creado correctamente.
HTTP 201
```

## Probar usuario inexistente

```powershell
try {
  Invoke-RestMethod -Uri "http://localhost:8000/api/tiquetes.php" -Method POST -ContentType "application/json" -Body '{"usuario_id":999,"monto":5000}'
} catch {
  $_.Exception.Response.StatusCode.value__
}
```

Resultado esperado:

```text
404
```

## Probar saldo insuficiente

```powershell
try {
  Invoke-RestMethod -Uri "http://localhost:8000/api/tiquetes.php" -Method POST -ContentType "application/json" -Body '{"usuario_id":3,"monto":5000}'
} catch {
  $_.Exception.Response.StatusCode.value__
}
```

Resultado esperado:

```text
422
```

## Probar monto inválido

```powershell
try {
  Invoke-RestMethod -Uri "http://localhost:8000/api/tiquetes.php" -Method POST -ContentType "application/json" -Body '{"usuario_id":1,"monto":0}'
} catch {
  $_.Exception.Response.StatusCode.value__
}
```

Resultado esperado:

```text
400
```

## Probar GET de tiquetes

```powershell
Invoke-RestMethod -Uri "http://localhost:8000/api/usuario_tiquetes.php?id=1" -Method GET | ConvertTo-Json -Depth 5
```

## Probar GET con usuario inexistente

```powershell
try {
  Invoke-RestMethod -Uri "http://localhost:8000/api/usuario_tiquetes.php?id=999" -Method GET
} catch {
  $_.Exception.Response.StatusCode.value__
}
```

Resultado esperado:

```text
404
```

---

# Parte 4: Interfaz web

## Objetivo

Crear una página `index.html` que permita registrar tiquetes desde el navegador.

## Archivo relacionado

```text
public/index.html
```

## Funcionamiento

La página contiene un formulario con:

```text
usuario_id
monto
```

Al enviar el formulario, JavaScript evita la recarga de la página:

```javascript
event.preventDefault()
```

Luego envía los datos al endpoint de creación de tiquetes usando `fetch`:

```javascript
fetch('/api/tiquetes.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json'
    },
    body: JSON.stringify({
        usuario_id: Number(usuarioId),
        monto: Number(monto)
    })
})
```

## Mensajes al usuario

La interfaz muestra mensajes diferentes según el código HTTP recibido:

```text
201 = éxito
400 = datos inválidos
404 = usuario no encontrado
422 = saldo insuficiente
500 = error inesperado
```

Durante las pruebas se corrigió un problema visual: el backend respondía correctamente, pero el mensaje no aparecía porque el contenedor seguía oculto. Se solucionó mostrando nuevamente el contenedor al presentar un mensaje:

```javascript
mensaje.style.display = 'block'
```

## Actualización del DOM

Cuando el tiquete se crea correctamente, se agrega a una lista visible sin recargar la página.

La función crea un nuevo elemento HTML y lo agrega al inicio de la lista:

```javascript
document.createElement('div')
listaTiquetes.prepend(item)
```

## Probar la interfaz

Iniciar el servidor:

```bash
php -S localhost:8000 -t public
```

Abrir:

```text
http://localhost:8000
```

Pruebas recomendadas:

```text
Usuario ID: 1
Monto: 5000
Resultado: tiquete creado correctamente
```

```text
Usuario ID: 999
Monto: 5000
Resultado: usuario no existe
```

```text
Usuario ID: 3
Monto: 5000
Resultado: saldo insuficiente
```

```text
Usuario ID: 1
Monto: 0
Resultado: monto inválido
```

---

# Parte 6 - Mejora libre propuesta: idempotencia

## Objetivo

Como mejora libre propongo implementar idempotencia en la creación de tiquetes.

La idea es evitar que una misma operación se procese más de una vez por accidente.

## Problema

Actualmente, si el usuario hace doble clic, si hay lentitud de red o si un cliente reintenta la petición, podrían crearse dos tiquetes y descontarse saldo dos veces.

Ejemplo:

```text
Saldo inicial: 50000
Monto apostado: 5000
```

Resultado correcto:

```text
Saldo final: 45000
Tiquetes creados: 1
```

Resultado incorrecto por duplicado:

```text
Saldo final: 40000
Tiquetes creados: 2
```

## Propuesta

Agregar una clave única por intento de creación:

```text
idempotency_key
```

Ejemplo de solicitud:

```json
{
  "usuario_id": 1,
  "monto": 5000,
  "idempotency_key": "usuario-1-20260603-abc123"
}
```

La base de datos podría tener una columna:

```sql
idempotency_key VARCHAR(100) NULL
```

Y un índice único:

```sql
UNIQUE INDEX uq_tiquetes_idempotency_key (idempotency_key)
```

Si llega otra solicitud con la misma clave, el sistema devolvería el tiquete ya creado sin descontar saldo nuevamente.

## Valor para el negocio

Esta mejora protege operaciones relacionadas con dinero.

Ayuda a:

* Evitar tiquetes duplicados.
* Evitar descuentos dobles de saldo.
* Reducir reclamos de usuarios.
* Mejorar la confianza en la plataforma.
* Manejar mejor reintentos por errores de red.

## Viabilidad

Es una mejora viable porque no requiere cambiar toda la arquitectura. Los cambios principales serían:

1. Agregar `idempotency_key` a `tiquetes`.
2. Crear un índice único.
3. Recibir la clave en el endpoint `POST`.
4. Revisar si ya existe un tiquete con esa clave.
5. Devolver el tiquete existente si la clave ya fue usada.
6. Generar la clave desde el frontend.

Elegí esta mejora porque está directamente relacionada con el flujo crítico del sistema: crear tiquetes y descontar saldo.

---

# Decisiones técnicas y supuestos

* Se usó PHP puro para mantener una solución simple y fácil de revisar.
* Se usó PDO para conexión a base de datos y consultas preparadas.
* Se usó Composer solo para autoload PSR-4.
* Se usó MySQL/MariaDB como base de datos relacional.
* Se usó `DECIMAL(10,2)` para valores monetarios.
* Se usó una transacción para crear tiquetes y descontar saldo.
* Se usó `FOR UPDATE` para proteger el saldo ante operaciones concurrentes.
* No se usó framework ni router; por eso algunos endpoints conservan extensión `.php`.
* El endpoint `GET /api/usuario_tiquetes.php?id=1` se documenta como equivalente funcional de `GET /api/usuarios/{id}/tiquetes`.
* La lista visible del frontend muestra los tiquetes creados durante la sesión actual, no todos los tiquetes existentes en base de datos.
* Las credenciales de base de datos están en `src/Database.php` para simplificar la prueba. En producción se moverían a variables de entorno.

# Si tuviera más tiempo...

* Implementaría un router para respetar exactamente rutas como `/api/tiquetes` y `/api/usuarios/{id}/tiquetes`.
* Agregaría pruebas automatizadas con PHPUnit.
* Movería credenciales y configuración a variables de entorno.
* Separaría CSS y JavaScript en archivos independientes.
* Agregaría carga inicial de tiquetes existentes desde el frontend.
* Implementaría la mejora de idempotencia.
* Agregaría validaciones más estrictas a nivel de base de datos, como evitar montos menores o iguales a cero.
* Estandarizaría el formato de errores JSON.
* Revisaría los índices con `EXPLAIN` usando un volumen mayor de datos.
