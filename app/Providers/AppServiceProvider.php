<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use App\Models\Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \App\Models\User::observe(\App\Observers\UserObserver::class);

        \App\Models\Ticket::creating(function($ticket){
            if (empty($ticket->code)) {
                $ticket->code = 'TKT-'.bin2hex(random_bytes(6));
            }
        });

        $this->observeAudit(\App\Models\Category::class);
        $this->observeAudit(\App\Models\Actor::class);
        $this->observeAudit(\App\Models\Movie::class);
        $this->observeAudit(\App\Models\Room::class);
        $this->observeAudit(\App\Models\Seat::class);
        $this->observeAudit(\App\Models\Showtime::class);
        $this->observeAudit(\App\Models\Combo::class);
        $this->observeAudit(\App\Models\DiscountCode::class);
        $this->observeAudit(\App\Models\Ticket::class);

        // Blade directives for roles
        Blade::if('role', function (...$roles) {
            $user = Auth::user();
            if (!$user || !$user->role) { return false; }
            $allowed = collect($roles)->flatten()->filter()->map(fn($r) => strtolower(trim($r)))->all();
            return in_array(strtolower($user->role->name ?? ''), $allowed, true);
        });

        Blade::if('manager', function () {
            $user = Auth::user();
            return $user && strtolower($user->role->name ?? '') === 'manager';
        });

        Blade::if('staff', function () {
            $user = Auth::user();
            return $user && strtolower($user->role->name ?? '') === 'staff';
        });
    }

    private function observeAudit(string $modelClass): void
    {
        $log = function(string $action, $model) {
            $adminId = Auth::id();
            $table = $model->getTable();
            $recordId = $model->getKey();

            $payload = [];
            $label = $model->name ?? $model->title ?? $model->code ?? null;

            // Domain-specific messages
            if ($table === 'tickets') {
                if ($action === 'created') {
                    $payload['message'] = 'Đặt vé';
                } elseif ($action === 'updated') {
                    $newStatus = $model->getChanges()['status'] ?? null;
                    if ($newStatus === 'paid_cash' || $newStatus === 'paid_online') {
                        $payload['message'] = 'Xác nhận thanh toán vé';
                    } elseif ($newStatus === 'used' || ($model->getChanges()['used_at'] ?? null)) {
                        $payload['message'] = 'Check-in vé';
                    } elseif ($newStatus === 'cancelled') {
                        $payload['message'] = 'Hủy vé';
                    }
                } elseif ($action === 'deleted') {
                    $payload['message'] = 'Hủy vé (xóa)';
                } elseif ($action === 'restored') {
                    $payload['message'] = 'Khôi phục vé';
                }
            } else {
                // Generic messages for CRUD by admins/managers
                $vnEntity = match ($table) {
                    'categories' => 'thể loại',
                    'actors' => 'diễn viên',
                    'movies' => 'phim',
                    'rooms' => 'phòng chiếu',
                    'seats' => 'ghế',
                    'showtimes' => 'suất chiếu',
                    'combos' => 'combo',
                    'discount_codes' => 'mã giảm giá',
                    'users' => 'người dùng',
                    default => $table,
                };
                if ($action === 'created') {
                    $payload['message'] = 'Tạo ' . $vnEntity . ($label ? ': ' . $label : '');
                } elseif ($action === 'updated') {
                    $payload['message'] = 'Cập nhật ' . $vnEntity . ($label ? ': ' . $label : '');
                } elseif ($action === 'deleted') {
                    $payload['message'] = 'Xóa ' . $vnEntity . ($label ? ': ' . $label : '');
                } elseif ($action === 'restored') {
                    $payload['message'] = 'Khôi phục ' . $vnEntity . ($label ? ': ' . $label : '');
                }
            }

            // Optional structured change details (kept for future deep-inspect)
            if ($action === 'updated') {
                $changes = [];
                foreach ($model->getChanges() as $field => $newValue) {
                    if (in_array($field, ['updated_at', 'created_at', 'deleted_at'], true)) { continue; }
                    $changes[$field] = [
                        'from' => $model->getOriginal($field),
                        'to' => $newValue,
                    ];
                }
                if (!empty($changes)) {
                    $payload['changed'] = $changes;
                }
            }
            if (in_array($action, ['deleted', 'restored'], true)) {
                $payload['id'] = $recordId;
                if ($label) { $payload['label'] = $label; }
            }

            Log::create([
                'admin_id' => $adminId ?? 1,
                'action' => $action,
                'table_name' => $table,
                'record_id' => $recordId,
                'description' => json_encode($payload, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
            ]);
        };

        $modelClass::created(function($m) use ($log){ $log('created', $m); });
        $modelClass::updated(function($m) use ($log){ $log('updated', $m); });
        $modelClass::deleted(function($m) use ($log){ $log('deleted', $m); });
        $modelClass::restored(function($m) use ($log){ $log('restored', $m); });
    }
}
