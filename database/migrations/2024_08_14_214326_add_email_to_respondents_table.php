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
        Schema::table('respondents', function (Blueprint $table) {
            $table->string('email')->nullable();
            /**
             * escolhi armazenar o e-mail no db por questões de falha no envio, dessa forma, através de uma rotina, por exemplo, pode-se tentar enviar novamente o e-mail
             */
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('respondents', function (Blueprint $table) {
            $table->dropColumn('email');
        });
    }
};
