<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('log_mensagens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas');
            $table->foreignId('cliente_id')->constrained('clientes');
            $table->foreignId('parcela_id')->nullable()->constrained('parcelas');
            $table->enum('tipo', ['lembrete_antes', 'lembrete_dia', 'aviso_atraso', 'confirmacao_pagamento']);
            $table->string('telefone');
            $table->text('mensagem');
            $table->enum('status', ['enviado', 'erro']);
            $table->text('erro_detalhes')->nullable();
            $table->timestamp('enviado_em')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_mensagens');
    }
};
