<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Order;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'email',
        'password',
        'celular',
        'cpf',
        'is_admin',
        'role',
    ];
    // Dentro da classe User

    public function enderecos()
    {
        // Como agora seguimos a convenção, não precisamos mais passar o segundo parâmetro
        return $this->hasMany(Endereco::class);
    }

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
            'is_admin' => 'boolean', // <-- Adicione esta linha
        ];
    }
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Verifica se o usuário é administrador
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin' || $this->is_admin === true;
    }

    /**
     * Verifica se o usuário é da logística
     */
    public function isLogistica(): bool
    {
        return $this->role === 'logistica';
    }

    /**
     * Verifica se o usuário tem acesso ao painel admin
     */
    public function canAccessAdmin(): bool
    {
        return $this->isAdmin() || $this->isLogistica();
    }
}
