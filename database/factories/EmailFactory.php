<?php

namespace Database\Factories;

use App\Models\Email;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EmailFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Email::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'email' => $this->faker->unique()->safeEmail,
            'subject' => $this->faker->sentence(2),
            'body' => $this->faker->sentence(10),
            'user_id' => function(){
                return User::factory()->create();
            },
        ];
    }
}
