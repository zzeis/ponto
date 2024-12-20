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
        Schema::create('registros_ponto', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->date('data');
            $table->enum('tipo', [
                'entrada_manha', 
                'saida_almoco', 
                'retorno_almoco', 
                'saida_fim'
            ]);
            $table->time('hora');
            $table->timestamps();
    
            $table->foreign('user_id')->references('id')->on('users');
            $table->unique(['user_id', 'data', 'tipo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registros_ponto');
    }
};
