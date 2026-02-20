<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::withCount('posts')
            ->latest()
            ->paginate(10);

        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:50|unique:categories',
            'description' => 'nullable|max:200'
        ]);

        Category::create($validated);

        return redirect()
            ->route('categories.index')
            ->with('success', 'カテゴリを作成しました');
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|max:50|unique:categories,name,' . $category->id,
            'description' => 'nullable|max:200'
        ]);

        $category->update($validated);
        return redirect()
            ->route('categories.index')
            ->with('success', 'カテゴリを更新しました');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        // 記事が紐づいている場合は削除できないようにする
        if ($category->posts()->exists()) {
            return back()->with('error', 'このカテゴリは記事が存在するため削除できません');
        }

        $category->delete();
        return redirect()
            ->route('categories.index')
            ->with('success', 'カテゴリを削除しました');
    }
}
