<footer
    style="background-color: rgb(17, 24, 39) !important; padding-top: 20px !important; padding-bottom: 20px !important; !important;"
    class="">
    <div class="max-w-6xl mx-auto px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-12 mb-10 items-start">
            <!-- Branding -->
            <div class="space-y-6">
                <div class="flex items-center space-x-3">
                    <span
                        class="text-2xl font-black text-white tracking-tighter uppercase leading-none">MyFicList</span>
                </div>
                <p class="text-sm text-gray-400 leading-relaxed max-w-xs">
                    Tu santuario digital para catalogar historias. Diseñado con precisión para los amantes de la ficción
                    en todas sus formas.
                </p>
            </div>

            <!-- Quick Links -->
            <div class="space-y-6 flex flex-col items-center md:items-start md:pl-10">
                <h4 class="text-[10px] font-black text-white uppercase tracking-[0.4em] opacity-40">Navegación</h4>
                <div class="flex flex-col space-y-4">
                    <a href="/" class="text-xs font-bold text-gray-400 hover:text-blue-400 transition-colors">Inicio</a>
                    <a href="{{ route('media.explore') }}"
                        class="text-xs font-bold text-gray-400 hover:text-blue-400 transition-colors">Explorar
                        Contenido</a>
                    <a href="{{ route('dashboard') }}"
                        class="text-xs font-bold text-gray-400 hover:text-blue-400 transition-colors">Fiction top</a>
                    <a href="{{ route('forum.index') }}"
                        class="text-xs font-bold text-gray-400 hover:text-blue-400 transition-colors">Comunidad y
                        foro</a>
                </div>
            </div>

            <!-- Social/External -->
            <div class="space-y-6 flex flex-col items-center md:items-end">
                <h4 class="text-[10px] font-black text-white uppercase tracking-[0.4em] opacity-40">Conecta</h4>
                <div class="flex space-x-4">
                    <a href="#"
                        class="w-12 h-12 bg-gray-800/40 hover:bg-blue-600/20 rounded-xl flex items-center justify-center text-gray-400 hover:text-blue-400 transition-all">
                        <i class="fab fa-twitter text-base"></i>
                    </a>
                    <a href="#"
                        class="w-12 h-12 bg-gray-800/40 hover:bg-purple-600/20 rounded-xl flex items-center justify-center text-gray-400 hover:text-purple-400 transition-all">
                        <i class="fab fa-github text-base"></i>
                    </a>
                    <a href="#"
                        class="w-12 h-12 bg-gray-800/40 hover:bg-indigo-600/20 rounded-xl flex items-center justify-center text-gray-400 hover:text-indigo-400 transition-all">
                        <i class="fab fa-discord text-base"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="pt-10">
            <div class="flex flex-col md:flex-row justify-between items-center gap-2">
                <p class="font-bold text-gray-600 uppercase tracking-[0.4em]" style="font-size: 9px;">
                    &copy; 2026 MyFicList &bull; Experiencia Premium
                </p>
                <div class="font-black text-gray-700 uppercase tracking-widest flex items-center gap-4"
                    style="font-size: 9px;">
                    <span>JIKAN</span>
                    <span class="w-1 h-1 bg-gray-800 rounded-full"></span>
                    <span>TMDB</span>
                    <span class="w-1 h-1 bg-gray-800 rounded-full"></span>
                    <span>RAWG</span>
                </div>
            </div>
        </div>
    </div>
</footer>