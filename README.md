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

## Configuración en un equipo nuevo

1. Extrae el archivo ZIP del proyecto o clona el repositorio:

```bash
# Si usas git
git clone <tu-repositorio> <nombre>
cd <nombre>
```

2. Instala dependencias PHP (requiere Composer):

```bash
composer install
```

3. Instala dependencias JavaScript (requiere Node.js/npm):

```bash
npm install
```

4. Configura el entorno:

Copia el archivo `.env.example` y renómbralo a `.env`:

**En Windows:**
```cmd
copy .env.example .env
```

**En Linux / macOS:**
```bash
cp .env.example .env
```

5. Configura la Base de Datos (SQLite está configurado por defecto para requerir mínima instalación):

**En Windows:**
```cmd
php artisan storage:link
if not exist database\database.sqlite type nul > database\database.sqlite
```

**En Linux / macOS:**
```bash
php artisan storage:link
touch database/database.sqlite
```

6. Genera la clave de aplicación y ejecuta las migraciones:

```bash
php artisan key:generate
php artisan migrate --force
```

7. Compila los assets (Tailwind CSS, Alpine.js, etc.):

```bash
npm run build
```

8. Inicia el servidor local:

```bash
php artisan serve
```

Luego abre `http://127.0.0.1:8000` en tu navegador.

## Desarrollo local

Para trabajar en modo desarrollo con recarga en caliente de los assets:

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

## Notas adicionales

- **Base de Datos Alternativa:** Si prefieres MySQL/MariaDB en lugar de SQLite, edita tu archivo `.env`, cambia `DB_CONNECTION=mysql` y configura las credenciales (Host, Port, Database, Username, Password).
- **Windows / XAMPP:** Asegúrate de que el ejecutable de PHP está en las variables de entorno de tu sistema, o usa la ruta completa al binario (ej. `C:\xampp\php\php.exe artisan serve`). Si `composer install` falla, verifica en tu `php.ini` que extensiones como `zip`, `pdo_sqlite` y `fileinfo` estén habilitadas.
- **Linux:** Asegúrate de tener instaladas las extensiones necesarias (`php-sqlite3`, `php-xml`, `php-zip`, `php-mbstring`, `php-curl`, etc.) según tu distribución.

## Cómo contribuir

1. Crear una rama nueva.
2. Realizar cambios.
3. Hacer un commit claro.
4. Enviar un Pull Request.
