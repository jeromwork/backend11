<?php

namespace Modules\Orders\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = \Modules\Orders\Models\Purchase::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [];
    }
}

