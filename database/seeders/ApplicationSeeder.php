<?php

namespace Database\Seeders;

use App\Models\Application;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $applications = [
            'fixwad' => [
                'name' => 'Fixwad Studio',
                'types' => ['default', 'partner']
            ],
            'basicapp' => [
                'name' => 'Basic Application',
                'types' => ['default']
            ]
        ];

        foreach ($applications as $appKey => $appVal) {
            $application = Application::updateOrCreate(['app_key' => $appKey], [
                'name' => $appVal['name'],
                'app_key' => $appKey,
                'secret_key' => Str::random(50),
                'payment_gateway' => 'xendit'
            ]);

            foreach ($appVal['types'] as $appType) {
                $application->paymentTypes()->updateOrCreate([
                    'type' => $appType
                ], [
                    'name' => ucwords($appType),
                    'type' => $appType,
                    'webhook_url' => 'http://laravel-paysplitter.test'
                ]);
            }
        }
    }
}
