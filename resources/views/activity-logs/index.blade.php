<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('System Activity Logs') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <!-- Filter Form -->
                <form method="GET" action="{{ route('activity-logs.index') }}" class="flex flex-wrap items-center gap-3 mb-6">
                    <select name="user_id" class="rounded-md border-gray-300 text-sm px-3 py-2">
                        <option value="">All Users</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ $userId == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->role }})</option>
                        @endforeach
                    </select>
                    <input type="text" name="action" value="{{ $action }}" placeholder="Filter by action (e.g. CREATE)..." class="rounded-md border-gray-300 text-sm px-3 py-2">
                    <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-md text-sm hover:bg-gray-700">Filter</button>
                    @if($userId || $action)
                        <a href="{{ route('activity-logs.index') }}" class="text-sm text-gray-500 hover:text-gray-700 underline">Reset</a>
                    @endif
                </form>

                <!-- Activity Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600">
                        <thead class="bg-gray-50 text-gray-700 font-semibold border-b">
                            <tr>
                                <th class="p-3">Timestamp</th>
                                <th class="p-3">User</th>
                                <th class="p-3">Action</th>
                                <th class="p-3">Description</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse($logs as $log)
                                <tr>
                                    <td class="p-3 text-xs text-gray-500 whitespace-nowrap">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                    <td class="p-3 font-medium text-gray-900">
                                        {{ $log->user->name ?? 'System' }}
                                        <div class="text-xs text-gray-400">{{ $log->user->email ?? '' }}</div>
                                    </td>
                                    <td class="p-3">
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-mono font-semibold bg-gray-100 text-gray-800">
                                            {{ $log->action }}
                                        </span>
                                    </td>
                                    <td class="p-3 text-gray-700">{{ $log->description }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-4 text-center text-gray-500">No activity logs recorded.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $logs->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
