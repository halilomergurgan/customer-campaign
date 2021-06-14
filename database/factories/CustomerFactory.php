<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Customer::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => ucwords($this->faker->unique()->words(rand(1, 3), true)) . $this->faker->randomNumber(),
            'since' => $this->faker->date(),
            'revenue' => $this->faker->randomFloat(2, 100, 10000),
        ];
    }
}
