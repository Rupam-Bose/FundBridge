<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Seed an admin user and some demo data for testing.
     * Run with: php artisan db:seed --class=AdminSeeder
     */
    public function run(): void
    {
        // ── Admin User ──────────────────────────────────────────
        $admin = \App\Models\User::updateOrCreate(
            ['email' => 'admin@fundbridge.com'],
            [
                'name'         => 'FundBridge Admin',
                'password'     => Hash::make('admin1234'),
                'role'         => 'admin',
                'company_name' => 'FundBridge Inc.',
            ]
        );

        $this->command->info("Admin created: admin@fundbridge.com / admin1234");

        // ── Demo Founder ────────────────────────────────────────
        $founder = \App\Models\User::updateOrCreate(
            ['email' => 'founder@fundbridge.com'],
            [
                'name'         => 'Alex Founder',
                'password'     => Hash::make('password123'),
                'role'         => 'founder',
                'company_name' => 'GreenTech Solutions',
            ]
        );

        // ── Demo Investor ────────────────────────────────────────
        $investor = \App\Models\User::updateOrCreate(
            ['email' => 'investor@fundbridge.com'],
            [
                'name'         => 'Rachel Investor',
                'password'     => Hash::make('password123'),
                'role'         => 'investor',
                'company_name' => 'Capital Ventures',
            ]
        );

        // ── Demo Ventures ────────────────────────────────────────
        $ventures = [
            [
                'user_id'       => $founder->id,
                'title'         => 'GreenEnergy AI',
                'description'   => 'AI-powered platform for optimizing renewable energy consumption across smart grids.',
                'sector'        => 'CleanTech',
                'stage'         => 'Series A',
                'goal_amount'   => 1200000,
                'raised_amount' => 780000,
                'status'        => 'active',
                'views'         => 342,
            ],
            [
                'user_id'       => $founder->id,
                'title'         => 'HealthBridge App',
                'description'   => 'Connecting patients with specialists via telemedicine in underserved regions.',
                'sector'        => 'HealthTech',
                'stage'         => 'Seed',
                'goal_amount'   => 800000,
                'raised_amount' => 410000,
                'status'        => 'active',
                'views'         => 215,
            ],
            [
                'user_id'       => $founder->id,
                'title'         => 'EduPlatform Pro',
                'description'   => 'Adaptive learning management system for universities with AI-driven curriculum.',
                'sector'        => 'EdTech',
                'stage'         => 'Pre-Seed',
                'goal_amount'   => 500000,
                'raised_amount' => 500000,
                'status'        => 'completed',
                'views'         => 289,
            ],
        ];

        foreach ($ventures as $vData) {
            $venture = \App\Models\Venture::updateOrCreate(
                ['title' => $vData['title'], 'user_id' => $vData['user_id']],
                $vData
            );

            // Add a campaign for each active venture
            if ($venture->status === 'active') {
                \App\Models\Campaign::updateOrCreate(
                    ['venture_id' => $venture->id, 'title' => $venture->title . ' — Round 1'],
                    [
                        'venture_id'  => $venture->id,
                        'title'       => $venture->title . ' — Round 1',
                        'description' => 'Initial funding round.',
                        'goal'        => $venture->goal_amount,
                        'raised'      => $venture->raised_amount,
                        'deadline'    => now()->addMonths(3),
                        'status'      => 'active',
                    ]
                );
            }

            // Investor interest
            \App\Models\InvestorInterest::updateOrCreate(
                ['investor_id' => $investor->id, 'venture_id' => $venture->id],
                ['interest_level' => ['low','medium','high'][rand(0,2)], 'note' => 'Looks promising.']
            );
        }

        $this->command->info("Demo data seeded successfully!");
        $this->command->line("  Founder: founder@fundbridge.com / password123");
        $this->command->line("  Investor: investor@fundbridge.com / password123");
    }
}
