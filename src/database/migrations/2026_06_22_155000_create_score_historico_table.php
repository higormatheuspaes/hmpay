<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('score_historico', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->foreignId('parcela_id')->constrained('parcelas')->cascadeOnDelete();
            $table->integer('pontos_aplicados');
            $table->integer('score_resultante');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('score_historico');
    }
};
