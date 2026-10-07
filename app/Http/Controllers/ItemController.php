<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $categoryId = $request->get('category_id');

        $items = Item::with('category')
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('code', 'like', '%' . $search . '%');
                });
            })
            ->when($categoryId, function ($query, $categoryId) {
                return $query->where('category_id', $categoryId);
            })
            ->latest()
            ->paginate(10);

        $categories = Category::orderBy('name')->get();

        return view('items.index', compact('items', 'categories', 'search', 'categoryId'));
    }

    public function store(StoreItemRequest $request)
    {
        $item = Item::create($request->validated());

        ActivityLogger::log(
            'CREATE_ITEM',
            "Created item '{$item->name}' with code '{$item->code}' (Stock: {$item->stock})."
        );

        return redirect()->route('items.index')->with('success', 'Item added successfully.');
    }

    public function update(UpdateItemRequest $request, Item $item)
    {
        $item->update($request->validated());

        ActivityLogger::log(
            'UPDATE_ITEM',
            "Updated item '{$item->name}' (Code: {$item->code})."
        );

        return redirect()->route('items.index')->with('success', 'Item updated successfully.');
    }

    public function destroy(Request $request, Item $item)
    {
        $itemName = $item->name;
        $itemCode = $item->code;
        $item->delete();

        ActivityLogger::log(
            'DELETE_ITEM',
            "Deleted item '{$itemName}' (Code: {$itemCode})."
        );

        return redirect()->route('items.index')->with('success', 'Item deleted successfully.');
    }
}
