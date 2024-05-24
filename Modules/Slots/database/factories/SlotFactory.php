<?php

namespace Modules\Slots\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SlotFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = \Modules\Slots\Models\Slot::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [];
    }
}

