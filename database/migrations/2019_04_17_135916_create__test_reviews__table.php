<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTestReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test_reviews', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('feedback')->nullable();
            $table->string('testerRate')->nullable();
            $table->integer('client_id');
            $table->integer('tester_id')->default(-1);
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
        Schema::dropIfExists('test_reviews');
    }
}
