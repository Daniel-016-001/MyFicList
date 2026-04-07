# Hub App

Aplicación web Laravel basada en Laravel 12, configurada para un entorno local con soporte de consultas, almacenamiento, colas y assets Vite.

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
  - `pdo_sqlite` o `pdo_mysql`
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

1. Clona el repositorio:

```bash
git clone <tu-repositorio> hub-app
cd hub-app
```

2. Instala dependencias PHP:

```bash
composer install
```

3. Instala dependencias JavaScript:

```bash
npm install
```

4. Copia el archivo de entorno:

```bash
copy .env.example .env
```

5. Genera la clave de aplicación:

```bash
php artisan key:generate
```

6. Si usas SQLite, crea el archivo de base de datos:

```bash
php artisan storage:link
if not exist database\database.sqlite type nul > database\database.sqlite
```

7. Ejecuta las migraciones:

```bash
php artisan migrate --force
```

8. Compila los assets:

```bash
npm run build
```

9. Inicia el servidor local:

```bash
php artisan serve
```

Luego abre `http://127.0.0.1:8000`.

## Desarrollo local

Para trabajar en modo desarrollo con recarga en caliente:

```bash
npm run dev
```

## Comandos útiles

- `composer install`
- `composer dump-autoload`
- `npm install`
- `npm run dev`
- `npm run build`
- `php artisan serve`
- `php artisan migrate`
- `php artisan test`
- `php artisan db:seed`

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
