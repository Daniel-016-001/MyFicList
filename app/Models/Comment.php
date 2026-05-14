<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Comment extends Model
{
    protected $fillable = [
        'user_id',
        'media_id',          // legacy, nullable tras migración polimórfica
        'commentable_type',  // App\Models\Media | ForumPost | MediaList
        'commentable_id',
        'content',
        'parent_id',
    ];

    /**
     * Usuario que creó el comentario
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Media relacionada al comentario
     */
    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }

    /**
     * Relación polimórfica: a qué objeto pertenece este comentario
     */
    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Comentarios anidados (respuestas)
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id')->with(['user', 'likes', 'replies'])->latest();
    }

    /**
     * Comentario padre (si es respuesta)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function isLikedBy(User $user): bool
    {
        return $this->likes()->where('user_id', $user->id)->exists();
    }
}
