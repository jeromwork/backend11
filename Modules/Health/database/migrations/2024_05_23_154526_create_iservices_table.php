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
        if (!Schema::hasTable('health_iservices')) {
            Schema::create('health_iservices', function (Blueprint $table) {
                $table->id();
                $table->char('name');
                $table->unsignedBigInteger('iid')->default(0);
                $table->float('price')->default(0);

                $table->timestamps();
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
        Schema::dropIfExists('health_iservices');
        Schema::enableForeignKeyConstraints();
    }
};
