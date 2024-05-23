<?php

namespace Modules\Health\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DoctorVariationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = \Modules\Health\Models\DoctorVariation::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [];
    }
}

