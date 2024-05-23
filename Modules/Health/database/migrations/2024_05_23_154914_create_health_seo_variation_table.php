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
        if (!Schema::hasTable('health_seo_variation')) {
            Schema::create('health_seo_variation', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('seo_id');
                $table->unsignedBigInteger('variation_id');


                $table->foreign('seo_id')->references('id')->on('health_seo')->onDelete('cascade');
                $table->foreign('variation_id')->references('id')->on('health_variations')->onDelete('cascade');


                $table->index(['seo_id', 'variation_id', ]);
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
        Schema::dropIfExists('health_seo_variation');
        Schema::enableForeignKeyConstraints();

    }
};
