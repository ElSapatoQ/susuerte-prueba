# Prueba técnica Susuerte

Este proyecto corresponde a la prueba técnica para el rol de Desarrollador de Software para la página web transaccional de Susuerte.

La solución se está desarrollando en PHP, usando una estructura sencilla y clara, con el objetivo de que el código no solo funcione, sino que también sea fácil de entender, explicar y sustentar.

## Tecnologías utilizadas

* PHP
* MySQL / MariaDB
* PDO para la conexión con base de datos
* Composer para autoload PSR-4
* XAMPP como entorno local de desarrollo

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
│   ├── TiqueteService.php
│   └── Premio.php
├── database/
│   ├── schema.sql
│   └── seed.sql
├── README.md
├── .gitignore
├── composer.json
└── vendor/
└── tests/
```

## Instalación inicial

Clonar el repositorio:

```bash
git clone URL_DEL_REPOSITORIO
```

Entrar a la carpeta del proyecto:

```bash
cd susuerte-prueba
```

Instalar o generar el autoload de Composer:

```bash
composer dump-autoload
```

> Nota: en este proyecto Composer se usa únicamente para manejar el autoload PSR-4. No se están usando librerías externas.

## Configuración de Composer

El archivo `composer.json` contiene la configuración del autoload:

```json
{
  "name": "santiago/susuerte-prueba",
  "description": "Prueba tecnica Susuerte - sistema simplificado de tiquetes",
  "type": "project",
  "autoload": {
    "psr-4": {
      "Susuerte\\": "src/"
    }
  },
  "require": {}
}
```

Esto permite cargar automáticamente las clases ubicadas dentro de la carpeta `src/` usando el namespace `Susuerte`.

---

# Parte 1: PHP y recursividad

## Objetivo

Implementar una función llamada:

```php
calcularPremioAcumulado(array $niveles): float
```

Esta función recibe una estructura de premios organizada por niveles. Cada premio tiene un monto y también puede tener otros premios hijos.

Ejemplo de estructura:

```php
$niveles = [
    [
        'monto' => 1000,
        'hijos' => [
            [
                'monto' => 500,
                'hijos' => []
            ],
            [
                'monto' => 250,
                'hijos' => [
                    [
                        'monto' => 100,
                        'hijos' => []
                    ]
                ]
            ]
        ]
    ]
];
```

En este caso, el resultado esperado es:

```text
1000 + 500 + 250 + 100 = 1850
```

## Archivo implementado

La solución se encuentra en:

```text
src/Premio.php
```


## Explicación de la solución

Para resolver esta parte, entendí que los premios pueden estar organizados como una estructura con varios niveles. Es decir, un premio puede tener un monto propio y también puede tener otros premios dentro.

La idea de la recursividad es que la función repite la misma tarea varias veces: toma un premio, suma su monto y luego revisa si ese premio tiene otros premios dentro. Si tiene hijos, vuelve a aplicar la misma lógica sobre ellos.

En mi solución, la función empieza revisando el primer premio de la lista. Suma su monto, luego revisa sus hijos y después pasa al siguiente premio del mismo nivel. Esto permite recorrer toda la estructura sin usar ciclos como `for`, `foreach` o `while`, porque la misma función se va llamando a sí misma para continuar el recorrido.

Elegí esta solución porque el enunciado pedía específicamente usar recursividad. Para este caso funciona bien, ya que la estructura de premios se parece a una lista donde cada elemento puede tener más elementos dentro.

## Caso base de la recursión

El caso base ocurre cuando el índice llega al final del arreglo de niveles:

```php
if ($indice >= count($niveles)) {
    return 0;
}
```

Esto significa que ya no hay más premios por revisar en esa lista. En ese momento la función devuelve `0` y empieza a devolver los resultados acumulados.

El caso base es importante porque evita que la función se siga llamando indefinidamente.

## ¿Qué ocurre con una estructura muy profunda?

La solución usa recursividad porque el enunciado lo solicita. Cada llamada recursiva queda pendiente hasta que sus llamadas internas terminan.

PHP guarda esas llamadas pendientes en una memoria llamada `stack` o pila de llamadas. El stack funciona como una pila donde PHP recuerda qué funciones están en ejecución y a cuál debe regresar cuando una función termina.

Si la estructura de premios tuviera demasiados niveles anidados, se podrían generar muchas llamadas recursivas. En ese caso, PHP tendría que guardar demasiadas llamadas pendientes y eventualmente podría alcanzar un límite de memoria o de stack.

En un caso real con estructuras muy profundas, evaluaría una solución iterativa usando una pila manual para tener mayor control del recorrido. Sin embargo, para esta prueba mantuve la recursividad porque era el objetivo principal del ejercicio.

## Prueba manual de la función

Para probar la función de forma sencilla, se creó una carpeta llamada `tests` en la raíz del proyecto.


El archivo de prueba se encuentra en:

```text
tests/test_premio.php
```

Para ejecutar la prueba desde la raíz del proyecto:

```bash
php tests/test_premio.php
```

Resultado esperado:

```text
Resultado: 1850
```

En este archivo se usa:

```php
require_once __DIR__ . '/../vendor/autoload.php';
```

Esto se debe a que el archivo `test_premio.php` está dentro de la carpeta `tests`. Por eso se usa `../` para subir un nivel hasta la raíz del proyecto y luego entrar a `vendor/autoload.php`.

Esta prueba permite validar rápidamente que la función `calcularPremioAcumulado` está sumando correctamente todos los montos de la estructura de premios.


## Supuestos hasta el momento

* Cada premio puede tener un campo `monto`.
* Cada premio puede tener un campo `hijos`.
* Si un premio no tiene hijos, se considera como una lista vacía.
* Si el monto no existe, se toma como `0`.
* La función debe recorrer toda la estructura sin usar ciclos explícitos para la jerarquía.

## Si tuviera más tiempo...

* Agregaría pruebas automatizadas para validar diferentes estructuras de premios.
* Validaría con más detalle que todos los montos sean numéricos.
* Agregaría manejo de errores para estructuras mal formadas.
* Compararía esta solución recursiva con una solución iterativa para casos con estructuras muy profundas.


---

# Parte 2: Base de datos y consultas SQL

## Objetivo

En esta parte se diseñó la base de datos para manejar usuarios y tiquetes de apuestas.

El objetivo fue cubrir los siguientes puntos del enunciado:

* Crear las tablas necesarias con claves foráneas e índices apropiados.
* Consultar los 3 usuarios con mayor monto total apostado en tiquetes ganadores.
* Consultar los usuarios que no tienen ningún tiquete registrado.
* Explicar por qué es importante usar una transacción al registrar un tiquete que descuenta saldo.

## Archivos relacionados

Los archivos usados para esta parte son:

```text
database/
├── schema.sql
├── seed.sql
└── queries.sql
```

También se agregó una prueba manual en:

```text
tests/test_queries.php
```

## Diseño de la base de datos

Se creó una base de datos llamada:

```text
susuerte_prueba
```

Dentro de esta base de datos se definieron dos tablas principales:

```text
usuarios
tiquetes
```

La tabla `usuarios` almacena la información básica del usuario, incluyendo su saldo disponible.

La tabla `tiquetes` almacena las apuestas realizadas por los usuarios. Cada tiquete pertenece a un usuario específico.

## Tabla usuarios

La tabla `usuarios` contiene campos como:

```sql
id
nombre
saldo
creado_en
```

El campo `id` funciona como identificador único del usuario.

El campo `saldo` se definió como:

```sql
DECIMAL(10, 2)
```

Se usó `DECIMAL` porque el saldo representa dinero. Para valores monetarios es mejor evitar tipos como `FLOAT`, ya que pueden generar pequeñas imprecisiones con decimales.

## Tabla tiquetes

La tabla `tiquetes` contiene campos como:

```sql
id
usuario_id
monto
estado
creado_en
```

El campo `usuario_id` permite relacionar cada tiquete con un usuario.

El campo `monto` representa el valor apostado en el tiquete.

El campo `estado` permite identificar el estado actual del tiquete. Para este proyecto se manejan los siguientes estados:

```text
ganador
perdedor
pendiente
```

El estado por defecto es:

```text
pendiente
```

Esto significa que cuando se crea un tiquete nuevo, todavía no se considera ganador ni perdedor.

## Clave foránea

La relación entre usuarios y tiquetes se definió con una clave foránea desde `tiquetes.usuario_id` hacia `usuarios.id`.

Fragmento representativo:

```sql
FOREIGN KEY (usuario_id)
REFERENCES usuarios(id)
```

Esto garantiza que no se pueda crear un tiquete para un usuario que no existe.

Por ejemplo, si se intenta registrar un tiquete con `usuario_id = 999` y ese usuario no existe, la base de datos rechaza la operación.

Esto ayuda a mantener la integridad de los datos.

## Índices utilizados

Se agregaron índices para mejorar consultas comunes sobre las tablas.

En la tabla `usuarios` se agregó un índice sobre el nombre:

```sql
INDEX idx_usuarios_nombre (nombre)
```

En la tabla `tiquetes` se agregaron índices sobre:

```sql
usuario_id
estado
usuario_id, estado
```

Estos índices ayudan especialmente en consultas donde se necesita buscar tiquetes por usuario o filtrar tiquetes por estado.

El índice combinado:

```sql
INDEX idx_tiquetes_usuario_estado (usuario_id, estado)
```

es útil porque una de las consultas principales necesita relacionar tiquetes con usuarios y filtrar únicamente los tiquetes ganadores.

## Datos de prueba

En el archivo `seed.sql` se agregaron usuarios y tiquetes iniciales para poder probar las consultas.

Los datos permiten validar casos como:

* Usuarios con varios tiquetes.
* Usuarios con tiquetes ganadores.
* Usuarios con tiquetes perdedores.
* Usuarios sin ningún tiquete registrado.

Esto facilita verificar que las consultas devuelven resultados correctos.

## Consulta: top 3 usuarios con mayor monto apostado en tiquetes ganadores

El punto 2.2 solicita una consulta que retorne los 3 usuarios con mayor monto total apostado en tiquetes ganadores.

La consulta se encuentra en:

```text
database/queries.sql
```

La idea general de la consulta es:

1. Unir usuarios con tiquetes.
2. Filtrar solo los tiquetes con estado `ganador`.
3. Sumar el monto apostado por cada usuario.
4. Ordenar de mayor a menor.
5. Tomar solo los primeros 3 resultados.

Fragmento principal:

```sql
SUM(t.monto) AS total_apostado_ganador
```

Este fragmento suma el dinero apostado en los tiquetes ganadores.

También se usa:

```sql
WHERE t.estado = 'ganador'
```

para tener en cuenta únicamente los tiquetes ganadores.

Con los datos de prueba, el resultado esperado es:

```text
Laura Martinez   9000.00
Juan Perez       8000.00
Maria Gomez      1000.00
```

## Consulta: usuarios sin ningún tiquete registrado

El punto 2.3 solicita listar los usuarios que no tienen ningún tiquete registrado.

Para esto se usa un `LEFT JOIN`.

La idea es traer todos los usuarios, incluso si no tienen tiquetes asociados. Luego se filtran los casos donde no se encontró ningún tiquete.

Fragmento principal:

```sql
LEFT JOIN tiquetes t ON t.usuario_id = u.id
WHERE t.id IS NULL
```

`LEFT JOIN` permite conservar los usuarios aunque no tengan registros relacionados en `tiquetes`.

La condición:

```sql
WHERE t.id IS NULL
```

significa que no se encontró ningún tiquete para ese usuario.

Con los datos de prueba, el resultado esperado es:

```text
Carlos Ruiz
```

## Prueba manual de las consultas

Para probar las consultas desde PHP, se creó el archivo:

```text
tests/test_queries.php
```

Este archivo ejecuta las dos consultas principales y muestra los resultados en consola.

Para ejecutarlo desde la raíz del proyecto:

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

## ¿Por qué usar una transacción al registrar un tiquete?

Al registrar un tiquete ocurren varias operaciones que deben mantenerse sincronizadas:

1. Validar que el usuario exista.
2. Validar que tenga saldo suficiente.
3. Descontar el saldo del usuario.
4. Crear el tiquete.

Estas operaciones deben tratarse como una sola unidad.

Por ejemplo, si se descuenta el saldo pero falla la creación del tiquete, el usuario perdería dinero sin tener una apuesta registrada.

También podría pasar lo contrario: que se cree un tiquete pero no se descuente el saldo correctamente.

Para evitar esos casos se usa una transacción.

Una transacción permite iniciar un bloque de operaciones y confirmar los cambios solo si todo sale bien. Si algo falla, se revierten los cambios.

En PDO esto se maneja con métodos como:

```php
beginTransaction()
commit()
rollBack()
```

La idea es:

```text
Si todo sale bien: confirmar cambios.
Si algo falla: deshacer cambios.
```

Por eso, en este proyecto es importante usar transacciones al crear tiquetes, ya que se está manejando saldo de usuarios y se debe evitar que la información quede inconsistente.

## Decisiones tomadas

* Se usó una base de datos relacional porque el problema tiene una relación clara entre usuarios y tiquetes.
* Se usó una clave foránea para garantizar que cada tiquete pertenezca a un usuario existente.
* Se usó `DECIMAL(10,2)` para representar dinero de forma más precisa.
* Se usó el campo `estado` para identificar si un tiquete está `pendiente`, `ganador` o `perdedor`.
* Se agregaron índices sobre campos usados en relaciones y filtros.
* Se creó un archivo `seed.sql` para poder probar las consultas de forma rápida.
* Se separaron las consultas del enunciado en `queries.sql` para que sean fáciles de revisar.

---

# Parte 3: API de tiquetes

## Objetivo

En esta parte se implementó una API sencilla en PHP puro para crear tiquetes y consultar los tiquetes asociados a un usuario.

El objetivo fue cubrir los siguientes puntos del enunciado:

* Crear un endpoint `POST /api/tiquetes`.
* Recibir un JSON con `usuario_id` y `monto`.
* Validar que el usuario exista.
* Validar que el usuario tenga saldo suficiente.
* Descontar el monto del saldo del usuario dentro de una transacción.
* Crear el tiquete.
* Crear un endpoint para consultar los tiquetes de un usuario.
* Retornar respuestas en formato JSON con códigos HTTP adecuados.

## Rama de trabajo

Para esta parte se trabajó en la rama:

```text
feature/api-tiquetes
```

Esto se hizo para mantener separado el desarrollo de la API y dejar evidencia del flujo de trabajo con ramas.

Al finalizar la parte, esta rama se integra a `main` mediante un merge.

## Archivos relacionados

Los archivos principales de esta parte son:

```text
src/
└── TiqueteService.php

