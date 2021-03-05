<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeTriplesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('triples', function (Blueprint $table) {
            $table->dropColumn('lily_id');
            $table->string('lily_slug');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('triples', function (Blueprint $table) {
            $table->bigInteger('lily_id')->unsigned();
            $table->dropColumn('lily_slug');
        });
    }
}
