<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        return Inertia::render('admin/CategoryManager', [
            'categories' => Category::latest()->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name'
        ]);

        Category::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name'])
        ]);

        return redirect()->back();
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->back();
    }
}
