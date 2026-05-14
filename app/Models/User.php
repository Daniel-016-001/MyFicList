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

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($user) {
            if (empty($user->username)) {
                $user->username = strtolower(str_replace(' ', '', $user->name)) . rand(100, 999);
            }
        });
    }

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

    public function forumPosts()
    {
        return $this->hasMany(ForumPost::class);
    }

    public function media()
    {
        return $this->belongsToMany(Media::class, 'user_lists')
            ->withPivot('status', 'score', 'progress')
            ->withTimestamps();
    }

    /**
     * Accesor para obtener la URL del avatar
     */
    public function getAvatarUrlAttribute($value)
    {
        if ($value) {
            // Si ya es una URL completa, devolverla
            if (filter_var($value, FILTER_VALIDATE_URL)) return $value;
            
            // Si empieza por storage/, quitarlo para que asset('storage/...') no lo duplique
            $path = ltrim($value, '/');
            if (str_starts_with($path, 'storage/')) {
                $path = substr($path, 8);
            }
            
            return asset('storage/' . $path);
        }

        $name = urlencode($this->username ?: $this->name ?: 'User');
        return "https://ui-avatars.com/api/?name={$name}&color=7F9CF5&background=EBF4FF";
    }
}