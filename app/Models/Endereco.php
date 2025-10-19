<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Endereco extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'rua', 'numero', 'complemento', 'cidade', 'estado', 'cep' // <-- CORRIGIDO AQUI
    ];

    public function user() // <-- CORRIGIDO AQUI (para user)
    {
        // Como agora seguimos a convenção, não precisamos mais passar o segundo parâmetro
        return $this->belongsTo(User::class);
    }
}