<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Empresa extends Model
{
    protected $fillable = [
        'nome',
        'cnpj_cpf',
        'email',
        'telefone',
        'plano_id',
        'status_assinatura',
    ];

    protected function casts(): array
    {
        return [
            'status_assinatura' => 'string',
        ];
    }

    public function plano(): BelongsTo
    {
        return $this->belongsTo(Plano::class);
    }

    public function clientes(): HasMany
    {
        return $this->hasMany(Cliente::class);
    }

    public function cobrancas(): HasMany
    {
        return $this->hasMany(Cobranca::class);
    }

    public function integracoesGateway(): HasMany
    {
        return $this->hasMany(IntegracaoGateway::class);
    }

    public function assinatura(): HasOne
    {
        return $this->hasOne(Assinatura::class);
    }

    public function consumoMensagensMes(): HasMany
    {
        return $this->hasMany(ConsumoMensagensMes::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
