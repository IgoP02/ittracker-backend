<?php

namespace Database\Seeders;

use App\Models\IssueType;
use Illuminate\Database\Seeder;

class IssueTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types =
            [
            "Red y Conectividad" => 2,
            "Servicios" => 2,
            "Software" => 1,
            "Hardware" => 2,
            "Soporte" => 1,
            "Otro" => 1,
        ];

        foreach ($types as $name => $value) {
            IssueType::create(
                [
                    "name" => $name,
                    "priority" => $value,
                ]);
        }
    }
}
