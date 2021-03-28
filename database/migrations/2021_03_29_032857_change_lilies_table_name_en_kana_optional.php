<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeLiliesTableNameEnKanaOptional extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lilies', function (Blueprint $table) {
            $table->text('name')->nullable()->change();
            $table->text('name_a')->nullable()->change();
            $table->text('name_y')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lilies', function (Blueprint $table) {
            $table->text('name')->nullable(false)->change();
            $table->text('name_a')->nullable(false)->change();
            $table->text('name_y')->nullable(false)->change();
        });
    }
}
