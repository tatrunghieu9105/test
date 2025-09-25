<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Seat;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $withTrashed = (bool) $request->query('with_trashed', false);
        $query = Room::query();
        if ($withTrashed) { $query->withTrashed(); }
        $rooms = $query->orderBy('name')->paginate(10)->withQueryString();
        return view('admin.rooms.index', compact('rooms', 'withTrashed'));
    }

    public function create()
    {
        return view('admin.rooms.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:50',
            'rows' => 'required|integer|min:1|max:26',
            'cols' => 'required|integer|min:1|max:30',
            'vip_last_rows' => 'nullable|integer|min:0|max:26',
        ]);

        $layout = [
            'rows' => $data['rows'],
            'cols' => $data['cols'],
            'vip_last_rows' => $data['vip_last_rows'] ?? 0,
        ];

        $room = Room::create([
            'name' => $data['name'],
            'total_seats' => $data['rows'] * $data['cols'],
            'layout' => $layout,
        ]);
        $this->syncSeats($room, $layout, replaceAll: true);
        return redirect()->route('admin.rooms.index')->with('success', 'Tạo phòng thành công');
    }

    public function edit(Room $room)
    {
        $room->load('seats');
        return view('admin.rooms.edit', compact('room'));
    }

    public function update(Request $request, Room $room)
    {
        $data = $request->validate([
            'name' => 'required|string|max:50',
        ]);
        $room->update($data);
        return redirect()->route('admin.rooms.index')->with('success', 'Cập nhật phòng thành công');
    }

    public function updateLayout(Request $request, Room $room)
    {
        $data = $request->validate([
            'rows' => 'required|integer|min:1|max:26',
            'cols' => 'required|integer|min:1|max:30',
            'vip_last_rows' => 'nullable|integer|min:0|max:26',
        ]);

        $layout = [
            'rows' => $data['rows'],
            'cols' => $data['cols'],
            'vip_last_rows' => $data['vip_last_rows'] ?? 0,
        ];

        DB::transaction(function () use ($room, $layout) {
            $room->update([
                'layout' => $layout,
                'total_seats' => $layout['rows'] * $layout['cols'],
            ]);
            $this->syncSeats($room, $layout);
        });
        return back()->with('success', 'Cập nhật layout thành công');
    }

    public function destroy(Room $room)
    {
        $room->delete();
        return back()->with('success', 'Đã xóa (mềm) phòng');
    }

    public function restore(string $id)
    {
        $room = Room::withTrashed()->findOrFail($id);
        $room->restore();
        return back()->with('success', 'Đã khôi phục phòng');
    }

    private function syncSeats(Room $room, array $layout, bool $replaceAll = false): void
    {
        $rows = range('A', chr(ord('A') + $layout['rows'] - 1));
        $cols = $layout['cols'];
        $vipStartIndex = max(0, count($rows) - (int)($layout['vip_last_rows'] ?? 0));

        $existing = $room->seats()->get()->keyBy('code');
        $toKeepCodes = [];

        foreach ($rows as $idx => $rowLetter) {
            for ($i = 1; $i <= $cols; $i++) {
                $code = $rowLetter.$i;
                $type = $idx >= $vipStartIndex ? 'VIP' : 'Thường';
                $toKeepCodes[] = $code;

                if (isset($existing[$code])) {
                    $seat = $existing[$code];
                    if ($seat->type !== $type) {
                        $seat->update(['type' => $type]);
                    }
                } else {
                    Seat::create([
                        'room_id' => $room->id,
                        'code' => $code,
                        'type' => $type,
                    ]);
                }
            }
        }

        // Remove seats that are outside new layout only if they have no tickets
        foreach ($existing as $code => $seat) {
            if (!in_array($code, $toKeepCodes, true)) {
                $hasTickets = Ticket::where('seat_id', $seat->id)->exists();
                if ($hasTickets) {
                    // Không xóa ghế đã có vé, chỉ ẩn (soft delete)
                    $seat->delete();
                } else {
                    $seat->forceDelete(); // Xóa cứng nếu không có vé nào liên quan
                }
            }
        }
    }
}


