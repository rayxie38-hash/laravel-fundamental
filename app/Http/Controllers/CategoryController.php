<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $categories = Category::withCount('items')
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', '%' . $search . '%');
            })
            ->latest()
            ->paginate(10);

        return view('categories.index', compact('categories', 'search'));
    }

    public function store(StoreCategoryRequest $request)
    {
        $category = Category::create($request->validated());

        ActivityLogger::log(
            'CREATE_CATEGORY',
            "Created category '{$category->name}' (ID: {$category->id})."
        );

        return redirect()->route('categories.index')->with('success', 'Category created successfully.');
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $oldName = $category->name;
        $category->update($request->validated());

        ActivityLogger::log(
            'UPDATE_CATEGORY',
            "Updated category name from '{$oldName}' to '{$category->name}' (ID: {$category->id})."
        );

        return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(Request $request, Category $category)
    {
        if ($category->items()->count() > 0) {
            return redirect()->back()->with('error', 'Kategori tidak dapat dihapus karena masih memiliki barang terikat.');
        }

        $categoryName = $category->name;
        $category->delete();

        ActivityLogger::log(
            'DELETE_CATEGORY',
            "Deleted category '{$categoryName}' (ID: {$category->id})."
        );

        return redirect()->route('categories.index')->with('success', 'Category deleted successfully.');
    }
}
