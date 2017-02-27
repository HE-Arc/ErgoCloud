<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSheetToTestConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql')->table('test_configs', function (Blueprint $table) {
             $table->string('sheet')->default('Form Responses 1');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql')->table('test_configs', function (Blueprint $table) {
            $table->dropColumn('sheet');
        });
    }
}
