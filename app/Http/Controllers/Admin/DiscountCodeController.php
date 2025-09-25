<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DiscountCode;
use Illuminate\Http\Request;

class DiscountCodeController extends Controller
{
    public function index(Request $request)
    {
        $withTrashed = (bool) $request->query('with_trashed', false);
        $query = DiscountCode::query();
        if ($withTrashed) { $query->withTrashed(); }
        $discounts = $query->orderByDesc('created_at')->paginate(10)->withQueryString();
        return view('admin.discounts.index', compact('discounts', 'withTrashed'));
    }

    public function create()
    {
        return view('admin.discounts.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|max:50|unique:discount_codes,code',
            'type' => 'required|in:percent,amount',
            'value' => 'required|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);
        $discount = DiscountCode::create($data);
        return redirect()->route('admin.discounts.index')->with('success', 'Tạo mã giảm giá thành công');
    }

    public function edit(DiscountCode $discount)
    {
        return view('admin.discounts.edit', compact('discount'));
    }

    public function update(Request $request, DiscountCode $discount)
    {
        $data = $request->validate([
            'code' => 'required|string|max:50|unique:discount_codes,code,'.$discount->id,
            'type' => 'required|in:percent,amount',
            'value' => 'required|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);
        $discount->update($data);
        return redirect()->route('admin.discounts.index')->with('success', 'Cập nhật mã giảm giá thành công');
    }

    public function destroy(DiscountCode $discount)
    {
        $discount->delete();
        return back()->with('success', 'Đã xóa (mềm) mã giảm');
    }

    public function restore(string $id)
    {
        $d = DiscountCode::withTrashed()->findOrFail($id);
        $d->restore();
        return back()->with('success', 'Đã khôi phục mã giảm');
    }
}


