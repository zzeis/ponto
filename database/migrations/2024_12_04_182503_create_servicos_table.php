<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('servicos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tecnico_id');
            $table->unsignedBigInteger('supervisor_id');
            $table->text('descricao');
            $table->enum('prioridade', ['baixa', 'media', 'alta']);
            $table->enum('status', ['pendente', 'em_andamento', 'concluido']);
            $table->date('data_limite');
            $table->timestamps();
    
            $table->foreign('tecnico_id')->references('id')->on('users');
            $table->foreign('supervisor_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servicos');
    }
};
