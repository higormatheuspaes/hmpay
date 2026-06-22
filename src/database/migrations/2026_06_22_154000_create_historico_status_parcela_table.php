<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historico_status_parcela', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parcela_id')->constrained('parcelas')->cascadeOnDelete();
            $table->enum('status_anterior', ['pendente', 'pago', 'atrasado', 'cancelado']);
            $table->enum('status_novo', ['pendente', 'pago', 'atrasado', 'cancelado']);
            $table->enum('origem_mudanca', ['webhook', 'manual_usuario']);
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->json('payload_bruto')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historico_status_parcela');
    }
};
