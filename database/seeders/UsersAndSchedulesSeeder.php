<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UsersAndSchedulesSeeder extends Seeder
{
    public function run()
    {
        // Criação de usuários
        $users = User::factory()->count(5)->create([
            'nivel_acesso' => 'estagiario',
            'is_active' => true,
            'first_login' => true,
        ]);

        // Criação de registros para cada usuário
        foreach ($users as $user) {
            foreach (range(1, 5) as $day) {
                $date = Carbon::now()->subDays($day);
                $user->registros()->createMany([
                    [
                        'data' => $date->toDateString(),
                        'tipo' => 'entrada_manha',
                        'hora' => $date->copy()->setTime(8, 0)->toTimeString(),
                        'observacao' => 'Entrada pela manhã',
                    ],
                    [
                        'data' => $date->toDateString(),
                        'tipo' => 'saida_almoco',
                        'hora' => $date->copy()->setTime(12, 0)->toTimeString(),
                        'observacao' => 'Saída para almoço',
                    ],
                    [
                        'data' => $date->toDateString(),
                        'tipo' => 'retorno_almoco',
                        'hora' => $date->copy()->setTime(13, 0)->toTimeString(),
                        'observacao' => 'Retorno do almoço',
                    ],
                    [
                        'data' => $date->toDateString(),
                        'tipo' => 'saida_fim',
                        'hora' => $date->copy()->setTime(17, 0)->toTimeString(),
                        'observacao' => 'Saída no fim do expediente',
                    ],
                ]);
            }
        }
    }
}
