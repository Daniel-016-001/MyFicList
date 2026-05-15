<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    // Definimos qué campos se pueden llenar masivamente
    protected $fillable = [
        'external_id',
        'title',
        'media_type',
        'source',
        'cover_url',
        'synopsis',
        'episodes_count',
        'episode_duration',
        'total_duration',
        'extra_data'
    ];

    // Como extra_data es un JSON en la BD, le decimos a Laravel 
    // que lo convierta automáticamente en un array de PHP al leerlo
    protected $casts = [
        'extra_data' => 'array',
    ];

    // app/Models/Media.php

    public function userRatings()
    {
        // Esto asume que tienes la tabla user_lists que hicimos en el script SQL
        return $this->hasMany(UserList::class, 'media_id');
    }

    // Creamos un "Atributo Virtual" para la nota media
    public function getAverageScoreAttribute()
    {
        $average = $this->userRatings()->avg('score');
        return $average === null ? 'N/A' : number_format($average, 1);
    }

    public function getAvgScoreAttribute()
    {
        return $this->average_score;
    }

    // app/Models/Media.php

    public function userLists()
    {
        // Una obra (Media) aparece en muchas listas de usuarios
        return $this->hasMany(UserList::class);
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}