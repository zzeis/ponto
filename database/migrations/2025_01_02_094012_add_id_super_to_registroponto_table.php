<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('registros_ponto', function (Blueprint $table) {
            $table->unsignedBigInteger('id_super')->nullable()->after('observacao');
            $table->foreign('id_super')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('registros_ponto', function (Blueprint $table) {
            $table->dropForeign(['id_super']);
            $table->dropColumn('id_super');
        });
    }
};
