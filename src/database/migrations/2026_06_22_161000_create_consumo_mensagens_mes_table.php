<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consumo_mensagens_mes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->date('ciclo_referencia');
            $table->integer('mensagens_enviadas')->default(0);
            $table->integer('mensagens_excedentes')->default(0);
            $table->decimal('valor_excedente_acumulado', 10, 2)->default(0);
            $table->decimal('teto_gasto_excedente', 10, 2)->nullable();
            $table->boolean('envios_pausados')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consumo_mensagens_mes');
    }
};
