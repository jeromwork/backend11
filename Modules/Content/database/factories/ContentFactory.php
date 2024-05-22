<?php

namespace Modules\Content\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ContentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = \Modules\Content\Models\Content::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [];
    }
}

