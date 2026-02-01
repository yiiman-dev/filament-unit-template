<?php

namespace Database\Factories;

use App\UserRole;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @template TModel of \App\UserRole
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<TModel>
 */
class UserRoleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<TModel>
     */
    protected $model = UserRole::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
        ];
    }
}
