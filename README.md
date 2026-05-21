# MyFicList

Aplicación web Laravel (MyFicList) basada en Laravel 12, configurada para realizar un seguimiento de contenidos cinematográficos y de ficción, con integración de APIs (TMDB y RAWG) y una base de datos local SQLite.

## Requisitos mínimos

- PHP `^8.2`
- Composer 2
- Node.js `>=18`
- npm (incluido con Node.js) o Yarn
- Extensiones PHP:
  - `bcmath`
  - `ctype`
  - `fileinfo`
  - `json`
  - `mbstring`
  - `openssl`
  - `pdo`
  - `pdo_sqlite`
  - `tokenizer`
  - `xml`
  - `zip`
- SQLite (para el valor por defecto de `.env`) o MySQL / MariaDB si prefieres otra DB
- Git (recomendado)

> El archivo `requirements.txt` contiene la lista de dependencias y el software requerido para ejecutar esta aplicación.

## Dependencias principales

Estas dependencias se instalan a través de Composer y npm:

- PHP: `^8.2`
- Laravel Framework: `^12.0`
- Guzzle HTTP: `^7.10`
- Laravel Tinker
- Laravel Breeze (dev)
- Laravel Pint (dev)
- PHPUnit (dev)
- Vite + Tailwind CSS
- Alpine.js

## Configuración en un entorno nuevo

Sigue estos pasos para realizar una instalación limpia del proyecto en un equipo nuevo:

1. Extrae la carpeta del archivo comprimido y posicionate sobre la carpeta:

```bash
cd MyFicList
```

2. Instala dependencias PHP:

```bash
composer install
```

3. Instala dependencias JavaScript:

```bash
npm install
```

4. Copia el archivo de configuración de entorno:

En Windows (PowerShell/CMD):
```bash
copy .env.example .env
```

En Linux / macOS:
```bash
cp .env.example .env
```

5. Configura las claves de API en el archivo `.env`. Si no se han incluido automáticamente, asegúrate de que tengan este aspecto:

```env
TMDB_TOKEN=eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiJlOTQ5ZDY4NTY2YjRiODYyNmZlMjU1Mjc0MzlmMzFhMyIsIm5iZiI6MTc3MjcxNjY0OS44NSwic3ViIjoiNjlhOTgyNjlkNWQwNzc1YWRmZWM2MDRiIiwic2NvcGVzIjpbImFwaV9yZWFkIl0sInZlcnNpb24iOjF9.ypXrUf9HXOzpW8rTRmtKdWI4g1zpc-I_uGu0iJRjXVQ
RAWG_KEY=86d2496fd8814790b8068b3056774276
```

6. Genera la clave de aplicación de Laravel:

```bash
php artisan key:generate
```

7. Enlaza el almacenamiento y crea el archivo de base de datos SQLite (configurado por defecto):

Enlazar el storage para las imágenes/avatars:
```bash
php artisan storage:link
```

Crear el archivo SQLite vacío:
* **En Windows (PowerShell):**
  ```powershell
  New-Item -Path database\database.sqlite -ItemType File -Force
  ```
* **En Windows (CMD):**
  ```cmd
  type nul > database\database.sqlite
  ```
* **En Linux / macOS:**
  ```bash
  touch database/database.sqlite
  ```

8. Ejecuta las migraciones e inserta los datos de prueba (Demo):

```bash
php artisan migrate --seed --force
```

*(Esto creará la estructura de tablas y un usuario de prueba con credenciales `demo@myficlist.com` y contraseña `password`, además de algunos títulos de demostración)*.

9. Compila los assets de Vite para producción:

```bash
npm run build
```

10. Inicia el servidor local:

```bash
php artisan serve
```

Luego abre `http://127.0.0.1:8000` en tu navegador para ver la aplicación funcionando.

## Desarrollo local

Si vas a realizar cambios y quieres verlos reflejados en tiempo real (recarga en caliente de assets/Tailwind):

```bash
npm run dev
```

## Comandos útiles

- `composer install` - Instalar dependencias PHP
- `npm install` - Instalar dependencias JavaScript
- `npm run dev` - Ejecutar servidor de desarrollo Vite
- `npm run build` - Compilar assets para producción
- `php artisan serve` - Servidor local de desarrollo Laravel
- `php artisan migrate:fresh --seed` - Reiniciar base de datos e insertar datos semilla
- `php artisan test` - Ejecutar tests unitarios y de integración

## Ajustes de base de datos

Por defecto el proyecto usa SQLite con la variable `DB_CONNECTION=sqlite`. Si prefieres MySQL/MariaDB, actualiza en `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nombre_de_base_de_datos
DB_USERNAME=usuario
DB_PASSWORD=contraseña
```

## Notas para Windows / XAMPP

- Asegúrate de usar el PHP de XAMPP si no tienes PHP global instalado.
- Si `composer install` falla por extensiones, instala las extensiones listadas arriba y reinicia Apache / el servicio PHP.
- Para ejecutar el servidor interno de Laravel, usa `php artisan serve` desde la carpeta del proyecto.

## Cómo contribuir

1. Crear una rama nueva.
2. Realizar cambios.
3. Hacer un commit claro.
4. Enviar un Pull Request.
