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
        if (!Schema::hasTable('health_seo')) {
            Schema::create('health_seo', function (Blueprint $table) {
                $table->id();
                $table->char('url', 255);
                $table->char('name');
                $table->char('title')->default('')->nullable();
                $table->boolean('active')->default(true);
                $table->char('type')->default('');
                $table->timestamps();


                $table->index('url');
                $table->index('active');
                $table->index('type');
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
        Schema::dropIfExists('health_seo');
        Schema::enableForeignKeyConstraints();

    }
};
