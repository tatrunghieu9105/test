<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $withTrashed = (bool) $request->query('with_trashed', false);
        $query = Category::query();
        if ($withTrashed) { $query->withTrashed(); }
        $categories = $query->orderBy('name')->paginate(10)->withQueryString();
        return view('admin.categories.index', compact('categories', 'withTrashed'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
        ]);
        $category = Category::create($data);
        return redirect()->route('admin.categories.index')->with('success', 'Tạo thể loại thành công');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
        ]);
        $category->update($data);
        return redirect()->route('admin.categories.index')->with('success', 'Cập nhật thể loại thành công');
    }

    public function destroy(Category $category)
    {
        if ($category->movies()->exists()) {
            return back()->with('error', 'Không thể xóa danh mục vì vẫn còn phim thuộc danh mục này');
        }
        
        $category->delete();
        return back()->with('success', 'Đã xóa danh mục thành công');
    }

    public function restore(string $id)
    {
        $category = Category::withTrashed()->findOrFail($id);
        $category->restore();
        return back()->with('success', 'Đã khôi phục');
    }
}
