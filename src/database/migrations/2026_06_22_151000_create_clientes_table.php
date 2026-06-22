<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->string('nome');
            $table->string('telefone', 20);
            $table->string('cpf_cnpj', 18)->nullable();
            $table->string('email')->nullable();
            $table->integer('score_atual')->default(100);
            $table->enum('score_categoria', ['bom_pagador', 'atencao', 'risco'])->default('bom_pagador');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
