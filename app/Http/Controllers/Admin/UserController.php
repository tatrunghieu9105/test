<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $role = $request->string('role')->toString();
        $q = $request->string('q')->toString();

        $query = User::query()->with('role');
        if ($role !== '') {
            $query->whereHas('role', fn($qr) => $qr->where('name', $role));
        }
        if ($q !== '') {
            $query->where(function($qr) use ($q){
                $qr->where('fullname', 'like', "%{$q}%")
                   ->orWhere('email', 'like', "%{$q}%");
            });
        }
        $users = $query->orderBy('fullname')->paginate(15)->withQueryString();
        $roles = Role::orderBy('name')->get();

        return view('admin.users.index', compact('users','roles','role','q'));
    }

    public function updateRole(Request $request, User $user)
    {
        $data = $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);
        $user->update(['role_id' => $data['role_id']]);
        return back()->with('success', 'Cập nhật quyền cho tài khoản thành công.');
    }
    
    public function updateStatus(Request $request, User $user)
    {
        // Không cho phép khóa/mở khóa tài khoản của chính mình
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Bạn không thể thay đổi trạng thái tài khoản của chính mình.');
        }

        // Không cho phép thay đổi trạng thái tài khoản admin
        if ($user->role->name === 'admin') {
            return back()->with('error', 'Không thể thay đổi trạng thái tài khoản quản trị viên.');
        }

        $request->validate([
            'is_active' => 'required|boolean'
        ]);

        $user->update(['is_active' => $request->is_active]);
        
        $status = $request->is_active ? 'mở khóa' : 'khóa';
        return back()->with('success', "Đã {$status} tài khoản thành công.");
    }


    public function destroy(User $user)
    {
        // Không cho phép xóa tài khoản của chính mình
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Bạn không thể xóa tài khoản của chính mình.');
        }

        // Không cho phép xóa tài khoản admin
        if ($user->role->name === 'admin') {
            return back()->with('error', 'Không thể xóa tài khoản quản trị viên.');
        }

        // Kiểm tra nếu tài khoản có liên kết với các bản ghi khác
        if ($user->tickets()->exists()) {
            return back()->with('error', 'Không thể xóa tài khoản vì có dữ liệu liên quan (vé xem phim).');
        }

        // Xóa tài khoản
        $user->delete();
        return back()->with('success', 'Đã xóa tài khoản thành công.');
    }
}
