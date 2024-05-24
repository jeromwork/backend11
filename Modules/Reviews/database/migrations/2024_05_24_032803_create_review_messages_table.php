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
            Schema::create('review_messages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('review_id')
                    ->constrained()
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
                $table->unsignedBigInteger('parent_id')->default(0);
                $table->text('message')->nullable();
                $table->text('published')->nullable();
                $table->text('author_id')->nullable();
                $table->text('author')->nullable();
                $table->timestamps();
            });
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('review_messages');
    }
};
