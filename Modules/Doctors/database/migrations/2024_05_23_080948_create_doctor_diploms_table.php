<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    protected $connection = DB_CONNECTION_DEFAULT;
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('doctor_diploms');
        Schema::create('doctor_diploms', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('');
            $table->boolean('published')->default(false);
            $table->unsignedBigInteger('doctor_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_diploms');
    }
};
