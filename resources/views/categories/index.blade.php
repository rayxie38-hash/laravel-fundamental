<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Category Management') }}
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

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Add Category Form -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 h-fit">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Add New Category</h3>
                    <form method="POST" action="{{ route('categories.store') }}">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Category Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="e.g. Electronics / Perangkat Elektronik">
                            @error('name')
                                <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-md text-sm transition">
                            Save Category
                        </button>
                    </form>
                </div>

                <!-- Category List Table -->
                <div class="md:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-900">Category List</h3>
                        <form method="GET" action="{{ route('categories.index') }}" class="flex gap-2">
                            <input type="text" name="search" value="{{ $search }}" placeholder="Search category name..." class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-1.5">
                            <button type="submit" class="bg-gray-800 text-white px-3 py-1.5 rounded-md text-sm hover:bg-gray-700">Search</button>
                        </form>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-gray-600">
                            <thead class="bg-gray-50 text-gray-700 font-semibold border-b">
                                <tr>
                                    <th class="p-3">#</th>
                                    <th class="p-3">Category Name</th>
                                    <th class="p-3">Total Items</th>
                                    <th class="p-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @forelse($categories as $category)
                                    <tr x-data="{ editing: false }">
                                        <td class="p-3">{{ number_format($loop->iteration + ($categories->currentPage() - 1) * $categories->perPage(), 0, ',', '.') }}</td>
                                        
                                        <td class="p-3">
                                            <span x-show="!editing" class="font-medium text-gray-900">{{ $category->name }}</span>
                                            <form x-show="editing" method="POST" action="{{ route('categories.update', $category) }}" class="flex gap-2">
                                                @csrf
                                                @method('PUT')
                                                <input type="text" name="name" value="{{ $category->name }}" required placeholder="Category name..." class="rounded-md border-gray-300 text-sm px-2 py-1">
                                                <button type="submit" class="bg-green-600 text-white px-2 py-1 rounded text-xs">Save</button>
                                                <button type="button" @click="editing = false" class="bg-gray-400 text-white px-2 py-1 rounded text-xs">Cancel</button>
                                            </form>
                                        </td>

                                        <td class="p-3">
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                                {{ number_format($category->items_count, 0, ',', '.') }} barang
                                            </span>
                                        </td>

                                        <td class="p-3 text-right">
                                            <div class="flex justify-end gap-2" x-show="!editing">
                                                <button @click="editing = true" class="text-indigo-600 hover:text-indigo-900 font-medium">Edit</button>
                                                <form method="POST" action="{{ route('categories.destroy', $category) }}" onsubmit="return confirm('Are you sure you want to delete this category?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900 font-medium">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="p-4 text-center text-gray-500">No categories found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $categories->links() }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
