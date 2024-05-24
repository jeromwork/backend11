<?php

namespace Modules\Reviews\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Reviews\Models\Review;
use Modules\Reviews\Models\ReviewMessage;

class ReviewMessageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = \Modules\Reviews\Models\ReviewMessage::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $parentId = (ReviewMessage::all()->count() > 0 ) ?  $this->faker->randomElement([ReviewMessage::all()->random()->id, 0, 0]) : 0;
        //if exist parentID, then not possible 0 authorId
        $authorId = ($parentId) ? $this->faker->numberBetween(1,5) : $this->faker->numberBetween(0,5);
        $authorName = (!$authorId) ? $this->faker->firstName() : null;


        return [
            'author' => $authorName,
            'author_id' => ($authorId) ? $authorId : null,
            'review_id' => Review::all()->random()->id,
            //'parent_id' =>  $parentId,
            'message' => $this->faker->realText(),
            'published' => $this->faker->numberBetween(0,1),
        ];
    }
}

