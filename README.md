# Trabajo Práctico Especial. Web 2

## Integrantes:

* Coria, Agustín Ariel.
* Álvarez, Lautaro.

## Descripción

En este proyecto se desarrolla una API para administrar el stock de chacinados, pudiendo listar todos los productos disponibles, obtener un producto en concreto, agregar, editar o eliminar productos, etc.

## Deploy

La base de datos se desplegará automáticamente (en caso contrario, importar la base de datos chacinados.sql).

## Documentación de la API

### Endpoints 
|       Request         | Método |      Endpoint        | Status Success |  Status Error    |
|-----------------------|--------|----------------------|----------------|------------------|
| Listar productos      |  GET   | /api/productos       |       200      |  404             |
| Obtener producto      |  GET   | /api/productos/:id   |       200      |  400/404         |
| Agregar producto      |  POST  | /api/productos       |       201      |  400/401/404/500 |
| Editar producto       |  PUT   | /api/productos/:id   |       201      |  400/401/404     |
| Obtener token         |  GET   | /api/usuarios/token  |       200      |  400             |

---

### Listar productos (GET)

Esta endpoint permite obtener una lista de los productos disponibles, con opciones para filtrar, ordenar y paginar los resultados.

```http
GET /api/productos
```

**Filtrado**:  
Se pueden filtrar los resultados por cualquiera de los campos `id_producto`,`nombre_producto`, `peso`, `precio` o `id_marca`. En el parámetro `filterBy` se debe especificar el campo y en `filterValue` el valor a buscar. En caso de no especificar el parámetro `filterBy` correctamente, se devolverá la lista de productos filtrados por nombre.

***Ejemplo de filtrado***:  
Obtiene todos los productos cuyo peso sea igual a 200:
```http
GET /api/productos?filterBy=peso&filterValue=200
 ```

**Ordenamiento**:  
Se pueden ordenar los resultados por cualquiera de los campos `nombre_producto`, `peso`, `precio` o `id_marca` de forma ascendente (`ASC`) o descendente (`DESC)`. En caso de no especificar el parámetro `orderBy` se devuelve la lista de productos ordenados por id. En caso de no especificar el parámetro `orderValue` la lista se ordenará en orden ascendente. 
  
***Ejemplo de ordenamiento***:  
Obtiene todos los productos, ordenados por precio en orden descendente:
  ```http
  GET /api/productos?orderBy=precio&orderValue=DESC
  ```


**Paginación**:  
Se puede limitar la cantidad de resultados por página a un número específico, además de seleccionar la página deseada. En el parámetro `page` se debe especificar la página deseada y en `limit` el límite máximo de elementos por página.

**Ejemplo de paginación**:  
Obtiene todos los productos de la página 2 con límite a 5 productos por página:
```http
GET /api/productos?page=2&limit=5
```

---

### Obtener producto (GET)

Devuelve un producto específico mediante su `id`.

```http
GET /api/productos/:id
```
**Ejemplo de request**:
Obtiene el producto con el `id_producto`: 14;
```http
GET /api/productos/14
```

---

### Agregar un producto (POST)
⚠️ Requiere autenticación.

Inserta un nuevo producto con la información proporcionada en el cuerpo de la solicitud en formato JSON. 

```http
POST /api/productos
```

**Ejemplo de body de request**:


```json
{
    "nombre_producto": "Jamón crudo ibérico",
    "peso": 500,
    "precio": 11111,
    "id_marca": 1
}
```
> **El campo `imagen_producto` es opcional y se puede omitir, tal como se muestra en el ejemplo**
  
---

### Editar un producto (PUT)
⚠️ Requiere autenticación.

Modifica un producto seleccionado con la información proporcionada en el cuerpo de la solicitud en formato JSON.
```http
PUT /api/productos/:id
```

**Ejemplo de request**:
```http
PUT /api/productos/14
```
**body**:
```json
{
    "nombre_producto": "Bondiola ahumada",
    "peso": 200,
    "precio": 7890,
    "id_marca": 2,
  }
```

---

### Eliminar un producto (DELETE)
⚠️ Requiere autenticación.

Elimina un producto específico mediante su `id`.
```http
DELETE /api/productos/:id
```

**Ejemplo de request**:
```http
DELETE /api/productos/14
```

---

### Autenticar (GET)

Para acceder a ciertas funcionalidades, se debe autenticar utilizando un token.
```http
GET /api/usuarios/token
```

**Credenciales Basic Auth**

- ***Nombre de usuario:*** `webadmin`
- ***Password:*** `admin`

Se devolverá un token que puede ser utilizado para la autenticación de futuras solicitudes a la API (POST, PUT o DELETE).
