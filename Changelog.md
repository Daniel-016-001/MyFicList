# 🛤️ Registro de Cambios (Changelog) y Ruta (Roadmap)

Todos los cambios notables de este proyecto se documentarán en este archivo. El desarrollo de MyFicList sigue un enfoque ágil centrado en el rendimiento y la experiencia de comunidad.

## [1.2.0] - 2026-05-21
### Añadido (Added)
- **Base de Datos SQLite Predeterminada**: Configuración por defecto de la aplicación orientada a SQLite (`database/database.sqlite`), simplificando drásticamente el proceso de instalación en nuevos entornos y eliminando la dependencia compleja de servidores MySQL locales en desarrollo.
- **Foro de Comunidad Integrado (`/foro`)**: Espacio comunitario interactivo donde los usuarios pueden abrir hilos de debate, categorizar temas de conversación y adjuntar archivos de soporte.
- **Listas Personalizadas con Control de Privacidad**: Los usuarios ahora pueden crear colecciones temáticas propias y configurar su visibilidad como públicas o privadas. Las listas públicas se integran orgánicamente con el resto de la comunidad.
- **Comentarios Polimórficos Anidados**: Rediseño integral de la arquitectura de comentarios. Un único modelo `Comment` se acopla polimórficamente a fichas de obras (`Media`), publicaciones de foro (`ForumPost`) y listas personalizadas (`MediaList`). Soporta respuestas infinitas en cascada gracias a su autorreferenciación.
- **Sistema de Likes Polimórficos (`Like.php`)**: Posibilidad de reaccionar con "Me gusta" a publicaciones de foro, comentarios y listas temáticas.
- **Red de Seguidores**: Interacción social bidireccional que permite a los usuarios seguir y ser seguidos por otros miembros de la comunidad, visualizando perfiles en `/u/{username}`.
- **Sección de los Más Populares (`/top`)**: Módulo gestionado por `PopularMediaController` que analiza e ilustra los rankings con contenidos mejor puntuados organizados por categorías.
- **Carga Diferida y Scroll Infinito en Explorar (`/explorar`)**: Paginación robusta en la grilla de exploración que descarta duplicidades y carga dinámicamente según scroll, mejorando de forma notable el rendimiento del navegador.

### Mejorado (Changed)
- **Estandarización de Categorías**: Homogeneización interna de tipos de medios en la lógica del backend, tablas y vistas para usar las convenciones `peli` y `serie` en sustitución de los antiguos términos ingleses `movie` y `series`.
- **Desacoplamiento de Búsqueda y Detalle (Rendimiento)**: Las búsquedas en APIs externas son ahora extremadamente livianas y veloces. La descarga de metadatos pesados (listados de episodios, sinopsis largas traducidas, duraciones detalladas) se realiza de forma diferida (*lazy loading*) solo cuando el usuario accede explícitamente al detalle de la ficha (`/details`).
- **Gestión Aislada de Avatares**: Se dividió la actualización de datos personales y la subida de avatares en formularios web independientes para una experiencia de usuario limpia.

### Arreglado (Fixed)
- **Estabilización de Fichas de Videojuegos**: Solucionados los problemas de renderización en detalles específicos consumidos desde RAWG y trailers asociados.
- **Duplicidades en Búsquedas Combinadas**: Filtrado en `SearchService` para omitir entradas duplicadas al mezclar fuentes externas con base de datos local.

---

## [1.1.0] - 2026-04-26
### Añadido (Added)
- **Buscador Unificado**: Ahora puedes buscar sin especificar tipo. El sistema busca en todas las fuentes (Jikan, TMDB, RAWG) y muestra resultados combinados.
- **Filtro Anti-NSFW**: Se implementó filtro de contenido para excluir resultados no apropiados en las búsquedas.
- **Página de Detalles con Comentarios**: Cada contenido ahora muestra página individual con información completa y sistema de comentarios de usuarios.
- **Dashboard Separado por Categorías**: El dashboard ahora muestra secciones diferenciadas para Anime, Manga, Películas, Series y Videojuegos.
- **API para Libros y Novelas**: Integración con Open Library API para buscar y agregar libros y novelas a tu colección.

### Mejorado (Changed)
- El buscador ahora permite búsqueda libre sin necesidad de seleccionar tipo específico.
- La página de detalles (`media_show`) ahora incluye sección de comentarios básica.
- El dashboard muestra filtros por categoría para mejor navegación.

### Arreglado (Fixed)
- Mejoras en el rendimiento de búsquedas múltiples.

---

## [1.0.0] - 2026-03-19
### Añadido (Added)
- Integración completa con la API de Jikan para anime y manga, TMDB para series y películas, y RAWG para videojuegos.
- Se ha incorporado un traductor para traducir la API de Jikan.
- Buscador inteligente con sugerencias en tiempo real.