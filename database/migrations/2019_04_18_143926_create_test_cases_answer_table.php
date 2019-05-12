<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTestCasesAnswerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test_case_answers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('clientRate')->nullable();
            $table->integer('userRate')->nullable();
            $table->string('answer');
            $table->integer('test_result_id');
            $table->integer('test_case_id');
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
        Schema::dropIfExists('test_cases_answers');
    }
}
