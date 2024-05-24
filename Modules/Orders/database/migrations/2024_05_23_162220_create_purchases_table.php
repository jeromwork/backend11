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
        if (!Schema::hasTable('purchases')) {
            Schema::create('purchases', function (Blueprint $table) {
                $table->char('name')->default('');
                $table->unsignedInteger('price');
                $table->unsignedInteger('count');
                $table->char('order_id')->nullable();
                $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
//            $table->char('order_id')->nullable();
                $table->text('note')->nullable();
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
        Schema::dropIfExists('purchases');
    }
};
