<?php

namespace Database\Factories;

use Modules\Archives\Models\Archive;
use Modules\Auth\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Archive>
 */
class ArchiveFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Archive::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'type' => 'note',
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'is_favorite' => false,
        ];
    }

    /**
     * Indicate that the archive is favorited.
     */
    public function favorite(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_favorite' => true,
        ]);
    }

    /**
     * Set the archive type.
     */
    public function ofType(string $type): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => $type,
        ]);
    }
}
