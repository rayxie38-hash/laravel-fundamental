<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 bg-green-100 border border-green-400 text-green-700 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 bg-red-100 border border-red-400 text-red-700 rounded-md">
                    {{ session('error') }}
                </div>
            @endif

            @if(Auth::user()->isAdmin())
                <!-- Admin Dashboard Cards -->
                <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                        <div class="text-gray-500 text-sm font-medium uppercase">Total Users</div>
                        <div class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_users'] }}</div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-emerald-500">
                        <div class="text-gray-500 text-sm font-medium uppercase">Categories</div>
                        <div class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_categories'] }}</div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-indigo-500">
                        <div class="text-gray-500 text-sm font-medium uppercase">Total Items</div>
                        <div class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_items'] }}</div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-amber-500">
                        <div class="text-gray-500 text-sm font-medium uppercase">Pending Loans</div>
                        <div class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['pending_loans'] }}</div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-purple-500">
                        <div class="text-gray-500 text-sm font-medium uppercase">Active Loans</div>
                        <div class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['active_loans'] }}</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <!-- Pending Loans Table -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Recent Pending Loans</h3>
                        @if($pendingLoans->isEmpty())
                            <p class="text-gray-500 text-sm">No pending loan requests.</p>
                        @else
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm text-gray-600">
                                    <thead class="bg-gray-50 text-gray-700 font-semibold border-b">
                                        <tr>
                                            <th class="p-2">User</th>
                                            <th class="p-2">Item</th>
                                            <th class="p-2">Qty</th>
                                            <th class="p-2">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y">
                                        @foreach($pendingLoans as $loan)
                                            <tr>
                                                <td class="p-2">{{ $loan->user->name }}</td>
                                                <td class="p-2">{{ $loan->item->name }}</td>
                                                <td class="p-2">{{ $loan->quantity }}</td>
                                                <td class="p-2 flex gap-1">
                                                    <form method="POST" action="{{ route('loans.approve', $loan) }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="px-2 py-1 bg-green-600 text-white rounded text-xs hover:bg-green-700">Approve</button>
                                                    </form>
                                                    <form method="POST" action="{{ route('loans.reject', $loan) }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="px-2 py-1 bg-red-600 text-white rounded text-xs hover:bg-red-700">Reject</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    <!-- Recent Activities -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Recent Activity Logs</h3>
                        @if($recentActivities->isEmpty())
                            <p class="text-gray-500 text-sm">No activities recorded yet.</p>
                        @else
                            <ul class="divide-y text-sm">
                                @foreach($recentActivities as $act)
                                    <li class="py-2">
                                        <div class="flex justify-between">
                                            <span class="font-semibold text-gray-800">{{ $act->user->name ?? 'System' }}</span>
                                            <span class="text-xs text-gray-400">{{ $act->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="text-gray-600 text-xs mt-1">{{ $act->description }}</p>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            @else
                <!-- User Dashboard -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                        <div class="text-gray-500 text-sm font-medium uppercase">My Total Loans</div>
                        <div class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['my_loans_count'] }}</div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-emerald-500">
                        <div class="text-gray-500 text-sm font-medium uppercase">Active Loans</div>
                        <div class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['my_active_loans'] }}</div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-amber-500">
                        <div class="text-gray-500 text-sm font-medium uppercase">Pending Requests</div>
                        <div class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['my_pending_loans'] }}</div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-purple-500">
                        <div class="text-gray-500 text-sm font-medium uppercase">Available Items</div>
                        <div class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['available_items'] }}</div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mt-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-900">My Recent Loans</h3>
                        <a href="{{ route('loans.index') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">View All →</a>
                    </div>
                    @if($myRecentLoans->isEmpty())
                        <p class="text-gray-500 text-sm">You haven't requested any loans yet.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm text-gray-600">
                                <thead class="bg-gray-50 text-gray-700 font-semibold border-b">
                                    <tr>
                                        <th class="p-3">Item Name</th>
                                        <th class="p-3">Qty</th>
                                        <th class="p-3">Loan Date</th>
                                        <th class="p-3">Due Date</th>
                                        <th class="p-3">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y">
                                    @foreach($myRecentLoans as $loan)
                                        <tr>
                                            <td class="p-3 font-medium text-gray-900">{{ $loan->item->name }}</td>
                                            <td class="p-3">{{ $loan->quantity }}</td>
                                            <td class="p-3">{{ $loan->loan_date->format('Y-m-d') }}</td>
                                            <td class="p-3">{{ $loan->due_date->format('Y-m-d') }}</td>
                                            <td class="p-3">
                                                <span class="px-2.5 py-1 text-xs rounded-full font-medium 
                                                    {{ $loan->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                                    {{ $loan->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                    {{ $loan->status === 'returned' ? 'bg-blue-100 text-blue-800' : '' }}
                                                    {{ $loan->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                                                    {{ ucfirst($loan->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
