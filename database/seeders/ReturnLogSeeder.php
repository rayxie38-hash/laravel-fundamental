<?php

namespace Database\Seeders;

use App\Models\Loan;
use App\Models\ReturnLog;
use Illuminate\Database\Seeder;

class ReturnLogSeeder extends Seeder
{
    public function run(): void
    {
        $returnedLoans = Loan::where('status', 'returned')->take(3)->get();

        if ($returnedLoans->count() >= 1) {
            ReturnLog::create([
                'loan_id' => $returnedLoans[0]->id,
                'returned_at' => '2026-03-04 10:00:00',
                'condition_on_return' => 'Good',
                'notes' => 'Returned on time without damage.',
            ]);
        }

        if ($returnedLoans->count() >= 2) {
            ReturnLog::create([
                'loan_id' => $returnedLoans[1]->id,
                'returned_at' => '2026-03-05 14:30:00',
                'condition_on_return' => 'Good',
                'notes' => 'Returned in working condition.',
            ]);
        }

        if ($returnedLoans->count() >= 3) {
            ReturnLog::create([
                'loan_id' => $returnedLoans[2]->id,
                'returned_at' => '2026-03-06 09:15:00',
                'condition_on_return' => 'Fair',
                'notes' => 'Slight scratches on frame.',
            ]);
        }
    }
}
