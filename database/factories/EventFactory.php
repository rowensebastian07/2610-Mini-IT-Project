<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    protected $model = Event::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'club_id'     => \App\Models\Club::factory(), // link to a club
            'title'       => $this->faker->sentence(3),
            'date'        => $this->faker->date(),
            'time'        => $this->faker->time(),
            'description' => $this->faker->paragraph(),
            'location'    => $this->faker->city(),
            'is_passed'   => false,
        ];
    }
}
