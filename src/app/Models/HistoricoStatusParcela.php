<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistoricoStatusParcela extends Model
{
    protected $fillable = [
        'parcela_id',
        'status_anterior',
        'status_novo',
        'origem_mudanca',
        'usuario_id',
        'payload_bruto',
    ];

    protected function casts(): array
    {
        return [
            'payload_bruto' => 'array',
        ];
    }

    public function parcela(): BelongsTo
    {
        return $this->belongsTo(Parcela::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
