<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Http\Requests\StoreReturnLogRequest;
use App\Models\Loan;
use App\Models\ReturnLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReturnLogController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = ReturnLog::with(['loan.user', 'loan.item']);

        if ($user->isUser()) {
            $query->whereHas('loan', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        $returnLogs = $query->latest()->paginate(10);
        $approvedLoans = Loan::with(['user', 'item'])
            ->where('status', 'approved')
            ->get();

        return view('returns.index', compact('returnLogs', 'approvedLoans'));
    }

    public function store(StoreReturnLogRequest $request)
    {
        $validated = $request->validated();
        $loan = Loan::with('item')->findOrFail($validated['loan_id']);

        if ($loan->status !== 'approved') {
            return redirect()->back()->with('error', 'Only approved loans can be marked as returned.');
        }

        DB::transaction(function () use ($loan, $validated, $request) {
            // Create return log
            $returnLog = ReturnLog::create([
                'loan_id' => $loan->id,
                'returned_at' => $validated['returned_at'],
                'condition_on_return' => $validated['condition_on_return'],
                'notes' => $validated['notes'] ?? null,
            ]);

            // Update loan status
            $loan->update([
                'status' => 'returned',
            ]);

            // Restore item stock
            $loan->item->increment('stock', $loan->quantity);

            // Log activity
            ActivityLogger::log(
                'VERIFY_RETURN',
                "Verified return for Loan ID {$loan->id} (Item: {$loan->item->name}, Qty: {$loan->quantity}, Condition: {$validated['condition_on_return']})."
            );
        });

        return redirect()->route('returns.index')->with('success', 'Return verified and stock updated successfully.');
    }

    public function destroy(Request $request, ReturnLog $returnLog)
    {
        $logId = $returnLog->id;
        $returnLog->delete(); // Soft delete

        ActivityLogger::log(
            'DELETE_RETURN_LOG',
            "Soft-deleted Return Log ID {$logId}."
        );

        return redirect()->route('returns.index')->with('success', 'Return log record deleted.');
    }
}
