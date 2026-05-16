<article style="{{ $level > 0 ? 'margin-left: ' . min($level * 2, 6) . 'rem; margin-top: 1rem;' : '' }}" 
         class="bg-gray-900/10 p-6 md:p-8 rounded-3xl group transition-all hover:bg-gray-900/20 relative">
    <div class="flex justify-between items-start mb-6">
        <div class="flex items-center gap-4">
            <img src="{{ $comment->user->avatar_url }}" alt="" class="w-10 h-10 md:w-12 md:h-12 rounded-full object-cover shadow-sm">
            <div>
                @if($comment->user)
                    @if($comment->user->username)
                        <a href="{{ route('users.show', $comment->user->username) }}" class="text-sm font-black text-white hover:text-purple-400 transition-colors uppercase tracking-tight">{{ $comment->user->username }}</a>
                    @else
                        <span class="text-sm font-black text-white uppercase tracking-tight">{{ $comment->user->name }}</span>
                    @endif
                @else
                    <span class="font-black text-gray-500 text-sm">Usuario eliminado</span>
                @endif
                <div class="text-[9px] text-gray-600 uppercase tracking-widest font-black mt-0.5">{{ $comment->created_at->diffForHumans() }}</div>
            </div>
        </div>
        
        <div class="flex items-center gap-3">
            @if(auth()->check() && (auth()->id() === $comment->user_id || auth()->user()->role === 'admin'))
                <form action="{{ route('comments.destroy', $comment->id) }}" method="POST" onsubmit="return confirm('¿Seguro que quieres eliminar este comentario?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-gray-600 hover:text-red-500 transition-colors">
                        <i class="fas fa-trash text-xs"></i>
                    </button>
                </form>
            @endif
            <!-- Like Button -->
            <button onclick="toggleLike({{ $comment->id }}, 'comment', this)" 
                    class="flex items-center gap-2 px-3 py-1.5 rounded-xl transition-all {{ auth()->user() && $comment->isLikedBy(auth()->user()) ? 'text-red-500 bg-red-500/10' : 'text-gray-500 bg-white/5 hover:bg-white/10' }}">
                <i class="{{ auth()->user() && $comment->isLikedBy(auth()->user()) ? 'fas' : 'far' }} fa-heart text-xs"></i>
                <span class="like-count font-black text-[10px]">{{ $comment->likes()->count() }}</span>
            </button>
        </div>
    </div>
    <p class="text-gray-400 text-sm leading-relaxed font-medium md:pl-16">{{ $comment->content }}</p>

    <div class="md:pl-16 mt-4 flex items-center gap-4">
        @auth
            <button onclick="toggleReplyForm({{ $comment->id }})" class="text-gray-500 hover:text-blue-400 text-[10px] font-black uppercase tracking-widest transition-colors flex items-center gap-2">
                <i class="fas fa-reply"></i> Responder
            </button>
        @endauth
    </div>

    @auth
        <div id="reply-form-{{ $comment->id }}" class="hidden md:pl-16 mt-6">
            <form action="{{ route('comments.store') }}" method="POST" class="flex gap-4">
                @csrf
                <input type="hidden" name="commentable_type" value="{{ get_class($model) }}">
                <input type="hidden" name="commentable_id" value="{{ $model->id }}">
                <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                <img src="{{ auth()->user()->avatar_url }}" class="w-8 h-8 rounded-full object-cover hidden md:block" alt="">
                <div class="flex-1 space-y-3">
                    <textarea name="content" required placeholder="Escribe tu respuesta..."
                        style="background-color: rgba(0,0,0,0.2) !important;"
                        class="w-full rounded-xl p-3 text-white text-sm focus:ring-0 transition-all placeholder-gray-600"
                        rows="2"></textarea>
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="toggleReplyForm({{ $comment->id }})" class="text-gray-500 hover:text-white text-[10px] font-black uppercase tracking-widest px-4 py-2">
                            Cancelar
                        </button>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white font-black px-6 py-2 rounded-lg transition-all shadow-lg shadow-blue-900/20 uppercase text-[9px] tracking-[0.2em]">
                            Responder
                        </button>
                    </div>
                </div>
            </form>
        </div>
    @endauth
</article>

@if($comment->replies && $comment->replies->count() > 0)
    <div class="space-y-2 mt-2">
        @foreach($comment->replies as $reply)
            @include('components.comment-item', ['comment' => $reply, 'model' => $model, 'level' => $level + 1])
        @endforeach
    </div>
@endif
