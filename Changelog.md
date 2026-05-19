# 🛤️ Registro de Cambios (Changelog) y Ruta (Roadmap)

Todos los cambios notables de este proyecto se documentarán en este archivo.

## [En Progreso / Futuro Roadmap]
- Agregar mostrador para cada categoría con los contenidos más populares
- Fix: Titulo cortado
- Fix: Detalles de los videojuegos
- Fix: Comentarios
---

## [1.2.0]
### Añadido (Added)
- **Foro de la Comunidad**: Nueva sección para que los usuarios puedan conversar libremente, publicar contenido y debatir.
- **Gestión de Imágenes con AWS S3**: Integración con Amazon S3 para almacenar avatares de usuario y las imágenes de las publicaciones del foro.
- **Optimización Automática de Imágenes**: Se implementó Intervention Image v3 para redimensionar avatares (400x400) e imágenes del foro (máx. 1200px de ancho y compresión JPEG al 75%) antes de subirlas.
- **Sistema de Roles (RBAC)**: Evolución del esquema de usuarios para soportar roles y asegurar que solo el autor original o un administrador puedan eliminar posts del foro.
- **Limpieza de Almacenamiento**: Eliminación automática de las imágenes alojadas en S3 al borrar un post en el foro.

### Mejorado (Changed)
- **Preparación de Entorno Universal**: Se ha simplificado la configuración local configurando **SQLite** como base de datos por defecto en `.env` y el archivo `requirements.txt`.
- Documentación del `README.md` adaptada para que pueda configurarse rápidamente en entornos Windows y Linux.

---

## [1.1.0]
### Añadido (Added)
- **Buscador Unificado**: Ahora puedes buscar sin especificar tipo. El sistema busca en todas las fuentes (Jikan, TMDB, RAWG) y muestra resultados combinados.
- **Filtro Anti-NSFW**: Se implementó filtro de contenido para excluir resultados no apropiados en las búsquedas.
- **Página de Detalles con Comentarios**: Cada contenido ahora muestra página individual con información completa y sistema de comentarios de usuarios.
- **Dashboard Separado por Categorías**: El dashboard ahora muestra secciones diferenciadas para Anime, Manga, Películas, Series y Videojuegos.
- **API para Libros y Novelas**: Integración con Open Library API para buscar y agregar libros y novelas a tu colección.

### Mejorado (Changed)
- El buscador ahora permite búsqueda libre sin necesidad de seleccionar tipo específico
- La página de detalles (`media_show`) ahora incluye sección de comentarios
- El dashboard muestra filtros por categoría para mejor navegación

### Arreglado (Fixed)
- Mejoras en el rendimiento de búsquedas múltiples

---

## [1.0.0]
### Añadido (Added)
- Integración completa con la API de Jikan para anime y manga, TMDB para series y películas, y RAWG para videojuegos.
- Se ha incorporado un traductor para traducir la API de Jikan
- Buscador inteligente con sugerencias



### Mejorado (Changed)

### Arreglado (Fixed)