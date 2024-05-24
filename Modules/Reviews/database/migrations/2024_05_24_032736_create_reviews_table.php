<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('reviews')) {
            Schema::create('reviews', function (Blueprint $table) {
                $table->id();
                $table->char('author')->default('');
                $table->unsignedInteger('author_id')->default(0);
                $table->char('author_info')->default('');
                $table->float('rating')->default(0);
                $table->text('text');


                $table->char('confirm')->nullable(); //можно использовать номер договора или к примеру фото
                $table->unsignedTinyInteger('confirm_type')->nullable();
                $table->char('contact')->default('');

                $table->timestamp('published_at')->nullable();
                $table->unsignedTinyInteger('published')->default(0);
                $table->unsignedTinyInteger('is_new')->default(0);

//            $table->unsignedInteger('seo_page_id')->nullable();
//            $table->unsignedInteger('seo_page_type')->nullable();

//            $table->unsignedInteger('doctor_review_id')->nullable();


                $table->nullableMorphs('reviewable');
                $table->unsignedInteger('after_course')->default(0);
                $table->unsignedInteger('contract_number')->default(0);
                $table->unsignedInteger('from_resource_id')->default(0);
                $table->text('otvet_legacy')->nullable();
                $table->char('otvet_date_legacy')->default('');
                $table->text('otvet_legacy')->nullable()->change();
                $table->char('source')->default('site')->nullable();
                $table->timestamps();
            });
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
