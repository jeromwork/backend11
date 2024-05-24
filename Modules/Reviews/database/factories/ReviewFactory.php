<?php

namespace Modules\Reviews\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = \Modules\Reviews\Models\Review::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'author' => $this->faker->firstName(),
            'reviewable_type' => 'doctor',
            'reviewable_id' => $this->faker->numberBetween(1, 20),
            'text' => $this->faker->realText(),
            'rating' => $this->faker->randomElement([60, 65, 70, 75, 80, 85, 90, 95, 100, 100]),
            'published' => $this->faker->numberBetween(0,1),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now')->getTimestamp(),
            'is_new' => $this->faker->numberBetween(0,1),
        ];
    }
}

