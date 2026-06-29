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
            $table->boolean('notificacoes_ativas')->default(false)->after('telefone');
            $table->unsignedTinyInteger('dias_antes_vencimento')->default(3)->after('notificacoes_ativas');
            $table->enum('frequencia_aviso_atraso', ['diaria', 'semanal', 'mensal'])->default('semanal')->after('dias_antes_vencimento');
        });
    }

    public function down(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->dropColumn([
                'notificacoes_ativas',
                'dias_antes_vencimento',
                'frequencia_aviso_atraso',
            ]);
        });
    }
};
