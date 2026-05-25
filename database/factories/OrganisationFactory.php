<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Organisation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Organisation>
 */
final class OrganisationFactory extends Factory
{
    protected $model = Organisation::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company(),
        ];
    }
}
