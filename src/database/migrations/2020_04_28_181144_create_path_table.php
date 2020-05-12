<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePathTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paths', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->index()->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('callback');
            $table->json('middleware');
            $table->boolean('is_active');
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('paths');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('route');
    }
}
