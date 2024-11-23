<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return view('learning.categories.index', compact('categories'));
    }

    public function manage()
    {
        $categories = Category::all();
        return view('learning.categories.manage', compact('categories'));
    }

    public function create()
    {
        return view('learning.categories.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:30|unique:categories',
        ]);

        $category = new Category();
        $category->name = $request->name;
        Storage::disk('public')->makeDirectory('videos/' . $category->name);
        $category->save();

        return redirect()->route('categories.manage');
    }

    public function edit(Category $category)
    {
        return view('learning.categories.form', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:30',
        ]);

        $originalCategoryName = $category->name;

        $category->name = $request->name;
        $category->save();

        if ($originalCategoryName !== $category->name) {
            Storage::disk('public')->move('videos/' . $originalCategoryName, 'videos/' . $category->name);
        }

        return redirect()->route('categories.manage');
    }

    public function destroy(Category $category)
    {

        Storage::disk('public')->deleteDirectory('videos/' . $category->name);

        $category->delete();

        return redirect()->route('categories.manage');
    }

    public function show(Category $category)
    {
        return view('learning.categories.show', compact('category'));
    }

}
