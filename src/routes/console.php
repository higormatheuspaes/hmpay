<?php

use App\Jobs\EnviarAvisosAtraso;
use App\Jobs\EnviarLembretes;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('score:atualizar-atrasados')->dailyAt('01:00');
Schedule::job(new EnviarLembretes)->dailyAt('08:00');
Schedule::job(new EnviarAvisosAtraso)->dailyAt('09:00');
