<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartamentosSeeder extends Seeder
{
    public function run()
    {
        DB::table('departamentos')->insert([
            ['nome' => 'Departamento Padrão', 'sigla' => 'DEP', 'created_at' => now(), 'updated_at' => now()]
        ]);
    }
}
