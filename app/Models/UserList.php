<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserList extends Model
{
    protected $table = 'user_lists';

    protected $fillable = [
        'user_id',
        'media_id',
        'media_list_id',
        'status',
        'progress',
        'score'
    ];

    // Relación: Una entrada de la lista pertenece a un Media (Anime/Peli/Juego)
    public function media()
    {
        return $this->belongsTo(Media::class);
    }

    // Relación: Una entrada de la lista pertenece a un Usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mediaList()
    {
        return $this->belongsTo(MediaList::class, 'media_list_id');
    }
}