<?php

namespace Database\Seeders;

use App\Models\Assinatura;
use App\Models\Cliente;
use App\Models\Cobranca;
use App\Models\Empresa;
use App\Models\Parcela;
use App\Models\Plano;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DevSeeder extends Seeder
{
    public function run(): void
    {
        // Planos
        $plano = Plano::firstOrCreate(['nome' => 'Pro'], [
            'limite_mensagens_mes' => 700,
            'valor_mensal'         => 149.00,
        ]);

        // Empresa
        $empresa = Empresa::create([
            'nome'               => 'Academia Fitness Pro',
            'cnpj_cpf'           => '12.345.678/0001-99',
            'email'              => 'contato@academiafitnespro.com.br',
            'telefone'           => '11987654300',
            'plano_id'           => $plano->id,
            'status_assinatura'  => 'trial',
        ]);

        // Assinatura trial
        Assinatura::create([
            'empresa_id' => $empresa->id,
            'status'     => 'trial',
        ]);

        // Usuário principal
        User::create([
            'name'       => 'Higor Paes',
            'email'      => 'higor@hmpay.com',
            'password'   => Hash::make('password'),
            'empresa_id' => $empresa->id,
        ]);

        // Clientes
        $clientes = [
            ['nome' => 'Carlos Eduardo Souza',  'telefone' => '11987654321', 'cpf_cnpj' => '123.456.789-01', 'email' => 'carlos@email.com',  'score_atual' => 100, 'score_categoria' => 'bom_pagador'],
            ['nome' => 'Fernanda Lima',          'telefone' => '21998765432', 'cpf_cnpj' => '234.567.890-12', 'email' => 'fernanda@email.com', 'score_atual' => 95,  'score_categoria' => 'bom_pagador'],
            ['nome' => 'Roberto Alves Pereira',  'telefone' => '31987654322', 'cpf_cnpj' => '345.678.901-23', 'email' => 'roberto@email.com',  'score_atual' => 70,  'score_categoria' => 'atencao'],
            ['nome' => 'Juliana Martins Costa',  'telefone' => '41998765433', 'cpf_cnpj' => '456.789.012-34', 'email' => 'juliana@email.com',  'score_atual' => 100, 'score_categoria' => 'bom_pagador'],
            ['nome' => 'Marcos Vinicius Rocha',  'telefone' => '51987654323', 'cpf_cnpj' => '567.890.123-45', 'email' => 'marcos@email.com',   'score_atual' => 40,  'score_categoria' => 'risco'],
            ['nome' => 'Patricia Oliveira',      'telefone' => '61998765434', 'cpf_cnpj' => '678.901.234-56', 'email' => 'patricia@email.com',  'score_atual' => 90,  'score_categoria' => 'bom_pagador'],
            ['nome' => 'Anderson Silva Santos',  'telefone' => '71987654324', 'cpf_cnpj' => '789.012.345-67', 'email' => 'anderson@email.com',  'score_atual' => 60,  'score_categoria' => 'atencao'],
            ['nome' => 'Camila Ferreira',        'telefone' => '81998765435', 'cpf_cnpj' => '890.123.456-78', 'email' => 'camila@email.com',    'score_atual' => 100, 'score_categoria' => 'bom_pagador'],
        ];

        $clienteModels = [];
        foreach ($clientes as $dados) {
            $clienteModels[] = Cliente::create(array_merge($dados, ['empresa_id' => $empresa->id]));
        }

        [$carlos, $fernanda, $roberto, $juliana, $marcos, $patricia, $anderson, $camila] = $clienteModels;

        // ───────────────────────────────────────────────
        // COBRANÇAS
        // ───────────────────────────────────────────────

        // 1. Mensalidade academia — Carlos — 6x — algumas pagas, uma atrasada
        $c1 = Cobranca::create([
            'empresa_id'      => $empresa->id,
            'cliente_id'      => $carlos->id,
            'descricao'       => 'Mensalidade Academia',
            'tipo'            => 'recorrente',
            'valor_total'     => 600.00,
            'numero_parcelas' => 6,
            'periodicidade'   => 'mensal',
        ]);
        $this->criarParcelas($c1, [
            ['numero' => 1, 'valor' => 100.00, 'vencimento' => now()->subMonths(5)->startOfMonth(), 'status' => 'pago',     'data_pagamento' => now()->subMonths(5)->startOfMonth()],
            ['numero' => 2, 'valor' => 100.00, 'vencimento' => now()->subMonths(4)->startOfMonth(), 'status' => 'pago',     'data_pagamento' => now()->subMonths(4)->startOfMonth()],
            ['numero' => 3, 'valor' => 100.00, 'vencimento' => now()->subMonths(3)->startOfMonth(), 'status' => 'pago',     'data_pagamento' => now()->subMonths(3)->startOfMonth()],
            ['numero' => 4, 'valor' => 100.00, 'vencimento' => now()->subMonths(2)->startOfMonth(), 'status' => 'pago',     'data_pagamento' => now()->subMonths(2)->startOfMonth()],
            ['numero' => 5, 'valor' => 100.00, 'vencimento' => now()->subMonth()->startOfMonth(),   'status' => 'pendente', 'data_pagamento' => null],
            ['numero' => 6, 'valor' => 100.00, 'vencimento' => now()->startOfMonth(),               'status' => 'pendente', 'data_pagamento' => null],
        ]);

        // 2. Personal trainer — Fernanda — 3x — todas pagas
        $c2 = Cobranca::create([
            'empresa_id'      => $empresa->id,
            'cliente_id'      => $fernanda->id,
            'descricao'       => 'Pacote Personal Trainer',
            'tipo'            => 'recorrente',
            'valor_total'     => 900.00,
            'numero_parcelas' => 3,
            'periodicidade'   => 'mensal',
        ]);
        $this->criarParcelas($c2, [
            ['numero' => 1, 'valor' => 300.00, 'vencimento' => now()->subMonths(3)->startOfMonth(), 'status' => 'pago', 'data_pagamento' => now()->subMonths(3)->startOfMonth()],
            ['numero' => 2, 'valor' => 300.00, 'vencimento' => now()->subMonths(2)->startOfMonth(), 'status' => 'pago', 'data_pagamento' => now()->subMonths(2)->startOfMonth()],
            ['numero' => 3, 'valor' => 300.00, 'vencimento' => now()->subMonth()->startOfMonth(),   'status' => 'pago', 'data_pagamento' => now()->subMonth()->startOfMonth()],
        ]);

        // 3. Avaliação física — Roberto — avulsa — atrasada
        $c3 = Cobranca::create([
            'empresa_id'      => $empresa->id,
            'cliente_id'      => $roberto->id,
            'descricao'       => 'Avaliação Física Completa',
            'tipo'            => 'avulsa',
            'valor_total'     => 250.00,
            'numero_parcelas' => 1,
            'periodicidade'   => null,
        ]);
        $this->criarParcelas($c3, [
            ['numero' => 1, 'valor' => 250.00, 'vencimento' => now()->subDays(15), 'status' => 'pendente', 'data_pagamento' => null],
        ]);

        // 4. Mensalidade — Juliana — 12x — 4 pagas, restante futuro
        $c4 = Cobranca::create([
            'empresa_id'      => $empresa->id,
            'cliente_id'      => $juliana->id,
            'descricao'       => 'Mensalidade Academia Anual',
            'tipo'            => 'recorrente',
            'valor_total'     => 1200.00,
            'numero_parcelas' => 12,
            'periodicidade'   => 'mensal',
        ]);
        $parcelas4 = [];
        for ($i = 1; $i <= 12; $i++) {
            $venc   = now()->subMonths(4)->addMonths($i - 1)->startOfMonth();
            $pago   = $i <= 4;
            $parcelas4[] = [
                'numero'         => $i,
                'valor'          => 100.00,
                'vencimento'     => $venc,
                'status'         => $pago ? 'pago' : 'pendente',
                'data_pagamento' => $pago ? $venc : null,
            ];
        }
        $this->criarParcelas($c4, $parcelas4);

        // 5. Suplementos — Marcos — avulsa — atrasada há 30 dias
        $c5 = Cobranca::create([
            'empresa_id'      => $empresa->id,
            'cliente_id'      => $marcos->id,
            'descricao'       => 'Compra Suplementos',
            'tipo'            => 'avulsa',
            'valor_total'     => 480.00,
            'numero_parcelas' => 1,
            'periodicidade'   => null,
        ]);
        $this->criarParcelas($c5, [
            ['numero' => 1, 'valor' => 480.00, 'vencimento' => now()->subDays(30), 'status' => 'pendente', 'data_pagamento' => null],
        ]);

        // 6. Plano semestral — Patricia — 6x — todas pendentes (futuro)
        $c6 = Cobranca::create([
            'empresa_id'      => $empresa->id,
            'cliente_id'      => $patricia->id,
            'descricao'       => 'Plano Semestral Premium',
            'tipo'            => 'recorrente',
            'valor_total'     => 720.00,
            'numero_parcelas' => 6,
            'periodicidade'   => 'mensal',
        ]);
        $parcelas6 = [];
        for ($i = 1; $i <= 6; $i++) {
            $parcelas6[] = [
                'numero'         => $i,
                'valor'          => 120.00,
                'vencimento'     => now()->addMonths($i - 1)->startOfMonth(),
                'status'         => 'pendente',
                'data_pagamento' => null,
            ];
        }
        $this->criarParcelas($c6, $parcelas6);

        // 7. Personal — Anderson — 4x — 1 paga, 1 atrasada, 2 futuras
        $c7 = Cobranca::create([
            'empresa_id'      => $empresa->id,
            'cliente_id'      => $anderson->id,
            'descricao'       => 'Personal Trainer Mensal',
            'tipo'            => 'recorrente',
            'valor_total'     => 800.00,
            'numero_parcelas' => 4,
            'periodicidade'   => 'mensal',
        ]);
        $this->criarParcelas($c7, [
            ['numero' => 1, 'valor' => 200.00, 'vencimento' => now()->subMonths(2)->startOfMonth(), 'status' => 'pago',     'data_pagamento' => now()->subMonths(2)->startOfMonth()],
            ['numero' => 2, 'valor' => 200.00, 'vencimento' => now()->subMonth()->startOfMonth(),   'status' => 'pendente', 'data_pagamento' => null],
            ['numero' => 3, 'valor' => 200.00, 'vencimento' => now()->addMonth()->startOfMonth(),   'status' => 'pendente', 'data_pagamento' => null],
            ['numero' => 4, 'valor' => 200.00, 'vencimento' => now()->addMonths(2)->startOfMonth(), 'status' => 'pendente', 'data_pagamento' => null],
        ]);

        // 8. Avulsa — Camila — paga
        $c8 = Cobranca::create([
            'empresa_id'      => $empresa->id,
            'cliente_id'      => $camila->id,
            'descricao'       => 'Taxa de Matrícula',
            'tipo'            => 'avulsa',
            'valor_total'     => 150.00,
            'numero_parcelas' => 1,
            'periodicidade'   => null,
        ]);
        $this->criarParcelas($c8, [
            ['numero' => 1, 'valor' => 150.00, 'vencimento' => now()->subMonths(2), 'status' => 'pago', 'data_pagamento' => now()->subMonths(2)],
        ]);
    }

    private function criarParcelas(Cobranca $cobranca, array $parcelas): void
    {
        foreach ($parcelas as $p) {
            Parcela::create([
                'cobranca_id'    => $cobranca->id,
                'numero'         => $p['numero'],
                'valor'          => $p['valor'],
                'vencimento'     => Carbon::parse($p['vencimento'])->toDateString(),
                'origem'         => 'automatica',
                'status'         => $p['status'],
                'data_pagamento' => $p['data_pagamento']
                    ? Carbon::parse($p['data_pagamento'])->toDateString()
                    : null,
            ]);
        }
    }
}
