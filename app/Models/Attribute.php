<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attribute extends Model
{
    use HasFactory;

    /**
     * Os atributos que podem ser preenchidos em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
    ];

    /**
     * Define a relação de que um atributo tem muitos valores de atributo.
     * Ex: 'Cor' tem os valores 'Vermelho', 'Azul', 'Verde'.
     */
    public function values(): HasMany
    {
        return $this->hasMany(AttributeValue::class);
    }
}