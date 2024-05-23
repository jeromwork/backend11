<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    protected $connection = DB_CONNECTION_MODX;
    /**
     * Run the migrations.
     */
    public function up()
    {

        if (!Schema::hasTable('modx_doc_doctors')) {
            Schema::create('modx_doc_doctors', function (Blueprint $table) {
                $table->id();
                $table->integer('iid')->default(0);
                $table->integer('id_resource')->default(0);
                $table->string('uri', 191)->nullable();
                $table->string('surname', 50)->nullable();
                $table->string('name', 100)->nullable();
                $table->string('middlename', 50)->nullable();
                $table->string('fullname', 100)->nullable();
                $table->string('photo', 255)->nullable();
                $table->string('photo_type', 60)->nullable();
                $table->json('photos')->nullable();
                $table->json('videos')->nullable();
                $table->tinyInteger('holiday')->default(0);
                $table->integer('rating')->default(0);
                $table->float('rating5')->default(0);
                $table->integer('comments')->default(0);
                $table->tinyInteger('show_comments')->default(1);
                $table->integer('child')->default(0);
                $table->tinyInteger('pregnant')->default(0);
                $table->text('diseases')->nullable();
                $table->integer('experience')->default(0);
                $table->text('way_experience')->nullable();
                $table->tinyInteger('show_experience')->default(1);
                $table->tinyInteger('telemedicine')->default(0);
                $table->text('training')->nullable();
                $table->text('longtitle')->nullable();
                $table->text('description')->nullable();
                $table->mediumText('description_full')->nullable();
                $table->text('introtext')->nullable();
                $table->integer('age_from')->default(0);
                $table->integer('age_to')->default(100);
                $table->integer('skill')->default(0);
                $table->tinyInteger('is_primary_care')->nullable();
                $table->tinyInteger('is_doctor')->default(1);
                $table->tinyInteger('is_nurse')->default(0);
                $table->tinyInteger('is_speciality')->default(0);
                $table->tinyInteger('is_analyze')->default(0);
                $table->tinyInteger('off')->default(0);
                $table->text('research')->nullable();
                $table->text('diploms_cache')->nullable();
                $table->text('content_cache')->nullable();
                $table->string('quotes', 5000)->default('');
                $table->text('interviews')->nullable();
                $table->text('awards')->nullable();
                $table->timestamps();
                $table->index('iid');
                $table->index('id_resource');
                $table->index('uri');
                $table->char('yawizard_url')->default('');
                $table->char('scheduleConst')->default('');
                $table->char('is_diagnostic')->default('');
                $table->char('is_cabinet')->default('');
            });
        }


    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('modx_doc_doctors');
    }
};
