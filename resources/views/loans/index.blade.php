<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Loan Records') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ showNewLoanModal: false }">
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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <!-- Toolbar -->
                <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
                    <form method="GET" action="{{ route('loans.index') }}" class="flex items-center gap-3 w-full md:w-auto">
                        <select name="status" class="rounded-md border-gray-300 text-sm px-3 py-2">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ $status == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="returned" {{ $status == 'returned' ? 'selected' : '' }}>Returned</option>
                            <option value="rejected" {{ $status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                        <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-md text-sm hover:bg-gray-700">Filter</button>
                        @if($status)
                            <a href="{{ route('loans.index') }}" class="text-sm text-gray-500 hover:text-gray-700 underline">Reset</a>
                        @endif
                    </form>

                    @if(Auth::user()->isUser())
                        <button @click="showNewLoanModal = true" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-md text-sm whitespace-nowrap">
                            + New Loan Request
                        </button>
                    @endif
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600">
                        <thead class="bg-gray-50 text-gray-700 font-semibold border-b">
                            <tr>
                                <th class="p-3">#</th>
                                @if(Auth::user()->isAdmin())
                                    <th class="p-3">Borrower (User)</th>
                                @endif
                                <th class="p-3">Item Name</th>
                                <th class="p-3">Qty</th>
                                <th class="p-3">Loan Date</th>
                                <th class="p-3">Due Date</th>
                                <th class="p-3">Status</th>
                                <th class="p-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse($loans as $loan)
                                <tr>
                                    <td class="p-3 font-semibold text-gray-500">#{{ $loan->id }}</td>
                                    @if(Auth::user()->isAdmin())
                                        <td class="p-3 font-medium text-gray-900">
                                            {{ $loan->user->name ?? 'Deleted User' }}
                                            <div class="text-xs text-gray-400">{{ $loan->user->email ?? '' }}</div>
                                        </td>
                                    @endif
                                    <td class="p-3">
                                        <div class="font-medium text-gray-900">{{ $loan->item->name ?? 'Deleted Item' }}</div>
                                        <div class="text-xs text-gray-500">{{ $loan->item->code ?? '' }}</div>
                                    </td>
                                    <td class="p-3 font-bold">{{ number_format($loan->quantity, 0, ',', '.') }}</td>
                                    <td class="p-3">{{ $loan->loan_date->format('d M Y') }}</td>
                                    <td class="p-3">{{ $loan->due_date->format('d M Y') }}</td>
                                    <td class="p-3">
                                        <span class="px-2.5 py-1 text-xs rounded-full font-medium 
                                            {{ $loan->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $loan->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $loan->status === 'returned' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $loan->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                                            {{ ucfirst($loan->status) }}
                                        </span>
                                    </td>
                                    <td class="p-3 text-right">
                                        <div class="flex justify-end gap-1">
                                            @if(Auth::user()->isAdmin() && $loan->status === 'pending')
                                                <form method="POST" action="{{ route('loans.approve', $loan) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="px-2.5 py-1 bg-green-600 text-white rounded text-xs hover:bg-green-700">Approve</button>
                                                </form>
                                                <form method="POST" action="{{ route('loans.reject', $loan) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="px-2.5 py-1 bg-red-600 text-white rounded text-xs hover:bg-red-700">Reject</button>
                                                </form>
                                            @endif

                                            @if(Auth::user()->isAdmin())
                                                <form method="POST" action="{{ route('loans.destroy', $loan) }}" onsubmit="return confirm('Soft-delete this loan record?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="px-2 py-1 text-red-600 hover:text-red-900 text-xs font-medium">Delete</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ Auth::user()->isAdmin() ? 8 : 7 }}" class="p-4 text-center text-gray-500">No loan records found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $loans->links() }}
                </div>
            </div>

        </div>

        <!-- Request Loan Modal (User) -->
        @if(Auth::user()->isUser())
            <div x-show="showNewLoanModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center p-4 z-50" style="display: none;">
                <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                    <div class="flex justify-between items-center mb-4 border-b pb-2">
                        <h3 class="text-lg font-bold text-gray-900">New Loan Request</h3>
                        <button @click="showNewLoanModal = false" class="text-gray-400 hover:text-gray-600">&times;</button>
                    </div>
                    <form method="POST" action="{{ route('loans.store') }}">
                        @csrf
                        <div class="space-y-4 text-sm">
                            <div>
                                <label class="block font-medium text-gray-700">Select Item <span class="text-red-500">*</span></label>
                                <select name="item_id" required class="w-full rounded-md border-gray-300 text-sm mt-1">
                                    <option value="">Pilih barang yang akan dipinjam</option>
                                    @foreach($items as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }} (Code: {{ $item->code }}, Stok Tersedia: {{ number_format($item->stock, 0, ',', '.') }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block font-medium text-gray-700">Quantity <span class="text-red-500">*</span></label>
                                <input type="number" name="quantity" min="1" value="1" required placeholder="Jumlah unit..." class="w-full rounded-md border-gray-300 text-sm mt-1">
                            </div>
                            <div>
                                <label class="block font-medium text-gray-700">Loan Date <span class="text-red-500">*</span></label>
                                <input type="date" name="loan_date" value="{{ date('Y-m-d') }}" required class="w-full rounded-md border-gray-300 text-sm mt-1">
                            </div>
                            <div>
                                <label class="block font-medium text-gray-700">Due Date <span class="text-red-500">*</span></label>
                                <input type="date" name="due_date" value="{{ date('Y-m-d', strtotime('+3 days')) }}" required class="w-full rounded-md border-gray-300 text-sm mt-1">
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end gap-2">
                            <button type="button" @click="showNewLoanModal = false" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md text-sm">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700">Submit Request</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

    </div>
</x-app-layout>
