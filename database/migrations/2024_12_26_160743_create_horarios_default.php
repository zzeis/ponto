<?php

// Segunda migration: recriar a tabela horarios_trabalho
// database/migrations/[timestamp]_create_horarios_trabalho_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('horarios_trabalho', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->time('entrada_manha');
            $table->time('saida_manha');
            $table->time('entrada_tarde');
            $table->time('saida_tarde');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('horarios_trabalho');
    }
};