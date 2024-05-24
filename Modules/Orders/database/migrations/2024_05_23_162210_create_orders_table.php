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
        if (!Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->json('contacts')->nullable();
                $table->unsignedInteger('client_id')->nullable();
                $table->unsignedInteger('sum')->default(0);
//            $table->unsignedInteger('count')->default(0);
                $table->text('note')->nullable();
                $table->boolean('is_online')->default(true);
                $table->char('payment_provider')->default('');
                $table->text('pay_url')->nullable();
                $table->char('pay_id')->nullable();
                $table->char('status')->default('');
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
        Schema::dropIfExists('orders');
    }
};
