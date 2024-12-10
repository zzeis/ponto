<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Dados do usuário admin
        $cpf = '48540290804'; // Insira o CPF desejado
        $email = 'admin@example.com'; // Email do administrador
        $password = 'admin123'; // Senha do administrador

        // Criar o usuário administrador
        User::create([
            'name' => 'Administrador',
            'email' => $email,

            'password' => Hash::make($password),

            'nivel_acesso' => 'admin',
            'is_active' => true,
            'first_login' => true, // Forçar troca de senha no primeiro login
            'cpf' => $cpf,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('Usuário administrador criado com sucesso.');
    }
}
