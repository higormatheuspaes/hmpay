<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsumoMensagensMes extends Model
{
    protected $fillable = [
        'empresa_id',
        'ciclo_referencia',
        'mensagens_enviadas',
        'mensagens_excedentes',
        'valor_excedente_acumulado',
        'teto_gasto_excedente',
        'envios_pausados',
    ];

    protected function casts(): array
    {
        return [
            'ciclo_referencia' => 'date',
            'mensagens_enviadas' => 'integer',
            'mensagens_excedentes' => 'integer',
            'valor_excedente_acumulado' => 'decimal:2',
            'teto_gasto_excedente' => 'decimal:2',
            'envios_pausados' => 'boolean',
        ];
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }
}
