# MyFicList - Visión general de la aplicación

## 1. Propósito general
MyFicList es una aplicación web Laravel que funciona como un buscador y biblioteca personal de entretenimiento. Permite buscar y organizar contenido de cinco tipos principales:

- Anime
- Manga
- Novelas ligeras
- Libros
- Películas
- Series de TV
- Videojuegos

La aplicación utiliza APIs externas para buscar datos y guarda en la base de datos local los elementos que el usuario decide agregar a su colección.

---

## 2. Flujo principal de la aplicación

1. `GET /`
   - Muestra la página de inicio en `resources/views/home.blade.php`.
   - Incluye búsqueda de texto y selección de tipo (`anime`, `manga`, `movie`, `series`, `game`).

2. `GET /search`
   - Controlador: `App\Http\Controllers\MediaController@search`.
   - Valida los parámetros `q` y `type`.
   - Llama a `App\Services\SearchService::searchMultiple`.
   - Devuelve la vista `resources/views/media_results.blade.php` con resultados.

3. `POST /media/add-from-search`
   - Controlador: `App\Http\Controllers\MediaController@addFromSearch`.
   - Recibe datos del resultado seleccionado.
   - Si ya existe en la base de datos local, redirige a la ficha.
   - Si no existe, descarga detalles completos de la API correspondiente y guarda un nuevo `Media`.

4. `GET /catalogo/{id}`
   - Controlador: `App\Http\Controllers\MediaController@show`.
   - Muestra el detalle del contenido guardado en `resources/views/media_show.blade.php`.

5. Rutas protegidas para usuarios autenticados (middleware `auth`, `verified`):
   - `GET /dashboard` → vista `resources/views/dashboard.blade.php`
   - `POST /user-list`, `PUT /user-list/{id}`, `DELETE /user-list/{id}` → `App\Http\Controllers\UserListController`

---

## 3. Componentes clave

### 3.1 Controladores

#### `MediaController`
- `search(Request $request)`
  - Busca en varias fuentes y devuelve resultados.
  - Si hay un solo resultado local, redirige directamente a su detalle.

- `show($id)`
  - Obtiene un item `Media` de la base de datos y muestra la ficha.

- `addFromSearch(Request $request)`
  - Agrega un resultado externo a la base de datos local.
  - Selecciona la fuente correcta: `Jikan`, `TMDB`, `RAWG`, o `Local`.

#### `UserListController`
- `store(Request $request)`
  - Valida y guarda el estado del usuario para un `Media`.

- `update(Request $request, $id)`
  - Actualiza estado, puntuación y/o progreso.

- `destroy($id)`
  - Elimina la entrada de la lista del usuario.

### 3.2 Servicio de búsqueda

#### `App\Services\SearchService`
Este servicio es el motor de búsqueda y normalización de datos.

- `searchMultiple(string $query, string $type)`
  - Busca primero en la base local (`Media`).
  - Luego consulta las APIs externas según el tipo:
    - `TMDB` (para `anime`, `movie`, `series`)
    - `Jikan` (para `anime`, `manga`)
    - `RAWG` (para `game`)
  - Combina resultados, elimina duplicados y limita a 10.

- Métodos auxiliares:
  - `searchInDatabase`
  - `searchMultipleInTmdb`
  - `searchMultipleInJikan`
  - `searchMultipleInRawg`
  - `searchInTmdb`
  - `searchInJikan`
  - `translateData`
  - `translateText`

### 3.3 Modelos

#### `App\Models\Media`
- Campos principales:
  - `external_id`
  - `title`
  - `media_type`
  - `source`
  - `cover_url`
  - `synopsis`
  - `extra_data`
- `extra_data` se castea automáticamente a array.
- Relación `userRatings()` hacia `UserList`.
- Atributo virtual `average_score` para obtener la puntuación media.

#### `App\Models\UserList`
- Campos principales:
  - `user_id`
  - `media_id`
  - `status`
  - `progress`
  - `score`
- Relaciones:
  - `media()` → `Media`
  - `user()` → `User`

#### `App\Models\User`
- Relación `userLists()` con `UserList`.

---

## 4. Vistas principales

- `resources/views/home.blade.php`
  - Página de inicio y buscador.

- `resources/views/media_results.blade.php`
  - Muestra los resultados de búsqueda.
  - Permite agregar items a la base local.

- `resources/views/media_show.blade.php`
  - Muestra la ficha detallada de un `Media` guardado.
  - Incluye formulario para añadir el item a la lista personal.

- `resources/views/dashboard.blade.php`
  - Muestra la colección del usuario autenticado.

---

## 5. Flujo de datos y almacenamiento

- Las búsquedas no guardan datos automáticamente.
- Solo se guarda en `media` cuando el usuario agrega un resultado externo.
- La tabla `user_lists` guarda el estado personal de cada usuario sobre cada `Media`.
- Las rutas protegidas aseguran que solo usuarios autenticados gestionen su lista.

---

## 6. Tipos de contenido

- `anime`, `manga`, `movie`, `series`, `game`
- Cada tipo usa la fuente/API adecuada:
  - `anime`/`manga` → `Jikan` y `TMDB`
  - `movie`/`series` → `TMDB`
  - `game` → `RAWG`

---

## 7. Conclusión
MyFicList combina búsqueda multi-fuente con una biblioteca personal. El núcleo es el servicio de búsqueda (`SearchService`), los controladores `MediaController`/`UserListController`, y los modelos `Media`/`UserList`.

El diseño permite que el usuario vea resultados externos, añada solo lo que quiera guardar y luego administre su colección desde su dashboard.
