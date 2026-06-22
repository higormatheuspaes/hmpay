<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Assinatura extends Model
{
    protected $fillable = [
        'empresa_id',
        'status',
        'gateway_assinatura_id_externo',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }
}
