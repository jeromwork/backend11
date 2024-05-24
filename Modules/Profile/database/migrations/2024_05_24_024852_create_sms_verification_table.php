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
            Schema::create('sms_verifications', function (Blueprint $table) {
                $table->id();
                $table->char('phone');
                $table->char('code');
                $table->integer('expiration')->default(0);
                $table->integer('try')->default(0);
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
        Schema::dropIfExists('sms_verifications');
    }
};
