<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username', // <-- Añade esto si quieres URLs tipo /u/nombreusuario
        'email',
        'password',
        'avatar_url', // <-- Útil para S3 en AWS
        'bio',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getRouteKeyName()
    {
        return 'username';
    }

    /**
     * Relación: Un usuario tiene muchas entradas en su lista
     */
    public function userLists()
    {
        return $this->hasMany(UserList::class);
    }

    public function mediaLists()
    {
        return $this->hasMany(MediaList::class);
    }

    /**
     * Relación: Comentarios realizados por el usuario
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // En app/Models/User.php

    public function following()
    {
        // Relación muchos a muchos: (Modelo, tabla_pivote, fk_que_sigue, fk_seguido)
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'followed_id');
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'followers', 'followed_id', 'follower_id');
    }

    public function media()
    {
        return $this->belongsToMany(Media::class, 'user_lists')
            ->withPivot('status', 'score', 'progress')
            ->withTimestamps();
    }
}