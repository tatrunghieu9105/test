<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Combo;
use Illuminate\Http\Request;

class ComboController extends Controller
{
    public function index(Request $request)
    {
        $withTrashed = (bool) $request->query('with_trashed', false);
        $query = Combo::query();
        if ($withTrashed) { $query->withTrashed(); }
        $combos = $query->orderBy('name')->paginate(10)->withQueryString();
        return view('admin.combos.index', compact('combos', 'withTrashed'));
    }

    public function create()
    {
        return view('admin.combos.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);
        $combo = Combo::create($data);
        return redirect()->route('admin.combos.index')->with('success', 'Tạo combo thành công');
    }

    public function edit(Combo $combo)
    {
        return view('admin.combos.edit', compact('combo'));
    }

    public function update(Request $request, Combo $combo)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);
        $combo->update($data);
        return redirect()->route('admin.combos.index')->with('success', 'Cập nhật combo thành công');
    }

    public function destroy(Combo $combo)
    {
        $combo->delete();
        return back()->with('success', 'Đã xóa (mềm) combo');
    }

    public function restore(string $id)
    {
        $combo = Combo::withTrashed()->findOrFail($id);
        $combo->restore();
        return back()->with('success', 'Đã khôi phục combo');
    }
}


