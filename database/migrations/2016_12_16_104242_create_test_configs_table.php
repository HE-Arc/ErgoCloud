<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTestConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql')->create('test_configs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('test_id')->unsigned()->index('test_id');
            //$table->foreign('test_id');
            $table->string('google_results_url');
            $table->string('name_column');
            $table->string('evaluation_column');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql')->drop('test_config');
    }
}
