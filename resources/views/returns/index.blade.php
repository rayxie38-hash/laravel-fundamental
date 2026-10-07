<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Return Logs & Verification') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ showReturnModal: false }">
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
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-gray-900">Returned Items Log</h3>
                    @if(Auth::user()->isAdmin())
                        <button @click="showReturnModal = true" class="bg-emerald-600 hover:bg-emerald-700 text-white font-medium py-2 px-4 rounded-md text-sm">
                            + Verify Item Return
                        </button>
                    @endif
                </div>

                <!-- Return Logs Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600">
                        <thead class="bg-gray-50 text-gray-700 font-semibold border-b">
                            <tr>
                                <th class="p-3">#</th>
                                <th class="p-3">Returned At</th>
                                <th class="p-3">Loan ID</th>
                                <th class="p-3">Borrower</th>
                                <th class="p-3">Item Name</th>
                                <th class="p-3">Condition on Return</th>
                                <th class="p-3">Notes</th>
                                @if(Auth::user()->isAdmin())
                                    <th class="p-3 text-right">Actions</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse($returnLogs as $log)
                                <tr>
                                    <td class="p-3 font-semibold text-gray-500">#{{ $log->id }}</td>
                                    <td class="p-3">{{ $log->returned_at->format('d M Y H:i') }} WIB</td>
                                    <td class="p-3 font-mono text-indigo-600">#{{ $log->loan_id }}</td>
                                    <td class="p-3 font-medium text-gray-900">{{ $log->loan->user->name ?? 'User' }}</td>
                                    <td class="p-3">{{ $log->loan->item->name ?? 'Item' }} (Qty: {{ number_format($log->loan->quantity ?? 1, 0, ',', '.') }})</td>
                                    <td class="p-3">
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                            {{ $log->condition_on_return }}
                                        </span>
                                    </td>
                                    <td class="p-3 text-xs text-gray-500 max-w-xs truncate">{{ $log->notes ?? '-' }}</td>
                                    @if(Auth::user()->isAdmin())
                                        <td class="p-3 text-right">
                                            <form method="POST" action="{{ route('returns.destroy', $log) }}" onsubmit="return confirm('Soft-delete this return log?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 text-xs font-medium">Delete</button>
                                            </form>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ Auth::user()->isAdmin() ? 8 : 7 }}" class="p-4 text-center text-gray-500">No return logs recorded yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $returnLogs->links() }}
                </div>
            </div>

        </div>

        <!-- Verify Return Modal (Admin) -->
        @if(Auth::user()->isAdmin())
            <div x-show="showReturnModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center p-4 z-50" style="display: none;">
                <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                    <div class="flex justify-between items-center mb-4 border-b pb-2">
                        <h3 class="text-lg font-bold text-gray-900">Verify Item Return</h3>
                        <button @click="showReturnModal = false" class="text-gray-400 hover:text-gray-600">&times;</button>
                    </div>
                    <form method="POST" action="{{ route('returns.store') }}">
                        @csrf
                        <div class="space-y-4 text-sm">
                            <div>
                                <label class="block font-medium text-gray-700">Select Approved Loan <span class="text-red-500">*</span></label>
                                <select name="loan_id" required class="w-full rounded-md border-gray-300 text-sm mt-1">
                                    <option value="">Pilih transaksi peminjaman yang disetujui</option>
                                    @foreach($approvedLoans as $loan)
                                        <option value="{{ $loan->id }}">
                                            #{{ $loan->id }} - {{ $loan->user->name }} ({{ $loan->item->name }}, Qty: {{ number_format($loan->quantity, 0, ',', '.') }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block font-medium text-gray-700">Returned Date & Time <span class="text-red-500">*</span></label>
                                <input type="datetime-local" name="returned_at" value="{{ date('Y-m-d\TH:i') }}" required class="w-full rounded-md border-gray-300 text-sm mt-1">
                            </div>
                            <div>
                                <label class="block font-medium text-gray-700">Condition on Return <span class="text-red-500">*</span></label>
                                <input type="text" name="condition_on_return" required class="w-full rounded-md border-gray-300 text-sm mt-1" placeholder="Contoh: Baik / Berfungsi Normal / Goresan Kecil">
                            </div>
                            <div>
                                <label class="block font-medium text-gray-700">Notes (Optional)</label>
                                <textarea name="notes" rows="2" class="w-full rounded-md border-gray-300 text-sm mt-1" placeholder="Catatan tambahan kondisi barang saat dikembalikan..."></textarea>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end gap-2">
                            <button type="button" @click="showReturnModal = false" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md text-sm">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-md text-sm hover:bg-emerald-700">Verify & Restore Stock</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

    </div>
</x-app-layout>
