<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IntegracaoGateway extends Model
{
    protected $fillable = [
        'empresa_id',
        'gateway',
        'credenciais_criptografadas',
        'ativo',
    ];

    protected function casts(): array
    {
        return [
            'credenciais_criptografadas' => 'encrypted',
            'ativo' => 'boolean',
        ];
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }
}
