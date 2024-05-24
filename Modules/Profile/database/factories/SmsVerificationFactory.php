<?php

namespace Modules\Profile\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SmsVerificationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = \Modules\Profile\Models\SmsVerification::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [];
    }
}

