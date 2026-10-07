<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $role = $request->get('role');

        $users = User::when($search, function ($q, $search) {
                return $q->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                          ->orWhere('email', 'like', '%' . $search . '%');
                });
            })
            ->when($role, function ($q, $role) {
                return $q->where('role', $role);
            })
            ->latest()
            ->paginate(10);

        return view('users.index', compact('users', 'search', 'role'));
    }

    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();

        $newUser = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        ActivityLogger::log(
            'CREATE_USER',
            "Created user '{$newUser->name}' ({$newUser->email}) with role '{$newUser->role}'."
        );

        return redirect()->route('users.index')->with('success', 'User account created successfully.');
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $validated = $request->validated();

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        ActivityLogger::log(
            'UPDATE_USER',
            "Updated user '{$user->name}' (ID: {$user->id})."
        );

        return redirect()->route('users.index')->with('success', 'User account updated successfully.');
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        $activeLoansCount = $user->loans()
            ->whereIn('status', ['pending', 'approved', 'overdue'])
            ->count();

        if ($activeLoansCount > 0) {
            return redirect()->back()->with('error', 'User tidak dapat dihapus karena masih memiliki peminjaman aktif.');
        }

        $userName = $user->name;
        $user->delete();

        ActivityLogger::log(
            'DELETE_USER',
            "Deleted user account '{$userName}' (ID: {$user->id})."
        );

        return redirect()->route('users.index')->with('success', 'User account deleted successfully.');
    }
}
