<?php

namespace Database\Seeders;

use App\Models\Plano;
use Illuminate\Database\Seeder;

class PlanoSeeder extends Seeder
{
    public function run(): void
    {
        $planos = [
            ['nome' => 'Básico', 'limite_mensagens_mes' => 150,   'valor_mensal' => 79.00],
            ['nome' => 'Pro',    'limite_mensagens_mes' => 700,   'valor_mensal' => 149.00],
            ['nome' => 'Plus',   'limite_mensagens_mes' => 3500,  'valor_mensal' => 299.00],
        ];

        foreach ($planos as $plano) {
            Plano::firstOrCreate(['nome' => $plano['nome']], $plano);
        }
    }
}
