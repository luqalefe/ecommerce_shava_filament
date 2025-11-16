<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'endereco_id',
        'status',
        'total_amount',
        'shipping_cost',
        'shipping_service',
        'payment_method',
        'payment_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function endereco(): BelongsTo
    {
        // Garanta que o Model 'Endereco' existe em 'App\Models\Endereco'
        return $this->belongsTo(Endereco::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}