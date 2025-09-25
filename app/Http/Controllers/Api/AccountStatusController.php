<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountStatusController extends Controller
{
    public function check()
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'active' => false,
                'redirect' => route('login')
            ], 200);
        }

        // Tải lại thông tin user từ database để đảm bảo có dữ liệu mới nhất
        $user->refresh();

        if (!$user->is_active) {
            Auth::logout();
            return response()->json([
                'active' => false,
                'message' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên.',
                'redirect' => route('login')
            ], 403);
        }

        return response()->json([
            'active' => true
        ]);
    }
}
