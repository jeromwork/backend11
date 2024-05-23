<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (!Schema::hasTable('health_variations')) {
            Schema::create('health_variations', function (Blueprint $table) {
                $table->id();
                $table->char('name');
                $table->foreignId('iservice_id')->references('id')->on('health_iservices');
//                $table->char('skill')->default('usual');
                $table->unsignedInteger('skill')->default(0);
                $table->boolean('active')->default(true);
                $table->char('option')->default('');
                $table->timestamps();

                $table->index('skill');
                $table->index('active');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('health_variations');
        Schema::enableForeignKeyConstraints();
    }
};
