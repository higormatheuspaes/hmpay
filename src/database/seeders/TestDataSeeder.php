<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Cobranca;
use App\Models\Parcela;
use App\Models\ScoreHistorico;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestDataSeeder extends Seeder
{
    private int $empresaId;

    public function run(): void
    {
        $this->empresaId = User::first()->empresa_id;

        $this->command->info('Limpando dados anteriores...');
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        ScoreHistorico::where('cliente_id', '!=', 0)->delete();
        Parcela::whereHas('cobranca', fn($q) => $q->where('empresa_id', $this->empresaId))->delete();
        Cobranca::where('empresa_id', $this->empresaId)->delete();
        Cliente::where('empresa_id', $this->empresaId)->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->command->info('Criando clientes e cobranças...');
        $this->seedClientes();

        $this->command->info('Recalculando scores...');
        \Artisan::call('payog:recalcular');
        $this->command->line(\Artisan::output());
    }

    private function seedClientes(): void
    {
        $clientes = [
            // Bons pagadores
            ['nome' => 'Ana Paula Carvalho',      'telefone' => '11991234001', 'cpf_cnpj' => '111.222.333-01'],
            ['nome' => 'Bruno Henrique Lopes',    'telefone' => '11991234002', 'cpf_cnpj' => '111.222.333-02'],
            ['nome' => 'Carla Souza Mendes',      'telefone' => '11991234003', 'cpf_cnpj' => '111.222.333-03'],
            ['nome' => 'Diego Fernandes Costa',   'telefone' => '11991234004', 'cpf_cnpj' => '111.222.333-04'],
            ['nome' => 'Elaine Rodrigues',        'telefone' => '11991234005', 'cpf_cnpj' => '111.222.333-05'],
            ['nome' => 'Felipe Santos Oliveira',  'telefone' => '11991234006', 'cpf_cnpj' => '111.222.333-06'],
            ['nome' => 'Gabriela Lima Pereira',   'telefone' => '11991234007', 'cpf_cnpj' => '111.222.333-07'],
            ['nome' => 'Henrique Alves Junior',   'telefone' => '11991234008', 'cpf_cnpj' => '111.222.333-08'],
            // Atenção
            ['nome' => 'Isabela Martins Cruz',    'telefone' => '11991234009', 'cpf_cnpj' => '111.222.333-09'],
            ['nome' => 'João Victor Ribeiro',     'telefone' => '11991234010', 'cpf_cnpj' => '111.222.333-10'],
            ['nome' => 'Karen Moreira Vieira',    'telefone' => '11991234011', 'cpf_cnpj' => '111.222.333-11'],
            ['nome' => 'Lucas Barbosa Teixeira',  'telefone' => '11991234012', 'cpf_cnpj' => '111.222.333-12'],
            ['nome' => 'Mariana Castro Freitas',  'telefone' => '11991234013', 'cpf_cnpj' => '111.222.333-13'],
            // Risco
            ['nome' => 'Nelson Pinto Dias',       'telefone' => '11991234014', 'cpf_cnpj' => '111.222.333-14'],
            ['nome' => 'Olivia Cunha Monteiro',   'telefone' => '11991234015', 'cpf_cnpj' => '111.222.333-15'],
            ['nome' => 'Paulo Gomes Ramos',       'telefone' => '11991234016', 'cpf_cnpj' => '111.222.333-16'],
            ['nome' => 'Rafaela Torres Nunes',    'telefone' => '11991234017', 'cpf_cnpj' => '111.222.333-17'],
            ['nome' => 'Sergio Azevedo Campos',   'telefone' => '11991234018', 'cpf_cnpj' => '111.222.333-18'],
            ['nome' => 'Tatiana Borges Melo',     'telefone' => '11991234019', 'cpf_cnpj' => '111.222.333-19'],
            ['nome' => 'Ulisses Cardoso Duarte',  'telefone' => '11991234020', 'cpf_cnpj' => '111.222.333-20'],
        ];

        foreach ($clientes as $idx => $dados) {
            $cliente = Cliente::create(array_merge($dados, [
                'empresa_id'      => $this->empresaId,
                'score_atual'     => 100,
                'score_categoria' => 'bom_pagador',
            ]));

            $this->seedCobrancasCliente($cliente, $idx);
        }
    }

    private function seedCobrancasCliente(Cliente $cliente, int $idx): void
    {
        // Padrões de pagamento baseados no índice
        // 0-7: bons pagadores — pagam em dia
        // 8-12: atenção — alguns atrasos curtos (1-7 dias)
        // 13-19: risco — atrasos longos ou inadimplentes

        $tipo = match(true) {
            $idx <= 7  => 'bom',
            $idx <= 12 => 'atencao',
            default    => 'risco',
        };

        // Cada cliente tem 2 cobranças
        $this->criarCobrancaMensalidade($cliente, $tipo);
        $this->criarCobrancaAvulsa($cliente, $tipo);
    }

    private function criarCobrancaMensalidade(Cliente $cliente, string $tipo): void
    {
        $numParcelas = match($tipo) {
            'bom'     => 6,
            'atencao' => 5,
            'risco'   => 4,
        };

        $valor = [150, 200, 250, 300, 180, 220][$cliente->id % 6];

        // Começa 3 meses atrás → garante parcelas passadas E futuras
        $offset = 3;

        $cobranca = Cobranca::create([
            'empresa_id'      => $this->empresaId,
            'cliente_id'      => $cliente->id,
            'descricao'       => 'Mensalidade ' . now()->format('Y'),
            'tipo'            => 'recorrente',
            'valor_total'     => $valor * $numParcelas,
            'numero_parcelas' => $numParcelas,
            'periodicidade'   => 'mensal',
        ]);

        $parcelas = [];
        for ($i = 1; $i <= $numParcelas; $i++) {
            $venc   = now()->subMonths($offset)->addMonths($i - 1)->startOfMonth();
            $futuro = $venc->gte(now()->startOfMonth()->addMonth()); // vencimento no próximo mês ou além

            if ($futuro) {
                $parcelas[] = ['numero' => $i, 'valor' => $valor, 'vencimento' => $venc, 'status' => 'pendente', 'data_pagamento' => null];
                continue;
            }

            // Parcela mais recente do risco: inadimplente (mês atual ou anterior)
            if ($tipo === 'risco' && $venc->gte(now()->subMonth()->startOfMonth())) {
                $parcelas[] = ['numero' => $i, 'valor' => $valor, 'vencimento' => $venc, 'status' => 'atrasado', 'data_pagamento' => null];
                continue;
            }

            $diasAtraso = match($tipo) {
                'bom'     => 0,
                'atencao' => ($i % 2 === 0) ? rand(2, 6) : 0,
                'risco'   => ($i % 2 === 0) ? rand(10, 20) : rand(3, 8),
            };

            $parcelas[] = ['numero' => $i, 'valor' => $valor, 'vencimento' => $venc, 'status' => 'pago', 'data_pagamento' => $venc->copy()->addDays($diasAtraso)];
        }

        $this->criarParcelas($cobranca, $parcelas);
    }

    private function criarCobrancaAvulsa(Cliente $cliente, string $tipo): void
    {
        $valor = [80, 120, 90, 150, 200, 75][$cliente->id % 6];

        // 6 meses atrás
        $venc = now()->subMonths(rand(2, 5))->startOfMonth()->addDays(rand(5, 20));

        $diasAtraso = match($tipo) {
            'bom'     => 0,
            'atencao' => rand(0, 5),
            'risco'   => rand(8, 30),
        };

        // Alguns clientes de risco ainda não pagaram
        $inadimplente = $tipo === 'risco' && $cliente->id % 3 === 0;

        $cobranca = Cobranca::create([
            'empresa_id'      => $this->empresaId,
            'cliente_id'      => $cliente->id,
            'descricao'       => ['Avaliação Física', 'Taxa de Matrícula', 'Kit Uniforme', 'Suplementos', 'Exame de Saúde'][$cliente->id % 5],
            'tipo'            => 'avulsa',
            'valor_total'     => $valor,
            'numero_parcelas' => 1,
            'periodicidade'   => null,
        ]);

        if ($inadimplente) {
            $this->criarParcelas($cobranca, [
                ['numero' => 1, 'valor' => $valor, 'vencimento' => $venc, 'status' => 'atrasado', 'data_pagamento' => null],
            ]);
        } else {
            $this->criarParcelas($cobranca, [
                ['numero' => 1, 'valor' => $valor, 'vencimento' => $venc, 'status' => 'pago', 'data_pagamento' => $venc->copy()->addDays($diasAtraso)],
            ]);
        }
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
                'data_pagamento' => isset($p['data_pagamento']) && $p['data_pagamento']
                    ? Carbon::parse($p['data_pagamento'])->toDateString()
                    : null,
            ]);
        }
    }
}
