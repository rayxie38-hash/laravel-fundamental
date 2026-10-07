<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->get('user_id');
        $action = $request->get('action');

        $logs = ActivityLog::with('user')
            ->when($userId, function ($q, $userId) {
                return $q->where('user_id', $userId);
            })
            ->when($action, function ($q, $action) {
                return $q->where('action', 'like', '%' . $action . '%');
            })
            ->latest()
            ->paginate(15);

        $users = User::orderBy('name')->get();

        return view('activity-logs.index', compact('logs', 'users', 'userId', 'action'));
    }
}
