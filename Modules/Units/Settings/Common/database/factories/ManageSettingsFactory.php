<?php

namespace Units\Settings\Common\database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Units\Settings\Manage\Models\ManageSettings;

class ManageSettingsFactory extends Factory
{
    protected $model = ManageSettings::class;

    public function definition(): array
    {
        return [
            'key' => $this->faker->word,
            'value' => $this->faker->randomElement([
                $this->faker->numberBetween(100000, 1000000000), // Random amount
                $this->faker->numberBetween(1, 24), // Random months
                $this->faker->randomElement(['active', 'inactive', 'pending']),
                $this->faker->sentence,
            ]),
        ];
    }

    /**
     * Define factory state for finance request settings
     */
    public function financeRequestSettings(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'key' => $this->faker->randomElement([
                    'finance_request.amount.min',
                    'finance_request.amount.max',
                    'finance_request.repayment_period.min',
                    'finance_request.repayment_period.max',
                    'finance_request.breathing_period.min',
                    'finance_request.breathing_period.max',
                ]),
                'value' => $this->getFinanceRequestValue(),
            ];
        });
    }

    /**
     * Get appropriate value based on the finance request setting key
     */
    private function getFinanceRequestValue(): mixed
    {
        $key = $this->faker->randomElement([
            'finance_request.amount.min',
            'finance_request.amount.max',
            'finance_request.repayment_period.min',
            'finance_request.repayment_period.max',
            'finance_request.breathing_period.min',
            'finance_request.breathing_period.max',
        ]);

        switch ($key) {
            case 'finance_request.amount.min':
            case 'finance_request.amount.max':
                return $this->faker->numberBetween(1, 100); // Billions in millions
            case 'finance_request.repayment_period.min':
            case 'finance_request.repayment_period.max':
            case 'finance_request.breathing_period.min':
            case 'finance_request.breathing_period.max':
                return $this->faker->numberBetween(1, 24); // Months
            default:
                return $this->faker->numberBetween(1, 100);
        }
    }
}
