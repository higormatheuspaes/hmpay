<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Cobranca;
use App\Models\Empresa;
use App\Models\LogMensagem;
use App\Models\Parcela;
use App\Models\User;
use App\Services\MensagemService;
use Illuminate\Database\Seeder;

class NotificacaoTestSeeder extends Seeder
{
    public function run(): void
    {
        $empresa = User::first()->empresa;
        $service = new MensagemService;

        // Garante notificações ativas
        $empresa->update([
            'notif_lembrete_antes_ativo'       => true,
            'notif_lembrete_antes_dias'         => 3,
            'notif_lembrete_dia_ativo'          => true,
            'notif_aviso_atraso_ativo'          => true,
            'notif_confirmacao_pagamento_ativo' => true,
        ]);

        // Limpa dados de teste anteriores
        Cliente::where('nome', 'Higor (Teste)')->where('empresa_id', $empresa->id)
            ->each(function (Cliente $c) {
                LogMensagem::where('cliente_id', $c->id)->delete();
                Parcela::whereHas('cobranca', fn($q) => $q->where('cliente_id', $c->id))->delete();
                Cobranca::where('cliente_id', $c->id)->delete();
                $c->delete();
            });

        $cliente = Cliente::create([
            'empresa_id'      => $empresa->id,
            'telefone'        => '4796475752',
            'nome'            => 'Higor (Teste)',
            'score_atual'     => 100,
            'score_categoria' => 'bom_pagador',
        ]);

        $this->command->info("Cliente: {$cliente->nome}");

        // Parcela vencendo HOJE → testa lembrete no dia
        $this->criarETestar($empresa, $cliente, today(), $service, 'lembrete_dia');

        // Parcela vencendo em 3 dias → testa lembrete antes
        $this->criarETestar($empresa, $cliente, today()->addDays(3), $service, 'lembrete_antes');

        // Parcela atrasada → testa aviso de atraso
        $this->criarETestar($empresa, $cliente, today()->subDays(5), $service, 'aviso_atraso');

        // Parcela paga → testa confirmação de pagamento
        $this->criarETestar($empresa, $cliente, today()->subDays(2), $service, 'confirmacao_pagamento');

        $this->command->info('Mensagens disparadas! Verifique o WhatsApp.');
    }

    private function criarETestar(Empresa $empresa, Cliente $cliente, $vencimento, MensagemService $service, string $tipo): void
    {
        $cobranca = Cobranca::create([
            'empresa_id'      => $empresa->id,
            'cliente_id'      => $cliente->id,
            'descricao'       => "Teste notificação: {$tipo}",
            'tipo'            => 'avulsa',
            'valor_total'     => 100,
            'numero_parcelas' => 1,
            'periodicidade'   => null,
        ]);

        $parcela = Parcela::create([
            'cobranca_id'    => $cobranca->id,
            'numero'         => 1,
            'valor'          => 100,
            'vencimento'     => $vencimento,
            'origem'         => 'automatica',
            'status'         => $tipo === 'confirmacao_pagamento' ? 'pago' : 'pendente',
            'data_pagamento' => $tipo === 'confirmacao_pagamento' ? today() : null,
        ]);

        $parcela->load('cobranca.empresa', 'cobranca.cliente');

        $this->command->info("Enviando {$tipo}...");

        match($tipo) {
            'lembrete_dia'          => $service->enviarLembreteDia($parcela),
            'lembrete_antes'        => $service->enviarLembreteAntes($parcela),
            'aviso_atraso'          => $service->enviarAvisoAtraso($parcela),
            'confirmacao_pagamento' => $service->enviarConfirmacaoPagamento($parcela),
        };
    }
}
