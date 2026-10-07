<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('User Account Management') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ showAddModal: false, editUser: null }">
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
                    <form method="GET" action="{{ route('users.index') }}" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama atau email..." class="rounded-md border-gray-300 text-sm px-3 py-2 w-full md:w-64">
                        <select name="role" class="rounded-md border-gray-300 text-sm px-3 py-2">
                            <option value="">Semua Role</option>
                            <option value="admin" {{ $role == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="user" {{ $role == 'user' ? 'selected' : '' }}>User</option>
                        </select>
                        <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-md text-sm hover:bg-gray-700">Filter</button>
                        @if($search || $role)
                            <a href="{{ route('users.index') }}" class="text-sm text-gray-500 hover:text-gray-700 underline">Reset</a>
                        @endif
                    </form>

                    <button @click="showAddModal = true" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-md text-sm whitespace-nowrap">
                        + Add New Account
                    </button>
                </div>

                <!-- Users Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600">
                        <thead class="bg-gray-50 text-gray-700 font-semibold border-b">
                            <tr>
                                <th class="p-3">#</th>
                                <th class="p-3">Name</th>
                                <th class="p-3">Email</th>
                                <th class="p-3">Role</th>
                                <th class="p-3">Created At</th>
                                <th class="p-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse($users as $user)
                                <tr>
                                    <td class="p-3 font-semibold text-gray-500">#{{ $user->id }}</td>
                                    <td class="p-3 font-medium text-gray-900">{{ $user->name }}</td>
                                    <td class="p-3">{{ $user->email }}</td>
                                    <td class="p-3">
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $user->isAdmin() ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td class="p-3 text-xs text-gray-500">{{ $user->created_at->format('d M Y') }}</td>
                                    <td class="p-3 text-right">
                                        <div class="flex justify-end gap-2">
                                            <button @click="editUser = {{ json_encode($user) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">Edit</button>

                                            @if($user->id !== Auth::id())
                                                <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Are you sure you want to delete this user account?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900 font-medium">Delete</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-4 text-center text-gray-500">No user accounts found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $users->links() }}
                </div>
            </div>

        </div>

        <!-- Add User Modal -->
        <div x-show="showAddModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center p-4 z-50" style="display: none;">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                <div class="flex justify-between items-center mb-4 border-b pb-2">
                    <h3 class="text-lg font-bold text-gray-900">Add New Account</h3>
                    <button @click="showAddModal = false" class="text-gray-400 hover:text-gray-600">&times;</button>
                </div>
                <form method="POST" action="{{ route('users.store') }}">
                    @csrf
                    <div class="space-y-4 text-sm">
                        <div>
                            <label class="block font-medium text-gray-700">Full Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" required class="w-full rounded-md border-gray-300 text-sm mt-1" placeholder="Nama lengkap penggunana...">
                        </div>
                        <div>
                            <label class="block font-medium text-gray-700">Email Address <span class="text-red-500">*</span></label>
                            <input type="email" name="email" required class="w-full rounded-md border-gray-300 text-sm mt-1" placeholder="alamat.email@school.com">
                        </div>
                        <div>
                            <label class="block font-medium text-gray-700">Password <span class="text-red-500">*</span></label>
                            <input type="password" name="password" required class="w-full rounded-md border-gray-300 text-sm mt-1" placeholder="Minimal 8 karakter">
                        </div>
                        <div>
                            <label class="block font-medium text-gray-700">Role <span class="text-red-500">*</span></label>
                            <select name="role" required class="w-full rounded-md border-gray-300 text-sm mt-1">
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end gap-2">
                        <button type="button" @click="showAddModal = false" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md text-sm">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700">Save Account</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit User Modal -->
        <template x-if="editUser">
            <div class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center p-4 z-50">
                <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                    <div class="flex justify-between items-center mb-4 border-b pb-2">
                        <h3 class="text-lg font-bold text-gray-900">Edit User Account</h3>
                        <button @click="editUser = null" class="text-gray-400 hover:text-gray-600">&times;</button>
                    </div>
                    <form method="POST" :action="`/users/${editUser.id}`">
                        @csrf
                        @method('PUT')
                        <div class="space-y-4 text-sm">
                            <div>
                                <label class="block font-medium text-gray-700">Full Name <span class="text-red-500">*</span></label>
                                <input type="text" name="name" :value="editUser.name" required placeholder="Nama lengkap..." class="w-full rounded-md border-gray-300 text-sm mt-1">
                            </div>
                            <div>
                                <label class="block font-medium text-gray-700">Email Address <span class="text-red-500">*</span></label>
                                <input type="email" name="email" :value="editUser.email" required placeholder="Alamat email..." class="w-full rounded-md border-gray-300 text-sm mt-1">
                            </div>
                            <div>
                                <label class="block font-medium text-gray-700">New Password (kosongkan jika tidak diubah)</label>
                                <input type="password" name="password" placeholder="Password baru..." class="w-full rounded-md border-gray-300 text-sm mt-1">
                            </div>
                            <div>
                                <label class="block font-medium text-gray-700">Role <span class="text-red-500">*</span></label>
                                <select name="role" required class="w-full rounded-md border-gray-300 text-sm mt-1">
                                    <option value="user" :selected="editUser.role == 'user'">User</option>
                                    <option value="admin" :selected="editUser.role == 'admin'">Admin</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end gap-2">
                            <button type="button" @click="editUser = null" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md text-sm">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700">Update Account</button>
                        </div>
                    </form>
                </div>
            </div>
        </template>

    </div>
</x-app-layout>
