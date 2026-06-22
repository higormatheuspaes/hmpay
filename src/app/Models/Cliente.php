<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    protected $fillable = [
        'empresa_id',
        'nome',
        'telefone',
        'cpf_cnpj',
        'email',
        'score_atual',
        'score_categoria',
    ];

    protected function casts(): array
    {
        return [
            'score_atual' => 'integer',
        ];
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function cobrancas(): HasMany
    {
        return $this->hasMany(Cobranca::class);
    }

    public function scoreHistorico(): HasMany
    {
        return $this->hasMany(ScoreHistorico::class);
    }
}
