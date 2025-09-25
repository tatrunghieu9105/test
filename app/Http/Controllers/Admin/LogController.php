<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Log;
use App\Models\User;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $query = Log::query()->with(['admin']);

        if ($request->filled('admin_id')) {
            $query->where('admin_id', $request->integer('admin_id'));
        }
        if ($request->filled('action')) {
            $query->where('action', $request->string('action'));
        }
        if ($request->filled('table_name')) {
            $query->where('table_name', $request->string('table_name'));
        }
        if ($request->filled('from')) {
            $query->where('created_at', '>=', $request->date('from')->startOfDay());
        }
        if ($request->filled('to')) {
            $query->where('created_at', '<=', $request->date('to')->endOfDay());
        }

        $logs = $query->orderByDesc('id')->paginate(20)->withQueryString();

        $admins = User::whereNotNull('role_id')->orderBy('fullname')->get(['id','fullname']);
        $actions = ['created','updated','deleted','restored','create','update','delete','restore'];
        $tables = Log::query()->select('table_name')->distinct()->pluck('table_name');

        return view('admin.logs.index', compact('logs','admins','actions','tables'));
    }
}
