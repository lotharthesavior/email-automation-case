<?php

namespace Database\Factories;

use App\Models\Attachment;
use App\Models\Email;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttachmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Attachment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'path' => '/some/path/' . $this->faker->word() . '.txt',
            'name' => $this->faker->word() . '.txt',
            'email_id' => function(){
                return Email::factory()->create();
            },
        ];
    }
}
