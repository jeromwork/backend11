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
        if (!Schema::hasTable('health_doctor_variation')) {
            Schema::create('health_doctor_variation', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('doctor_id');
                $table->unsignedBigInteger('variation_id');


                $table->float('price')->default(-1);
                $table->boolean('active')->default(true);
                $table->boolean('use_always')->default(false);
                $table->text('description_private')->nullable();

                // Define foreign key constraints
//                $table->foreign('doctor_id')->references('id')->on('modx_doc_doctors')->onDelete('cascade');
                $table->foreign('variation_id')->references('id')->on('health_variations')->onDelete('cascade');


                // Add indexes
                $table->index(['doctor_id', 'variation_id']);
                $table->index('price');
                $table->index('active');
                $table->index('use_always');

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
        Schema::dropIfExists('health_doctor_variation');
        Schema::enableForeignKeyConstraints();

    }
};