public/
└── api/
    ├── tiquetes.php
    └── usuario_tiquetes.php
```

También se agregó una prueba manual en:

```text
tests/test_crear_tiquete.php
```

## Servicio de tiquetes

La lógica principal se encuentra en:

```text
src/TiqueteService.php
```

Esta clase se encarga de crear tiquetes y consultar los tiquetes de un usuario.

La función principal para registrar un tiquete es:

```php
crearTiquete(int $usuarioId, float $monto): array
```

Esta función realiza los siguientes pasos:

1. Valida que el monto sea mayor que cero.
2. Inicia una transacción.
3. Busca el usuario en la base de datos.
4. Valida que el usuario exista.
5. Valida que el usuario tenga saldo suficiente.
6. Calcula el nuevo saldo.
7. Actualiza el saldo del usuario.
8. Registra el tiquete con estado `pendiente`.
9. Confirma la transacción.

Si algo falla durante el proceso, se ejecuta un `rollBack()` para deshacer los cambios.

## Uso de transacción al crear el tiquete

El registro de un tiquete modifica dos partes importantes de la base de datos:

```text
usuarios
tiquetes
```

Primero se descuenta saldo al usuario y luego se crea el tiquete.

Estas dos acciones deben quedar sincronizadas. No sería correcto descontar saldo sin crear el tiquete, ni crear un tiquete sin descontar el saldo.

Por eso se usa una transacción:

```php
beginTransaction()
commit()
rollBack()
```

La transacción permite que ambas operaciones se comporten como una sola.

Si todo sale bien, se confirman los cambios con `commit()`.

Si ocurre un error, se revierten los cambios con `rollBack()`.

## Uso de `FOR UPDATE`

Al consultar el usuario durante la creación del tiquete, se usa una consulta con:

```sql
FOR UPDATE
```

Esto bloquea temporalmente la fila del usuario mientras dura la transacción.

La razón es evitar problemas si dos apuestas del mismo usuario llegan al mismo tiempo.

Por ejemplo:

```text
Saldo del usuario: 10000

