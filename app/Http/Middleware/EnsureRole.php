<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();
        if (!$user || !$user->role) {
            abort(403, 'Bạn không có quyền truy cập.');
        }

        $allowed = collect($roles)->filter()->map(fn($r) => strtolower(trim($r)))->all();
        $userRole = strtolower($user->role->name ?? '');

        if (!in_array($userRole, $allowed, true)) {
            abort(403, 'Bạn không có quyền truy cập.');
        }

        return $next($request);
    }
}


