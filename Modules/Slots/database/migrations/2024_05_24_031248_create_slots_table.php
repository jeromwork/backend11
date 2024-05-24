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

        if (!Schema::hasTable('modx_eslot_slots')) {
            Schema::create('modx_eslot_slots', function (Blueprint $table) {
                $table->unsignedInteger('iid');

                $table->unsignedBigInteger('time_begin')->default(0);
                $table->unsignedBigInteger('time_end')->default(0);

                $table->unsignedInteger('doctor_id')->default(0);
                $table->unsignedInteger('clinic_id')->default(0);
                $table->unsignedInteger('cabinet_id')->default(0);

                $table->integer('user_id')->default(0);

    //            $table->unsignedInteger('variation_id')->default(0);
    //            $table->unsignedInteger('seo_id')->default(0);
    //            $table->char('seo_name')->default(0);//there is possible not need
    //            $table->char('seo_options')->default(0);//its possible not need
    //            $table->unsignedInteger('price')->default(0);
    //            $table->unsignedInteger('count')->default(0);

                $table->integer('slot_type')->default(0);
                $table->tinyInteger('is_autoreserve')->default(0);
                $table->integer('slot_create_time')->default(0);
                $table->string('vendor_id')->nullable();


                $table->char('order_id')->nullable();
//                $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
                $table->string('user_id')->nullable();

                //todo #addForeign ref when add models (clinics, vendors etc)
                $table->text('note')->nullable();
                $table->timestamps();
            });
        }
//

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modx_eslot_slots');
    }
};
