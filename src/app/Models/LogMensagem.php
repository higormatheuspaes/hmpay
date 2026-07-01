<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogMensagem extends Model
{
    protected $table = 'log_mensagens';

    public $timestamps = false;

    protected $fillable = [
        'empresa_id',
        'cliente_id',
        'parcela_id',
        'tipo',
        'telefone',
        'mensagem',
        'status',
        'erro_detalhes',
        'enviado_em',
    ];

    protected $casts = [
        'enviado_em' => 'datetime',
    ];

    public function empresa()   { return $this->belongsTo(Empresa::class); }
    public function cliente()   { return $this->belongsTo(Cliente::class); }
    public function parcela()   { return $this->belongsTo(Parcela::class); }
}
