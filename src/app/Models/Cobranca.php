<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cobranca extends Model
{
    protected $fillable = [
        'empresa_id',
        'cliente_id',
        'descricao',
        'tipo',
        'valor_total',
        'numero_parcelas',
        'periodicidade',
    ];

    protected function casts(): array
    {
        return [
            'valor_total' => 'decimal:2',
            'numero_parcelas' => 'integer',
        ];
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function parcelas(): HasMany
    {
        return $this->hasMany(Parcela::class);
    }
}
