# 🎬 MyFicList — Plataforma Cinéfila e Interactiva de Medios

[![Laravel v12](https://img.shields.io/badge/Laravel-v12.0-FF2D20?style=for-the-badge&logo=laravel)](https://laravel.com)
[![Tailwind CSS v4](https://img.shields.io/badge/Tailwind_CSS-v4.0-38BDF8?style=for-the-badge&logo=tailwind-css)](https://tailwindcss.com)
[![SQLite](https://img.shields.io/badge/SQLite-predeterminado-003B57?style=for-the-badge&logo=sqlite)](https://sqlite.org)
[![Vite](https://img.shields.io/badge/Vite-compilador-646CFF?style=for-the-badge&logo=vite)](https://vitejs.dev)

**MyFicList** es una aplicación web moderna diseñada como un buscador unificado, biblioteca de catalogación personal y red social interactiva orientada al sector del entretenimiento. Los usuarios pueden explorar, puntuar, reseñar y organizar obras pertenecientes a seis categorías fundamentales: **Películas**, **Series de TV**, **Anime**, **Manga**, **Videojuegos** y **Libros**.

El proyecto ha sido desarrollado bajo estrictos estándares de ingeniería de software para cumplir con los requerimientos académicos de un **Trabajo de Fin de Grado (TFG)**, priorizando el rendimiento, la experiencia de usuario y una arquitectura limpia y mantenible.

---

## 🚀 Guía de Despliegue Local (Desde Archivo ZIP)

Siga estas instrucciones paso a paso para realizar una instalación limpia del proyecto en cualquier máquina a partir del archivo comprimido.

### 📋 Requisitos Previos Mínimos
Asegúrese de tener instalados los siguientes componentes globales en su sistema:
*   **PHP**: `^8.2` (con extensiones habilitadas: `pdo`, `pdo_sqlite`, `mbstring`, `openssl`, `xml`, `zip`)
*   **Composer**: `v2.x` (gestor de dependencias PHP)
*   **Node.js**: `>=18.x` (con gestor de paquetes `npm`)
*   **Servidor web local** (o simplemente el CLI de PHP para ejecutar el servidor integrado de Laravel)

---

### 🔧 Proceso de Instalación

#### 1. Extraer y Posicionarse en el Directorio
Extraiga el contenido del archivo `.zip` en la ubicación de su preferencia y abra una terminal en la carpeta raíz del proyecto:
```bash
cd MyFicList
```

#### 2. Instalar Dependencias de Backend (PHP)
Descargue e instale de forma segura las dependencias del framework mediante Composer:
```bash
composer install
```

#### 3. Instalar Dependencias de Frontend (Javascript)
Descargue los módulos de Node necesarios para compilar la interfaz de usuario:
```bash
npm install
```

#### 4. Configurar el Entorno Local (`.env`)
Copie el archivo de plantilla para generar su configuración local. 

*   **En Windows (CMD / PowerShell):**
    ```bash
    copy .env.example .env
    ```
*   **En Linux / macOS:**
    ```bash
    cp .env.example .env
    ```

> 💡 **Nota sobre las APIs:** El archivo `.env.example` ya incluye las claves de desarrollo preestablecidas para las APIs de **TMDB** y **RAWG**, facilitando un despliegue inmediato sin necesidad de registros externos.

#### 5. Generar la Clave de Seguridad de Laravel
Establezca la firma criptográfica única de la sesión de la aplicación:
```bash
php artisan key:generate
```

#### 6. Crear la Base de Datos SQLite (Base de Datos por Defecto)
Para lograr un despliegue ágil sin dependencias de motores complejos de base de datos como MySQL, el proyecto está configurado para utilizar **SQLite**.

Cree el archivo de base de datos vacío según su sistema operativo:
*   **En Windows (PowerShell):**
    ```powershell
    New-Item -Path database\database.sqlite -ItemType File -Force
    ```
*   **En Windows (CMD):**
    ```cmd
    type nul > database\database.sqlite
    ```
*   **En Linux / macOS:**
    ```bash
    touch database/database.sqlite
    ```

Asimismo, configure el enlace simbólico del almacenamiento público para los archivos multimedia/avatars:
```bash
php artisan storage:link
```

#### 7. Ejecutar Migraciones y Datos Semilla (Seeds Demo)
Ejecute las migraciones para crear la estructura de tablas e inyecte los datos de demostración para evaluar la aplicación al instante:
```bash
php artisan migrate --seed --force
```

#### 8. Compilar los Assets con Vite
Realice la compilación optimizada y empaquetamiento del frontend para producción:
```bash
npm run build
```

#### 9. Iniciar el Servidor de Laravel
Ejecute el servidor de desarrollo integrado de PHP:
```bash
php artisan serve
```

Acceda a la aplicación abriendo la siguiente URL en su navegador web preferido:
👉 **[http://127.0.0.1:8000](http://127.0.0.1:8000)**

---

## 🎓 Información Exclusiva para el Tribunal / Jurado

Para facilitar una evaluación exhaustiva y dinámica del proyecto, se ha precargado un escenario de demostración durante la fase de inyección de semillas (paso 7):

### 🔑 Credenciales de Acceso Demo
Utilice estas credenciales para iniciar sesión y explorar el panel de administración, su colección privada y las funciones sociales:
*   **Usuario:** `demo@myficlist.com`
*   **Contraseña:** `password`

### 🔍 Puntos Clave de Valoración Técnica
Durante la navegación, le recomendamos prestar especial atención a los siguientes hitos de ingeniería:
1.  **Buscador Inteligente Multifuente:** Búsquedas concurrentes unificadas. Se realizan consultas ligeras e instantáneas para una renderización fluida del listado.
2.  **Carga Diferida (*Lazy Details Import*):** Los detalles pesados (como listados de episodios, sinopsis traducidas, duraciones, etc.) se importan a la base de datos local de manera diferida, únicamente cuando el usuario accede a la ficha detallada.
3.  **Modelo de Datos Polimórfico:** Comentarios anidados en cascada (respuestas infinitas) y sistema de reacciones "Me gusta" unificados polimórficamente bajo los mismos modelos para servir de forma uniforme a obras, posts de foro y listas personalizadas.
4.  **Red Social de Comunidad:** Posibilidad de seguir a otros usuarios de la comunidad, revisar sus colecciones, participar en el foro temático (`/foro`) con soporte para adjuntar archivos y crear listas personalizadas públicas o privadas.

### 📧 Cómo Probar el Envío de Emails en Local (Verificación y Contraseñas)
Para evitar que tenga que configurar un servidor SMTP real (como Gmail o SendGrid), la aplicación está preconfigurada en modo **Log** para el correo electrónico (`MAIL_MAILER=log` en el `.env`). 

Esto significa que todos los correos electrónicos se escriben en texto plano en el archivo de registro local: **`storage/logs/laravel.log`**.

#### A. Para probar la Verificación de Email:
1. Regístrese con un correo nuevo en la página de registro.
2. La aplicación le mostrará la pantalla de espera de verificación de email.
3. Abra el archivo **`storage/logs/laravel.log`** en su editor.
4. Al final del archivo verá el email de verificación generado. Copie el enlace que aparece (ej. `http://127.0.0.1:8000/verify-email/...`) y péguelo en su navegador para verificar la cuenta.

#### B. Para probar el Restablecimiento de Contraseña:
1. En la pantalla de Login, haga clic en **"Forgot your password?"**.
2. Introduzca el correo electrónico de una cuenta registrada y envíe la solicitud.
3. Abra el archivo **`storage/logs/laravel.log`** en su editor.
4. Al final del archivo verá el correo con el enlace de restablecimiento. Copie el enlace generado (ej. `http://127.0.0.1:8000/reset-password/...`), péguelo en su navegador y elija su nueva contraseña.

---

## 🛠️ Desarrollo Activo
Si desea realizar cambios en el código o estilos y ver las actualizaciones en tiempo real con recarga en caliente de Tailwind CSS v4, ejecute el compilador en modo desarrollo:
```bash
npm run dev
```

---

## 📂 Estructura Principal del Proyecto

*   `app/Models/` — Modelos de Eloquent con relaciones complejas y polimórficas.
*   `app/Services/SearchService.php` — Lógica central de búsqueda cruzada y normalización.
*   `app/Http/Controllers/` — Controladores de las funciones de comunidad, catálogo y listas.
*   `database/migrations/` — Historial de estructuración de tablas.
*   `resources/views/` — Vistas optimizadas escritas en HTML y Blade con Tailwind CSS.
*   `routes/web.php` — Declaración de endpoints públicos y protegidos.
