<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Rental;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Rental>
 */
class RentalFactory extends Factory
{
    protected $model = Rental::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(), // Assumes you have a User factory
            'book_id' => Book::factory(), // Use the Book factory
            'issue_at' => $issueDate = $this->faker->date(),
            'overdue_at' => (new \Carbon\Carbon($issueDate))->addWeeks(2),
            'return_at' => $this->faker->optional()->date(),
            'is_overdue' => $this->faker->boolean(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
