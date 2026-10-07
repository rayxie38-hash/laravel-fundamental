<?php

namespace App\Console\Commands;

use App\Helpers\ActivityLogger;
use App\Models\Loan;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckOverdueLoansCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'loans:check-overdue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and update overdue loan statuses daily';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $today = Carbon::today();

        $overdueLoans = Loan::where('status', 'approved')
            ->whereDate('due_date', '<', $today)
            ->get();

        if ($overdueLoans->isEmpty()) {
            $this->info('No overdue loans found.');
            return Command::SUCCESS;
        }

        $count = 0;
        foreach ($overdueLoans as $loan) {
            DB::transaction(function () use ($loan, &$count) {
                $loan->update(['status' => 'overdue']);
                $count++;

                ActivityLogger::log(
                    'MARK_OVERDUE',
                    "Loan ID {$loan->id} marked as overdue (Due Date was {$loan->due_date->format('Y-m-d')}).",
                    $loan->user_id
                );
            });
        }

        $this->info("Successfully updated {$count} overdue loan(s) to 'overdue' status.");
        return Command::SUCCESS;
    }
}
