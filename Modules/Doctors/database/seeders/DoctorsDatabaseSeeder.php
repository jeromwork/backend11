<?php

namespace Modules\Doctors\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Doctors\Models\Doctor;

class DoctorsDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        config(['database.default' => 'MODX']);
        Schema::disableForeignKeyConstraints();

        DB::table('modx_doc_doctors')->truncate();


        Schema::enableForeignKeyConstraints();


        Doctor::factory(100)->create();
        config(['database.default' => 'sqlite']);
    }
}
