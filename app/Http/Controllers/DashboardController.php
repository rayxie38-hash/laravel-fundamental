<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Item;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            $stats = [
                'total_users' => User::count(),
                'total_categories' => Category::count(),
                'total_items' => Item::count(),
                'pending_loans' => Loan::where('status', 'pending')->count(),
                'active_loans' => Loan::whereIn('status', ['approved', 'overdue'])->count(),
            ];

            $recentActivities = ActivityLog::with('user')
                ->latest()
                ->take(5)
                ->get();

            $pendingLoans = Loan::with(['user', 'item'])
                ->where('status', 'pending')
                ->latest()
                ->take(5)
                ->get();

            // Monthly loan counts for chart (approved or returned loans)
            $monthlyLoans = Loan::select(
                DB::raw('MONTH(loan_date) as month_number'),
                DB::raw('DATE_FORMAT(loan_date, "%b %Y") as month_label'),
                DB::raw('COUNT(*) as total_loans')
            )
            ->whereIn('status', ['approved', 'returned', 'overdue'])
            ->groupBy('month_number', 'month_label')
            ->orderBy('month_number', 'asc')
            ->get();

            $chartLabels = $monthlyLoans->pluck('month_label')->toArray();
            $chartData = $monthlyLoans->pluck('total_loans')->toArray();

            if (empty($chartLabels)) {
                $chartLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
                $chartData = [0, 0, 3, 0, 0, 0];
            }

            return view('dashboard', compact('stats', 'recentActivities', 'pendingLoans', 'chartLabels', 'chartData'));
        }

        // User View
        $stats = [
            'my_loans_count' => Loan::where('user_id', $user->id)->count(),
            'my_active_loans' => Loan::where('user_id', $user->id)->whereIn('status', ['approved', 'overdue'])->count(),
            'my_pending_loans' => Loan::where('user_id', $user->id)->where('status', 'pending')->count(),
            'available_items' => Item::where('stock', '>', 0)->count(),
        ];

        $myRecentLoans = Loan::with('item')
            ->where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact('stats', 'myRecentLoans'));
    }
}
