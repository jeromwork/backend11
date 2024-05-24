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
    public function up(): void
    {

        if (!Schema::hasTable('modx_shd_work_time')) {
            Schema::create('modx_shd_work_time', function (Blueprint $table) {
                $table->integer('iid')->nullable();
                $table->integer('clinic_id')->nullable();
                $table->integer('clinic_iid')->nullable();
                $table->integer('doctor_id')->nullable();
                $table->integer('doctor_iid')->nullable();
                $table->integer('workplace_iid')->nullable();
                $table->date('date');
                $table->integer('ux_date')->nullable();
                $table->time('work_begin');
                $table->time('work_end');
                $table->integer('ux_work_begin');
                $table->integer('ux_work_end');
                $table->integer('slot_interval')->nullable();
                $table->integer('doc_type')->nullable();
                $table->integer('autoreserve_offset')->default(0);
                $table->integer('count_busy_slots')->default(-1);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modx_shd_work_time');
    }
};
