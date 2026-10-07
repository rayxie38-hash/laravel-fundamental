<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Seeder;

class ActivityLogSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $user1 = User::where('email', 'budi@school.com')->first();

        ActivityLog::create([
            'user_id' => $admin->id,
            'action' => 'CREATE_ITEM',
            'description' => 'Admin created inventory items and categories.',
        ]);

        ActivityLog::create([
            'user_id' => $user1->id,
            'action' => 'REQUEST_LOAN',
            'description' => 'User Budi Santoso requested loan for LCD Projector EPSON.',
        ]);

        ActivityLog::create([
            'user_id' => $admin->id,
            'action' => 'APPROVE_LOAN',
            'description' => 'Admin approved loan for Siti Aminah.',
        ]);

        ActivityLog::create([
            'user_id' => $admin->id,
            'action' => 'VERIFY_RETURN',
            'description' => 'Admin verified return for loan.',
        ]);
    }
}
