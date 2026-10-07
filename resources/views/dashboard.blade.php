<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard System') }}
        </h2>
    </x-slot>

    <div class="py-6 space-y-6">

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
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-5 border-l-4 border-blue-500">
                    <div class="text-gray-500 text-xs font-bold uppercase tracking-wider">Total Users</div>
                    <div class="text-3xl font-extrabold text-gray-900 mt-2">{{ number_format($stats['total_users'], 0, ',', '.') }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-5 border-l-4 border-emerald-500">
                    <div class="text-gray-500 text-xs font-bold uppercase tracking-wider">Categories</div>
                    <div class="text-3xl font-extrabold text-gray-900 mt-2">{{ number_format($stats['total_categories'], 0, ',', '.') }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-5 border-l-4 border-indigo-500">
                    <div class="text-gray-500 text-xs font-bold uppercase tracking-wider">Total Items</div>
                    <div class="text-3xl font-extrabold text-gray-900 mt-2">{{ number_format($stats['total_items'], 0, ',', '.') }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-5 border-l-4 border-amber-500">
                    <div class="text-gray-500 text-xs font-bold uppercase tracking-wider">Pending Loans</div>
                    <div class="text-3xl font-extrabold text-gray-900 mt-2">{{ number_format($stats['pending_loans'], 0, ',', '.') }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-5 border-l-4 border-purple-500">
                    <div class="text-gray-500 text-xs font-bold uppercase tracking-wider">Active Loans</div>
                    <div class="text-3xl font-extrabold text-gray-900 mt-2">{{ number_format($stats['active_loans'], 0, ',', '.') }}</div>
                </div>
            </div>

            <!-- Chart & Tables Row -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Monthly Loan Statistics Chart -->
                <div class="lg:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Grafik Peminjaman Barang Per Bulan</h3>
                    <div class="h-64 relative">
                        <canvas id="monthlyLoanChart"></canvas>
                    </div>
                </div>

                <!-- Recent Activity Logs -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-900">Activity Logs Terbaru</h3>
                        <a href="{{ route('activity-logs.index') }}" class="text-xs font-semibold text-indigo-600 hover:underline">Lihat Semua →</a>
                    </div>
                    @if($recentActivities->isEmpty())
                        <p class="text-gray-500 text-sm">Belum ada aktivitas tercatat.</p>
                    @else
                        <ul class="divide-y text-sm">
                            @foreach($recentActivities as $act)
                                <li class="py-2.5">
                                    <div class="flex justify-between text-xs">
                                        <span class="font-semibold text-gray-800">{{ $act->user->name ?? 'System' }}</span>
                                        <span class="text-gray-400">{{ $act->created_at->format('d M H:i') }} WIB</span>
                                    </div>
                                    <p class="text-gray-600 text-xs mt-1">{{ $act->description }}</p>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <!-- Recent Pending Loans Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Permohonan Peminjaman Menunggu Verifikasi</h3>
                    <a href="{{ route('loans.index') }}" class="text-xs font-semibold text-indigo-600 hover:underline">Kelola Semua Peminjaman →</a>
                </div>
                @if($pendingLoans->isEmpty())
                    <p class="text-gray-500 text-sm">Tidak ada permohonan peminjaman yang perlu diverifikasi saat ini.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-gray-600">
                            <thead class="bg-gray-50 text-gray-700 font-semibold border-b">
                                <tr>
                                    <th class="p-3">Peminjam</th>
                                    <th class="p-3">Barang</th>
                                    <th class="p-3">Jumlah Unit</th>
                                    <th class="p-3">Tgl Pinjam</th>
                                    <th class="p-3">Tgl Kembali</th>
                                    <th class="p-3 text-right">Tindakan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach($pendingLoans as $loan)
                                    <tr>
                                        <td class="p-3 font-medium text-gray-900">{{ $loan->user->name }}</td>
                                        <td class="p-3">{{ $loan->item->name }}</td>
                                        <td class="p-3 font-bold">{{ number_format($loan->quantity, 0, ',', '.') }}</td>
                                        <td class="p-3">{{ $loan->loan_date->format('d M Y') }}</td>
                                        <td class="p-3">{{ $loan->due_date->format('d M Y') }}</td>
                                        <td class="p-3 text-right">
                                            <div class="flex justify-end gap-2">
                                                <form method="POST" action="{{ route('loans.approve', $loan) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="px-3 py-1 bg-green-600 text-white rounded text-xs font-semibold hover:bg-green-700">Approve</button>
                                                </form>
                                                <form method="POST" action="{{ route('loans.reject', $loan) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded text-xs font-semibold hover:bg-red-700">Reject</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- Include Chart.js -->
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    const ctx = document.getElementById('monthlyLoanChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: {!! json_encode($chartLabels) !!},
                            datasets: [{
                                label: 'Jumlah Peminjaman',
                                data: {!! json_encode($chartData) !!},
                                backgroundColor: 'rgba(79, 70, 229, 0.8)',
                                borderColor: 'rgba(79, 70, 229, 1)',
                                borderWidth: 1,
                                borderRadius: 6
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1,
                                        precision: 0
                                    }
                                }
                            }
                        }
                    });
                });
            </script>

        @else
            <!-- User Dashboard -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                    <div class="text-gray-500 text-xs font-bold uppercase tracking-wider">Total Pinjaman Saya</div>
                    <div class="text-3xl font-extrabold text-gray-900 mt-2">{{ number_format($stats['my_loans_count'], 0, ',', '.') }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-emerald-500">
                    <div class="text-gray-500 text-xs font-bold uppercase tracking-wider">Pinjaman Aktif</div>
                    <div class="text-3xl font-extrabold text-gray-900 mt-2">{{ number_format($stats['my_active_loans'], 0, ',', '.') }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-amber-500">
                    <div class="text-gray-500 text-xs font-bold uppercase tracking-wider">Menunggu Persetujuan</div>
                    <div class="text-3xl font-extrabold text-gray-900 mt-2">{{ number_format($stats['my_pending_loans'], 0, ',', '.') }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-purple-500">
                    <div class="text-gray-500 text-xs font-bold uppercase tracking-wider">Barang Tersedia</div>
                    <div class="text-3xl font-extrabold text-gray-900 mt-2">{{ number_format($stats['available_items'], 0, ',', '.') }}</div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mt-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Riwayat Peminjaman Terbaru Saya</h3>
                    <a href="{{ route('loans.index') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">Lihat Semua →</a>
                </div>
                @if($myRecentLoans->isEmpty())
                    <p class="text-gray-500 text-sm">Anda belum memiliki riwayat peminjaman barang.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-gray-600">
                            <thead class="bg-gray-50 text-gray-700 font-semibold border-b">
                                <tr>
                                    <th class="p-3">Nama Barang</th>
                                    <th class="p-3">Jumlah Unit</th>
                                    <th class="p-3">Tanggal Pinjam</th>
                                    <th class="p-3">Jatuh Tempo</th>
                                    <th class="p-3">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach($myRecentLoans as $loan)
                                    <tr>
                                        <td class="p-3 font-medium text-gray-900">{{ $loan->item->name }}</td>
                                        <td class="p-3 font-bold">{{ number_format($loan->quantity, 0, ',', '.') }}</td>
                                        <td class="p-3">{{ $loan->loan_date->format('d M Y') }}</td>
                                        <td class="p-3">{{ $loan->due_date->format('d M Y') }}</td>
                                        <td class="p-3">
                                            <span class="px-2.5 py-1 text-xs rounded-full font-medium 
                                                {{ $loan->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $loan->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $loan->status === 'returned' ? 'bg-blue-100 text-blue-800' : '' }}
                                                {{ $loan->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}
                                                {{ $loan->status === 'overdue' ? 'bg-red-800 text-white font-bold' : '' }}">
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
</x-app-layout>
