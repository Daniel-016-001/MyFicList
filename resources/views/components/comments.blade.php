@props(['model'])

<section class="pt-8 space-y-8">
    <div class="flex items-center justify-between pb-4">
        <h3 class="text-xs font-black text-gray-500 uppercase tracking-[0.4em]">Comunidad</h3>
        <div class="text-[9px] font-black text-gray-600 uppercase tracking-[0.2em]">{{ $model->comments()->count() }} COMENTARIOS</div>
    </div>

    @auth
        <div class="py-8">
            <div class="flex gap-6">
                <img src="{{ auth()->user()->avatar_url }}" class="w-12 h-12 rounded-2xl object-cover" alt="">
                <form action="{{ route('comments.store') }}" method="POST" class="flex-1 space-y-4">
                    @csrf
                    <input type="hidden" name="commentable_type" value="{{ get_class($model) }}">
                    <input type="hidden" name="commentable_id" value="{{ $model->id }}">
                    <textarea name="content" required placeholder="Añadir un comentario..."
                        style="background-color: transparent !important;"
                        class="w-full rounded-2xl p-4 text-white text-sm focus:ring-0 transition-all placeholder-gray-600"
                        rows="3"></textarea>
                    <div class="flex justify-end">
                        <button type="submit"
                            class="bg-gradient-to-r from-blue-600 to-purple-600 text-white font-black px-5 py-2.5 rounded-xl transition-all hover:scale-[1.02] shadow-lg shadow-purple-900/20 uppercase text-[9px] tracking-[0.2em]">
                            Publicar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @else
        <div class="bg-blue-900/10 rounded-2xl p-8 text-center my-8">
            <p class="text-gray-300 font-medium text-sm">Inicia sesión para participar en la discusión.</p>
            <a href="{{ route('login') }}" class="inline-block mt-4 text-blue-400 hover:text-blue-300 font-bold uppercase tracking-widest text-[10px]">INICIAR SESIÓN</a>
        </div>
    @endauth

    <div class="space-y-6">
        @forelse($model->comments()->whereNull('parent_id')->with(['user', 'likes', 'replies'])->latest()->get() as $comment)
            @include('components.comment-item', ['comment' => $comment, 'model' => $model, 'level' => 0])
        @empty
        @endforelse
    </div>
</section>

<script>
    function toggleReplyForm(commentId) {
        const form = document.getElementById('reply-form-' + commentId);
        if (form.classList.contains('hidden')) {
            form.classList.remove('hidden');
            form.querySelector('textarea').focus();
        } else {
            form.classList.add('hidden');
        }
    }
</script>
