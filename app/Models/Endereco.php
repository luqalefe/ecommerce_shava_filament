<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Endereco extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'rua', 'numero', 'complemento', 'bairro', 'cidade', 'estado', 'cep'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFullAddressAttribute(): string
    {
        return "{$this->rua}, {$this->numero}" .
               ($this->complemento ? " - {$this->complemento}" : "") .
               ($this->bairro ? " - {$this->bairro}" : "") .
               " - {$this->cidade}/{$this->estado} - CEP: {$this->cep}";
    }
}