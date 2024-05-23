<?php

namespace Modules\Health\Database\Seeders;

use Faker\Factory as Faker;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Doctors\Models\Doctor;
use Modules\Health\Models\Iservice;
use Modules\Health\Models\Seo;
use Modules\Health\Models\Variation;

class HealthDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        Model::unguard();
        Schema::disableForeignKeyConstraints();

        DB::table('health_iservices')->truncate();
        DB::table('health_variations')->truncate();
        DB::table('health_seo')->truncate();
        DB::table('health_doctor_variation')->truncate();
        DB::table('health_seo_variation')->truncate();

        Schema::enableForeignKeyConstraints();




        Iservice::factory(280)->create();


        // Create 20 variations
        $variations = Variation::factory(340)->create();

        //doctors already exists
        if($doctors = Doctor::get()){       // Attach variations to doctors

            foreach ($doctors as $doctor) {
                $variationsToAdd = $variations->random(random_int(0, 5));
                $doctor->variations()->attach($variationsToAdd);
            }
        }

        Seo::factory(800)->create();
        if(        $seo = Seo::where('type', 'service')->get() ){
            //        // Attach variations to doctors
            foreach ($seo as $item) {
                $variationsToAdd = $variations->random(random_int(0, 5));
                $item->variations()->attach($variationsToAdd);
            }
        }
    }
}
