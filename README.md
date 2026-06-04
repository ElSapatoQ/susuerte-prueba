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


# Parte 2: Base de datos y consultas SQL

## Objetivo

En esta parte se diseñó una base de datos sencilla para manejar usuarios y tiquetes de apuestas.

El objetivo principal fue cubrir los siguientes puntos del enunciado:

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
monto_apostado
estado
es_ganador
creado_en
```

El campo `usuario_id` permite relacionar cada tiquete con un usuario.

Para marcar si un tiquete fue ganador, se agregó el campo:

```sql
es_ganador TINYINT(1)
```

En este proyecto se interpreta así:

```text
0 = no ganador
1 = ganador
```

También se dejó el campo `estado`, que permite representar estados como:

```text
PENDIENTE
GANADOR
PERDEDOR
ANULADO
```

## Clave foránea

La relación entre usuarios y tiquetes se definió con una clave foránea desde `tiquetes.usuario_id` hacia `usuarios.id`.

La idea es garantizar que no se pueda crear un tiquete para un usuario que no existe.

Fragmento representativo:

```sql
FOREIGN KEY (usuario_id)
REFERENCES usuarios(id)
```

Esto ayuda a mantener la integridad de los datos.

Por ejemplo, si se intenta crear un tiquete con un `usuario_id` inexistente, la base de datos no debería permitirlo.

## Índices utilizados

Se agregaron índices para mejorar consultas comunes sobre las tablas.

En la tabla `usuarios` se agregó un índice sobre el nombre:

```sql
INDEX idx_usuarios_nombre (nombre)
```

En la tabla `tiquetes` se agregaron índices sobre:

```sql
usuario_id
es_ganador
usuario_id, es_ganador
```

Estos índices ayudan especialmente en consultas donde se necesita buscar tiquetes por usuario o filtrar tiquetes ganadores.

El índice combinado:

```sql
INDEX idx_tiquetes_usuario_ganador (usuario_id, es_ganador)
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
2. Filtrar solo los tiquetes ganadores.
3. Sumar el monto apostado por cada usuario.
4. Ordenar de mayor a menor.
5. Tomar solo los primeros 3 resultados.

Fragmento principal:

```sql
SUM(t.monto_apostado) AS total_apostado_ganador
```

Este fragmento suma el dinero apostado en los tiquetes ganadores.

También se usa:

```sql
WHERE t.es_ganador = 1
```

para tener en cuenta únicamente los tiquetes marcados como ganadores.

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
* Se agregó `es_ganador` para facilitar la consulta de tiquetes ganadores.
* Se agregaron índices sobre campos usados en relaciones y filtros.
* Se creó un archivo `seed.sql` para poder probar las consultas de forma rápida.
* Se separaron las consultas del enunciado en `queries.sql` para que sean fáciles de revisar.

## Si tuviera más tiempo...

* Agregaría más restricciones a nivel de base de datos, por ejemplo validar que el monto apostado sea mayor que cero.
* Evaluaría usar un catálogo de estados para evitar escribir estados manualmente como texto.
* Agregaría pruebas automatizadas para validar las consultas.
* Revisaría los índices con datos más grandes usando herramientas como `EXPLAIN`.
* Movería la configuración de base de datos a variables de entorno.

