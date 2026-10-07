<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Database\Seeder;

class LoanSeeder extends Seeder
{
    public function run(): void
    {
        $budi = User::where('email', 'budi@school.com')->first();
        $siti = User::where('email', 'siti@school.com')->first();

        $projector = Item::where('code', 'ELEC-001')->first();
        $laptop = Item::where('code', 'ELEC-002')->first();
        $desk = Item::where('code', 'FURN-001')->first();
        $mic = Item::where('code', 'ELEC-003')->first();
        $camera = Item::where('code', 'ELEC-004')->first();

        Loan::create([
            'user_id' => $budi->id,
            'item_id' => $projector->id,
            'quantity' => 1,
            'loan_date' => '2026-03-01',
            'due_date' => '2026-03-05',
            'status' => 'returned',
        ]);

        Loan::create([
            'user_id' => $budi->id,
            'item_id' => $laptop->id,
            'quantity' => 1,
            'loan_date' => '2026-03-02',
            'due_date' => '2026-03-06',
            'status' => 'returned',
        ]);

        Loan::create([
            'user_id' => $siti->id,
            'item_id' => $desk->id,
            'quantity' => 2,
            'loan_date' => '2026-03-03',
            'due_date' => '2026-03-07',
            'status' => 'returned',
        ]);

        Loan::create([
            'user_id' => $siti->id,
            'item_id' => $mic->id,
            'quantity' => 1,
            'loan_date' => '2026-03-10',
            'due_date' => '2026-03-15',
            'status' => 'approved',
        ]);

        Loan::create([
            'user_id' => $budi->id,
            'item_id' => $camera->id,
            'quantity' => 1,
            'loan_date' => '2026-03-12',
            'due_date' => '2026-03-16',
            'status' => 'pending',
        ]);
    }
}
