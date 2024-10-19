<?php

namespace Database\Factories;

use App\Models\Application;
use App\Models\Transaction;
use App\Models\TransactionLog;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $application = Application::has('paymentTypes')->inRandomOrder()->first();
        $paymentType = $application->paymentTypes->random();
        $status = $this->faker->randomElement(['pending', 'paid', 'expired']);
        $paidAt = $status === 'paid' ? now() : null;
        $paymentMethod = $status === 'paid' ? $this->faker->randomElement(['credit_card', 'bank_transfer', 'paypal']) : null;
        $paymentAccount = $status === 'paid' ? $this->faker->numberBetween(1000000, 9999999) : null;

        return [
            'application_id' => $application->id,
            'payment_url' => $this->faker->url(),
            'invoice_number' => 'PS-' . strtoupper(Str::random(5)) . '-' . $this->faker->numberBetween(100, 999),
            'transaction_type' => $paymentType->type,
            'payment_method' => $paymentMethod,
            'payment_account' => $paymentAccount,
            'amount' => $this->faker->numberBetween(10000, 100000),
            'payment_gateway' => $this->faker->randomElement(['xendit', 'midtrans', 'doku']),
            'status' => $status,
            'paid_at' => $paidAt,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Transaction $transaction) {
            TransactionLog::create([
                'transaction_id' => $transaction->id,
                'status' => 'pending',
                'request_payload' => json_encode([
                    'data' => 'Initial pending transaction',
                    'timestamp' => now()->toISOString(),
                ]),
                'response_payload' => json_encode([
                    'message' => 'Pending log created',
                    'timestamp' => now()->toISOString(),
                ]),
            ]);

            if (in_array($transaction->status, ['paid', 'expired'])) {
                TransactionLog::create([
                    'transaction_id' => $transaction->id,
                    'status' => $transaction->status,
                    'request_payload' => json_encode([
                        'data' => 'Transaction ' . $transaction->status,
                        'timestamp' => now()->toISOString(),
                    ]),
                    'response_payload' => json_encode([
                        'message' => ucfirst($transaction->status) . ' log created',
                        'timestamp' => now()->toISOString(),
                    ]),
                ]);
            }
        });
    }
}
