<!-- Loading Overlay -->
<div id="search-loading-overlay" class="fixed inset-0 z-[100] hidden bg-gray-950/90 backdrop-blur-md flex flex-col items-center justify-center transition-all duration-300 opacity-0">
    <div class="relative flex items-center justify-center">
        <!-- Glowing rings -->
        <div class="w-32 h-32 border-4 border-purple-500/20 rounded-full animate-pulse absolute"></div>
        <div class="w-24 h-24 border-t-4 border-l-4 border-purple-500 rounded-full animate-spin absolute"></div>
        <div class="w-16 h-16 border-b-4 border-r-4 border-pink-500 rounded-full animate-spin absolute" style="animation-direction: reverse; animation-duration: 1.5s;"></div>
        <!-- Center Icon -->
        <div class="absolute text-3xl">🌌</div>
    </div>
    
    <h2 class="mt-12 text-3xl font-black text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-pink-500 tracking-[0.2em] animate-pulse">
        EXPLORANDO
    </h2>
    <p class="mt-4 text-gray-400 font-medium max-w-sm text-center transition-opacity duration-300" id="loading-text">
        Conectando con bases de datos externas...
    </p>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Encontrar todos los formularios de búsqueda (apuntan a /search o /search/unified)
        const searchForms = document.querySelectorAll('form[action*="/search"]');
        const overlay = document.getElementById('search-loading-overlay');
        const loadingText = document.getElementById('loading-text');
        
        searchForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                // Si está vacío, dejar que HTML5 validación actúe o que el controlador rebote rápido
                const queryInput = form.querySelector('input[name="query"], input[name="q"]');
                if (queryInput && queryInput.value.trim() === '') return;

                // Bloquear el scroll
                document.body.style.overflow = 'hidden';

                // Mostrar el overlay
                overlay.classList.remove('hidden');
                // Forzar un reflow para que la transición funcione
                void overlay.offsetWidth; 
                overlay.classList.remove('opacity-0');
                overlay.classList.add('opacity-100');

                // Cambiar el texto dinámicamente para que la espera sea agradable
                const messages = [
                    "Sincronizando información...",
                    "Traduciendo sinopsis al español...",
                    "Agrupando formatos y duplicados...",
                    "Descargando metadatos...",
                    "¡Casi lo tenemos!"
                ];
                
                let msgIndex = 0;
                setInterval(() => {
                    loadingText.style.opacity = '0';
                    setTimeout(() => {
                        loadingText.textContent = messages[msgIndex % messages.length];
                        loadingText.style.opacity = '1';
                        msgIndex++;
                    }, 300); // 300ms de fundido
                }, 3500); // Cambiar mensaje cada 3.5 segundos
            });
        });
    });
</script>