Petición A: apuesta 8000
Petición B: apuesta 8000
```

Sin un bloqueo, ambas peticiones podrían leer el saldo inicial de `10000` y ambas creer que hay saldo suficiente.

Con `FOR UPDATE`, una petición espera a que la otra termine antes de leer y modificar el saldo. Esto ayuda a evitar descuentos incorrectos.

## Endpoint POST para crear tiquetes

El endpoint para crear tiquetes se encuentra en:

```text
public/api/tiquetes.php
```

Este endpoint recibe una petición `POST` con un cuerpo JSON como:

```json
{
  "usuario_id": 1,
  "monto": 5000
}
```

La respuesta exitosa devuelve código HTTP `201`.

Ejemplo de respuesta:

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

## Códigos de respuesta del POST

El endpoint `POST /api/tiquetes` maneja los siguientes códigos:

```text
201 = tiquete creado correctamente
400 = JSON inválido, campos faltantes o monto inválido
404 = usuario no existe
422 = saldo insuficiente
500 = error inesperado del servidor
```

## Endpoint GET para listar tiquetes de un usuario

El enunciado solicita un endpoint tipo:

```text
GET /api/usuarios/{id}/tiquetes
```

Como este proyecto está desarrollado en PHP puro, sin framework ni sistema de rutas, se implementó un endpoint equivalente usando un archivo PHP y un parámetro `id`:

```text
GET /api/usuario_tiquetes.php?id=1
```

Este endpoint retorna los tiquetes asociados al usuario indicado.

Si el usuario no existe, responde con código HTTP `404`.

Ejemplo de respuesta exitosa:

```json
{
  "usuario_id": 1,
  "tiquetes": [
    {
      "id": 18,
      "usuario_id": 1,
      "monto": "5000.00",
      "estado": "pendiente",
      "creado_en": "2026-06-03 18:30:00"
    }
  ]
}
```

## Códigos de respuesta del GET

El endpoint para listar tiquetes maneja los siguientes códigos:

```text
200 = consulta correcta
400 = id de usuario inválido o no enviado
404 = usuario no existe
405 = método no permitido
500 = error inesperado del servidor
```

## Cómo ejecutar el servidor local

Como el proyecto no necesariamente está dentro de `C:\xampp\htdocs`, se puede usar el servidor embebido de PHP.

Desde la raíz del proyecto:

```bash
php -S localhost:8000 -t public
```

La opción `-t public` indica que la carpeta pública del proyecto será `public`.

Mientras este servidor esté activo, la API queda disponible en:

```text
http://localhost:8000
```

## Cómo probar el POST de creación de tiquete

En otra terminal PowerShell, ejecutar:

```powershell
Invoke-RestMethod -Uri "http://localhost:8000/api/tiquetes.php" -Method POST -ContentType "application/json" -Body '{"usuario_id":1,"monto":5000}'
```

Resultado esperado:

```text
mensaje: Tiquete creado correctamente.
estado HTTP: 201
```

También se puede verificar en la respuesta que el saldo fue descontado correctamente:

```text
saldo_anterior: 45000
saldo_actual: 40000
```

Esto demuestra que el monto apostado fue descontado del saldo del usuario.

## Probar usuario inexistente en POST

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

## Probar saldo insuficiente en POST

El usuario `Carlos Ruiz` tiene saldo `0`, por lo que una apuesta debería fallar.

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

## Probar monto inválido en POST

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

## Cómo probar el GET de tiquetes por usuario

```powershell
Invoke-RestMethod -Uri "http://localhost:8000/api/usuario_tiquetes.php?id=1" -Method GET | ConvertTo-Json -Depth 5
```

Resultado esperado:

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

## Probar usuario inexistente en GET

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

## Prueba manual desde consola

También se puede probar la creación de tiquetes directamente desde PHP usando:

```bash
php tests/test_crear_tiquete.php
```

Esta prueba ejecuta directamente el servicio `TiqueteService` sin pasar por HTTP.

Sirve para validar rápidamente la lógica interna de creación del tiquete, descuento de saldo y transacción.

## Decisiones tomadas

* Se separó la lógica de negocio en `TiqueteService.php`.
* Los archivos de `public/api` se encargan principalmente de recibir la petición, validar datos básicos y devolver JSON.
* Se usaron excepciones personalizadas para diferenciar errores como usuario inexistente y saldo insuficiente.
* Se usó una transacción para garantizar que el descuento de saldo y la creación del tiquete se completen juntos.
* Se usó `FOR UPDATE` para evitar problemas cuando dos operaciones intentan modificar el saldo del mismo usuario al mismo tiempo.
* Se usó el servidor embebido de PHP para facilitar la ejecución local sin mover el proyecto a `htdocs`.

## Si tuviera más tiempo...

* Implementaría un router para respetar exactamente la ruta `GET /api/usuarios/{id}/tiquetes`.
* Agregaría pruebas automatizadas para los endpoints.
* Agregaría validaciones más estrictas para los datos recibidos.
* Movería la configuración de base de datos a variables de entorno.
* Estandarizaría mejor el formato de errores JSON.


