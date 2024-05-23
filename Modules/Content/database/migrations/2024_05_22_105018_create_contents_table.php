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
        if (!Schema::hasTable('contents')) {
            Schema::create('contents', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->char('file')->default('');
                $table->char('url')->default('');
                $table->char('is_preview_for')->default('');
                $table->char('parent_id')->default('');
                $table->char('type')->default('');
                $table->char('typeFile')->default('');
                $table->char('mime')->default('');
                $table->char('targetClass')->default('');
                $table->char('original_file_name')->default('');
                $table->char('alt')->nullable(false)->default('');
                $table->char('file_extension')->default('');
                $table->unsignedInteger('file_size')->default(0);
                $table->integer('left_handle_replicas')->default(-1);
                $table->nullableMorphs('contentable');
                $table->boolean('confirm')->default(false);
                $table->boolean('published')->default(false);
                $table->timestamps();

            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contents');
    }
};
