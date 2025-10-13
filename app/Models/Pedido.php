<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    protected $fillable = [
        'usuario_id', 'endereco_id', 'valor_total', 'status', 'metodo_pagamento', 'status_pagamento'
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function endereco()
    {
        return $this->belongsTo(Endereco::class, 'endereco_id');
    }

    public function produtos()
    {
        return $this->belongsToMany(Product::class, 'pedido_produto', 'pedido_id', 'produto_id')
                    ->withPivot('quantidade', 'preco')
                    ->withTimestamps();
    }
}