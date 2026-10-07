<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistem Inventaris Sekolah</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-800">
    <div class="min-h-screen flex flex-col justify-between">
        <!-- Navigation -->
        <header class="bg-white border-b border-gray-100 shadow-sm sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-indigo-600 text-white rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                    <span class="font-bold text-xl text-gray-900">Sistem Inventaris Sekolah</span>
                </div>

                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-md shadow-sm transition">
                            Ke Dashboard ({{ Auth::user()->name }})
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 text-gray-700 hover:text-indigo-600 text-sm font-semibold transition">
                            Masuk / Login
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-md shadow-sm transition">
                                Daftar Akun
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 flex-1 flex flex-col justify-center items-center text-center">
            <div class="max-w-3xl">
                <span class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-xs font-semibold uppercase tracking-wider">
                    School Inventory Management System
                </span>
                <h1 class="text-4xl sm:text-5xl font-extrabold text-gray-900 mt-4 leading-tight">
                    Pengelolaan Inventaris & Peminjaman Barang Sekolah
                </h1>
                <p class="text-lg text-gray-600 mt-4">
                    Sistem informasi terintegrasi untuk mengelola fasilitas sekolah, kategori barang, permohonan peminjaman, serta pencatatan pengembalian secara efisien dan transparan.
                </p>

                <div class="mt-8 flex flex-wrap justify-center gap-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg shadow-md transition">
                            Buka Dashboard Sekarang
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg shadow-md transition">
                            Login Pengguna / Admin
                        </a>
                        <a href="{{ route('register') }}" class="px-6 py-3 bg-white border border-gray-300 hover:bg-gray-50 text-gray-800 font-bold rounded-lg shadow-sm transition">
                            Registrasi Akun Baru
                        </a>
                    @endauth
                </div>
            </div>

            <!-- Features Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-16 text-left w-full">
                <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm">
                    <div class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-lg flex items-center justify-center font-bold mb-3">
                        01
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Manajemen Kategori & Barang</h3>
                    <p class="text-sm text-gray-600 mt-2">
                        Admin dapat mengelola data barang, mencatat kondisi, kode unik barang, dan memperbarui jumlah stok secara mudah.
                    </p>
                </div>

                <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm">
                    <div class="w-10 h-10 bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center font-bold mb-3">
                        02
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Pengajuan & Verifikasi Peminjaman</h3>
                    <p class="text-sm text-gray-600 mt-2">
                        Siswa/User dapat melihat daftar stok barang yang tersedia dan mengajukan peminjaman secara mandiri.
                    </p>
                </div>

                <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm">
                    <div class="w-10 h-10 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center font-bold mb-3">
                        03
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Verifikasi Pengembalian & Audit Log</h3>
                    <p class="text-sm text-gray-600 mt-2">
                        Verifikasi pengembalian barang yang secara otomatis mengembalikan stok inventaris dan mencatat log riwayat aktivitas.
                    </p>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-100 py-6 text-center text-xs text-gray-500">
            &copy; {{ date('Y') }} Sistem Inventaris Sekolah. Developed with Laravel 11 & Tailwind CSS.
        </footer>
    </div>
</body>
</html>
