<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Item;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Http\Request;

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
                'active_loans' => Loan::where('status', 'approved')->count(),
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

            return view('dashboard', compact('stats', 'recentActivities', 'pendingLoans'));
        }

        // User View
        $stats = [
            'my_loans_count' => Loan::where('user_id', $user->id)->count(),
            'my_active_loans' => Loan::where('user_id', $user->id)->where('status', 'approved')->count(),
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
