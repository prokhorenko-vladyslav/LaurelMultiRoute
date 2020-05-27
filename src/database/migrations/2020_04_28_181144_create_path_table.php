<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Package migration
 *
 * Class CreatePathTable
 */
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
            $table->string('slug')->index()->nullable()->comment('Slug for path');
            $table->string('prefix')->nullable()->comment('Prefix for slug');
            $table->unsignedBigInteger('parent_id')->nullable()->comment('Path parent');
            $table->string('callback')->comment('Callback for path');
            $table->json('middleware')->nullable()->comment('List of path middleware');
            $table->boolean('is_active')->comment('Path activity');
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
