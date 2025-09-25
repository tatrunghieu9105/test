<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckUserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Bỏ qua các route công khai
        $publicRoutes = ['login', 'register', 'logout', 'password/*'];
        foreach ($publicRoutes as $route) {
            if ($request->is($route)) {
                return $next($request);
            }
        }

        // Nếu người dùng đã đăng nhập
        if (auth()->check()) {
            $user = auth()->user()->fresh(); // Lấy dữ liệu mới nhất từ database
            
            // Kiểm tra xem tài khoản có bị khóa không
            if ($user && !$user->is_active) {
                // Ghi log
                Log::warning("Truy cập bị từ chối - Tài khoản đã bị khóa", [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'ip' => $request->ip(),
                    'path' => $request->path(),
                    'method' => $request->method()
                ]);
                
                // Đăng xuất người dùng
                auth()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                // Xóa remember token nếu có
                if (!empty($user->getRememberToken())) {
                    $user->setRememberToken(null);
                    $user->save();
                }
                
                // Chuẩn bị thông báo
                $message = 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên để biết thêm chi tiết.';
                
                // Nếu là API request hoặc AJAX
                if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'error' => true,
                        'message' => $message,
                        'redirect' => route('login')
                    ], 403);
                }
                
                // Chuyển hướng về trang đăng nhập với thông báo
                return redirect()->route('login')
                    ->with('account_locked', $message)
                    ->withHeaders([
                        'Cache-Control' => 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0',
                        'Pragma' => 'no-cache',
                        'Expires' => '0',
                    ]);
            }
            
            // Kiểm tra thời gian hoạt động cuối cùng (nếu cần)
            if (method_exists($user, 'updateLastActivity')) {
                $user->updateLastActivity();
            }
        }
        
        // Thêm header ngăn cache cho tất cả responses
        $response = $next($request);
        
        return $response->withHeaders([
            'Cache-Control' => 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }
}
