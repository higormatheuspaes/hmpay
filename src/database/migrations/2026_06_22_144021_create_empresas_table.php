<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('cnpj_cpf', 18);
            $table->string('email');
            $table->string('telefone', 20);
            $table->foreignId('plano_id')->constrained('planos');
            $table->enum('status_assinatura', ['trial', 'ativa', 'inadimplente', 'suspensa', 'cancelada'])->default('trial');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empresas');
    }
};
