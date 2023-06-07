<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Report>
 */
class ReportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $statuses = ["P", "A", "S", "C"];
        return [
            "department_id" => fake()->numberBetween(1, 20),
            "issue_id" => fake()->numberBetween(1, 20),
            "status" => $statuses[fake()->numberBetween(0, 3)],
            "description" => fake()->text(80),
            "priority" => fake()->numberBetween(1, 5),
        ];
    }
}
