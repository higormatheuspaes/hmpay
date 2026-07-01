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
        Schema::table('empresas', function (Blueprint $table) {
            $table->boolean('notif_lembrete_antes_ativo')->default(false)->after('logo_path');
            $table->unsignedTinyInteger('notif_lembrete_antes_dias')->default(3)->after('notif_lembrete_antes_ativo');
            $table->boolean('notif_lembrete_dia_ativo')->default(false)->after('notif_lembrete_antes_dias');
            $table->boolean('notif_aviso_atraso_ativo')->default(false)->after('notif_lembrete_dia_ativo');
            $table->boolean('notif_confirmacao_pagamento_ativo')->default(false)->after('notif_aviso_atraso_ativo');
        });
    }

    public function down(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->dropColumn([
                'notif_lembrete_antes_ativo',
                'notif_lembrete_antes_dias',
                'notif_lembrete_dia_ativo',
                'notif_aviso_atraso_ativo',
                'notif_confirmacao_pagamento_ativo',
            ]);
        });
    }
};
