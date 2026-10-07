<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Inventory Items') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ showAddModal: false, showRequestModal: false, requestItem: null, editItem: null }">
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
                <!-- Top Toolbar -->
                <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
                    <form method="GET" action="{{ route('items.index') }}" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama atau kode barang..." class="rounded-md border-gray-300 text-sm px-3 py-2 w-full md:w-64">
                        <select name="category_id" class="rounded-md border-gray-300 text-sm px-3 py-2">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-md text-sm hover:bg-gray-700">Filter</button>
                        @if($search || $categoryId)
                            <a href="{{ route('items.index') }}" class="text-sm text-gray-500 hover:text-gray-700 underline">Reset</a>
                        @endif
                    </form>

                    @if(Auth::user()->isAdmin())
                        <button @click="showAddModal = true" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-md text-sm whitespace-nowrap">
                            + Add New Item
                        </button>
                    @endif
                </div>

                <!-- Items Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600">
                        <thead class="bg-gray-50 text-gray-700 font-semibold border-b">
                            <tr>
                                <th class="p-3">Code</th>
                                <th class="p-3">Item Name</th>
                                <th class="p-3">Category</th>
                                <th class="p-3">Stock</th>
                                <th class="p-3">Condition</th>
                                <th class="p-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse($items as $item)
                                <tr>
                                    <td class="p-3 font-mono text-indigo-600 font-semibold">{{ $item->code }}</td>
                                    <td class="p-3 font-medium text-gray-900">{{ $item->name }}</td>
                                    <td class="p-3">
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                            {{ $item->category->name ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="p-3">
                                        <span class="font-bold {{ $item->stock > 0 ? 'text-green-600' : 'text-red-600' }}">
                                            {{ number_format($item->stock, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="p-3">{{ $item->condition }}</td>
                                    <td class="p-3 text-right">
                                        <div class="flex justify-end gap-2">
                                            @if(Auth::user()->isUser() && $item->stock > 0)
                                                <button @click="requestItem = {{ json_encode($item) }}; showRequestModal = true" class="px-3 py-1 bg-indigo-600 text-white rounded text-xs hover:bg-indigo-700">
                                                    Request Loan
                                                </button>
                                            @endif

                                            @if(Auth::user()->isAdmin())
                                                <button @click="editItem = {{ json_encode($item) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">Edit</button>
                                                <form method="POST" action="{{ route('items.destroy', $item) }}" onsubmit="return confirm('Are you sure you want to delete this item?');">
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
                                    <td colspan="6" class="p-4 text-center text-gray-500">No inventory items found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $items->links() }}
                </div>
            </div>

        </div>

        <!-- Add Item Modal (Admin) -->
        <div x-show="showAddModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center p-4 z-50" style="display: none;">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                <div class="flex justify-between items-center mb-4 border-b pb-2">
                    <h3 class="text-lg font-bold text-gray-900">Add New Item</h3>
                    <button @click="showAddModal = false" class="text-gray-400 hover:text-gray-600">&times;</button>
                </div>
                <form method="POST" action="{{ route('items.store') }}">
                    @csrf
                    <div class="space-y-4 text-sm">
                        <div>
                            <label class="block font-medium text-gray-700">Category <span class="text-red-500">*</span></label>
                            <select name="category_id" required class="w-full rounded-md border-gray-300 text-sm mt-1">
                                <option value="">Pilih Kategori</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block font-medium text-gray-700">Item Code <span class="text-red-500">*</span></label>
                            <input type="text" name="code" required class="w-full rounded-md border-gray-300 text-sm mt-1" placeholder="Contoh: ELEC-007">
                        </div>
                        <div>
                            <label class="block font-medium text-gray-700">Item Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" required class="w-full rounded-md border-gray-300 text-sm mt-1" placeholder="Contoh: Wireless Mouse Logi">
                        </div>
                        <div>
                            <label class="block font-medium text-gray-700">Stock <span class="text-red-500">*</span></label>
                            <input type="number" name="stock" min="0" required class="w-full rounded-md border-gray-300 text-sm mt-1" value="1" placeholder="Masukkan jumlah stok">
                        </div>
                        <div>
                            <label class="block font-medium text-gray-700">Condition <span class="text-red-500">*</span></label>
                            <input type="text" name="condition" required class="w-full rounded-md border-gray-300 text-sm mt-1" placeholder="Contoh: Baik / Sangat Baik / Cukup">
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end gap-2">
                        <button type="button" @click="showAddModal = false" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md text-sm">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700">Save Item</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Item Modal (Admin) -->
        <template x-if="editItem">
            <div class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center p-4 z-50">
                <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                    <div class="flex justify-between items-center mb-4 border-b pb-2">
                        <h3 class="text-lg font-bold text-gray-900">Edit Item</h3>
                        <button @click="editItem = null" class="text-gray-400 hover:text-gray-600">&times;</button>
                    </div>
                    <form method="POST" :action="`/items/${editItem.id}`">
                        @csrf
                        @method('PUT')
                        <div class="space-y-4 text-sm">
                            <div>
                                <label class="block font-medium text-gray-700">Category <span class="text-red-500">*</span></label>
                                <select name="category_id" required class="w-full rounded-md border-gray-300 text-sm mt-1">
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" :selected="editItem.category_id == {{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block font-medium text-gray-700">Item Code <span class="text-red-500">*</span></label>
                                <input type="text" name="code" :value="editItem.code" required placeholder="Kode barang..." class="w-full rounded-md border-gray-300 text-sm mt-1">
                            </div>
                            <div>
                                <label class="block font-medium text-gray-700">Item Name <span class="text-red-500">*</span></label>
                                <input type="text" name="name" :value="editItem.name" required placeholder="Nama barang..." class="w-full rounded-md border-gray-300 text-sm mt-1">
                            </div>
                            <div>
                                <label class="block font-medium text-gray-700">Stock <span class="text-red-500">*</span></label>
                                <input type="number" name="stock" min="0" :value="editItem.stock" required placeholder="Jumlah stok..." class="w-full rounded-md border-gray-300 text-sm mt-1">
                            </div>
                            <div>
                                <label class="block font-medium text-gray-700">Condition <span class="text-red-500">*</span></label>
                                <input type="text" name="condition" :value="editItem.condition" required placeholder="Kondisi barang..." class="w-full rounded-md border-gray-300 text-sm mt-1">
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end gap-2">
                            <button type="button" @click="editItem = null" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md text-sm">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700">Update Item</button>
                        </div>
                    </form>
                </div>
            </div>
        </template>

        <!-- Request Loan Modal (User) -->
        <template x-if="requestItem">
            <div class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center p-4 z-50">
                <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                    <div class="flex justify-between items-center mb-4 border-b pb-2">
                        <h3 class="text-lg font-bold text-gray-900">Request Loan: <span x-text="requestItem.name"></span></h3>
                        <button @click="requestItem = null" class="text-gray-400 hover:text-gray-600">&times;</button>
                    </div>
                    <form method="POST" action="{{ route('loans.store') }}">
                        @csrf
                        <input type="hidden" name="item_id" :value="requestItem.id">
                        <div class="space-y-4 text-sm">
                            <div>
                                <label class="block font-medium text-gray-700">Quantity <span class="text-red-500">*</span> (Tersedia: <span class="font-bold text-green-600" x-text="requestItem.stock"></span>)</label>
                                <input type="number" name="quantity" min="1" :max="requestItem.stock" value="1" required placeholder="Jumlah yang dipinjam" class="w-full rounded-md border-gray-300 text-sm mt-1">
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
                            <button type="button" @click="requestItem = null" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md text-sm">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700">Submit Request</button>
                        </div>
                    </form>
                </div>
            </div>
        </template>

    </div>
</x-app-layout>
