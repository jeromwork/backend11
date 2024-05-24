<?php

namespace Modules\Slots\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Modules\Slots\Models\Slot;

class SlotsDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Model::unguard();
        Slot::factory(500)->create();
        // $this->call("OthersTableSeeder");
    }
}
