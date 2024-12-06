<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Primeiro, crie um departamento padrão se não existir
        $departamentoPadrao = DB::table('departamentos')
            ->where('sigla', 'DEP')
            ->first();

        if (!$departamentoPadrao) {
            $departamentoPadraoId = DB::table('departamentos')->insertGetId([
                'nome' => 'Departamento Padrão',
                'sigla' => 'DEP',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } else {
            $departamentoPadraoId = $departamentoPadrao->id;
        }

        // Altere a tabela de usuários
        Schema::table('users', function (Blueprint $table) use ($departamentoPadraoId) {
            // Torne o campo nullable se já não for
            $table->unsignedBigInteger('departamento_id')
                ->nullable()
                ->default($departamentoPadraoId)
                ->change();
        });

        // Atualize usuários existentes com o departamento padrão
        DB::table('users')
            ->whereNull('departamento_id')
            ->update(['departamento_id' => $departamentoPadraoId]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('departamento_id')->nullable(false)->change();
        });
    }
};