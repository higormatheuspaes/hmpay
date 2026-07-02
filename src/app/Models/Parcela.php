<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Parcela extends Model
{
    protected $fillable = [
        'cobranca_id',
        'numero',
        'valor',
        'vencimento',
        'origem',
        'status',
        'id_externo_gateway',
        'anexo_path',
        'data_pagamento',
        'codigo_boleto',
    ];

    protected function casts(): array
    {
        return [
            'valor' => 'decimal:2',
            'vencimento' => 'date',
            'data_pagamento' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::updating(function (Parcela $parcela) {
            if ($parcela->isDirty('status')) {
                HistoricoStatusParcela::create([
                    'parcela_id'      => $parcela->id,
                    'status_anterior' => $parcela->getOriginal('status'),
                    'status_novo'     => $parcela->status,
                    'origem_mudanca'  => 'manual_usuario',
                    'usuario_id'      => Auth::id(),
                ]);
            }
        });
    }

    public function cobranca(): BelongsTo
    {
        return $this->belongsTo(Cobranca::class);
    }

    public function historicoStatus(): HasMany
    {
        return $this->hasMany(HistoricoStatusParcela::class);
    }

    public function scoreHistorico(): HasMany
    {
        return $this->hasMany(ScoreHistorico::class);
    }

    public function logMensagens(): HasMany
    {
        return $this->hasMany(LogMensagem::class);
    }
}
