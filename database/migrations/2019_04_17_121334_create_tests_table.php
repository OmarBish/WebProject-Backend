<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('websiteURL');
            $table->double('credit',10,2);
            $table->string('tags');
            $table->string('post_url')->nullable();
            $table->integer('client_id');
            $table->boolean('video')->nullable()->default(false);
            $table->boolean('comment')->nullable()->default(false);
            $table->boolean('active')->nullable()->default(false);
            $table->boolean('availabe')->default(true);
            $table->integer('testers')->default(0);
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
        Schema::dropIfExists('tests');
    }
}
