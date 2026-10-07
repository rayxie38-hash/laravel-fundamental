<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Http\Requests\StoreLoanRequest;
use App\Models\Item;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $status = $request->get('status');

        $query = Loan::with(['user', 'item.category', 'returnLog'])
            ->when($status, function ($q, $status) {
                return $q->where('status', $status);
            });

        if ($user->isUser()) {
            $query->where('user_id', $user->id);
        }

        $loans = $query->latest()->paginate(10);
        $items = Item::where('stock', '>', 0)->get();

        return view('loans.index', compact('loans', 'items', 'status'));
    }

    public function store(StoreLoanRequest $request)
    {
        $validated = $request->validated();
        $item = Item::findOrFail($validated['item_id']);

        if ($item->stock < $validated['quantity']) {
            return redirect()->back()->with('error', 'Requested quantity exceeds available stock (' . number_format($item->stock, 0, ',', '.') . ').');
        }

        $loan = Loan::create([
            'user_id' => $request->user()->id,
            'item_id' => $item->id,
            'quantity' => $validated['quantity'],
            'loan_date' => $validated['loan_date'],
            'due_date' => $validated['due_date'],
            'status' => 'pending',
        ]);

        ActivityLogger::log(
            'REQUEST_LOAN',
            "Requested loan of {$loan->quantity} unit(s) of '{$item->name}' (Loan ID: {$loan->id})."
        );

        return redirect()->route('loans.index')->with('success', 'Loan request submitted successfully.');
    }

    public function approve(Request $request, Loan $loan)
    {
        if ($loan->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending loans can be approved.');
        }

        DB::transaction(function () use ($loan, $request) {
            $item = $loan->item;

            if ($item->stock < $loan->quantity) {
                throw new \Exception('Insufficient item stock to approve this loan.');
            }

            $item->decrement('stock', $loan->quantity);

            $loan->update([
                'status' => 'approved',
            ]);

            ActivityLogger::log(
                'APPROVE_LOAN',
                "Approved loan ID {$loan->id} for user '{$loan->user->name}' (Item: {$item->name}, Qty: {$loan->quantity})."
            );
        });

        return redirect()->route('loans.index')->with('success', 'Loan request approved successfully.');
    }

    public function reject(Request $request, Loan $loan)
    {
        if ($loan->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending loans can be rejected.');
        }

        $loan->update([
            'status' => 'rejected',
        ]);

        ActivityLogger::log(
            'REJECT_LOAN',
            "Rejected loan ID {$loan->id} for user '{$loan->user->name}'."
        );

        return redirect()->route('loans.index')->with('success', 'Loan request rejected.');
    }

    public function destroy(Request $request, Loan $loan)
    {
        $loanId = $loan->id;
        $loan->delete(); // Soft delete

        ActivityLogger::log(
            'DELETE_LOAN',
            "Soft-deleted loan ID {$loanId}."
        );

        return redirect()->route('loans.index')->with('success', 'Loan record deleted.');
    }
}
