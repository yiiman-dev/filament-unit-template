<?php

namespace Database\Factories;

use App\Units\DocumentTypes\Common\Models\DocumentType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @template TModel of \App\Units\DocumentTypes\Common\Models\DocumentType
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<TModel>
 */
class DocumentTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<TModel>
     */
    protected $model = DocumentType::class;

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
