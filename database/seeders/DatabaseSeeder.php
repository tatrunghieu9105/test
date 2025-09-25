<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Category;
use App\Models\Actor;
use App\Models\Movie;
use App\Models\Room;
use App\Models\Seat;
use App\Models\Showtime;
use App\Models\Combo;
use App\Models\DiscountCode;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Roles
        $managerRole = Role::firstOrCreate(['name' => 'manager'], [
            'description' => 'Quản lý'
        ]);
        $staffRole = Role::firstOrCreate(['name' => 'staff'], [
            'description' => 'Nhân viên'
        ]);
        $customerRole = Role::firstOrCreate(['name' => 'customer'], [
            'description' => 'Khách hàng'
        ]);

        // Users
        $manager = User::firstOrCreate([
            'email' => 'manager@example.com',
        ], [
            'fullname' => 'Manager',
            'password' => Hash::make('password'),
            'role_id' => $managerRole->id,
        ]);

        $staff = User::firstOrCreate([
            'email' => 'staff@example.com',
        ], [
            'fullname' => 'Staff',
            'password' => Hash::make('password'),
            'role_id' => Role::where('name', 'staff')->value('id'),
        ]);

        $customer = User::firstOrCreate([
            'email' => 'customer@example.com',
        ], [
            'fullname' => 'Customer',
            'password' => Hash::make('password'),
            'role_id' => Role::where('name', 'customer')->value('id'),
        ]);

        // Categories
        $catAction = Category::firstOrCreate(['name' => 'Hành động']);
        $catDrama = Category::firstOrCreate(['name' => 'Tâm lý']);

        // Actors
        $actor1 = Actor::firstOrCreate(['name' => 'Diễn viên A']);
        $actor2 = Actor::firstOrCreate(['name' => 'Diễn viên B']);

        // Movies
        $movie1 = Movie::firstOrCreate([
            'title' => 'Phim 1',
        ], [
            'description' => 'Mô tả phim 1',
            'duration' => 120,
            'category_id' => $catAction->id,
            'release_date' => now()->toDateString(),
        ]);
        $movie2 = Movie::firstOrCreate([
            'title' => 'Phim 2',
        ], [
            'description' => 'Mô tả phim 2',
            'duration' => 100,
            'category_id' => $catDrama->id,
            'release_date' => now()->toDateString(),
        ]);

        $movie1->actors()->syncWithoutDetaching([$actor1->id, $actor2->id]);
        $movie2->actors()->syncWithoutDetaching([$actor2->id]);

        // Rooms and seats
        $room1 = Room::firstOrCreate(['name' => 'Phòng 1'], [
            'total_seats' => 40,
            'layout' => null,
        ]);
        $room2 = Room::firstOrCreate(['name' => 'Phòng 2'], [
            'total_seats' => 30,
            'layout' => null,
        ]);

        // Generate seats if not exist
        foreach ([$room1, $room2] as $room) {
            if ($room->seats()->count() === 0) {
                $rows = ['A','B','C','D','E'];
                $cols = $room->id === $room1->id ? 8 : 6;
                foreach ($rows as $row) {
                    for ($i = 1; $i <= $cols; $i++) {
                        Seat::create([
                            'room_id' => $room->id,
                            'code' => $row.$i,
                            'type' => in_array($row, ['D','E']) ? 'VIP' : 'Thường'
                        ]);
                    }
                }
            }
        }

        // Combos
        Combo::firstOrCreate(['name' => 'Combo 1'], [
            'price' => 50000,
            'description' => 'Bắp + Nước',
        ]);
        Combo::firstOrCreate(['name' => 'Combo 2'], [
            'price' => 80000,
            'description' => 'Bắp lớn + 2 Nước',
        ]);

        // Discount codes
        $discounts = [
            [
                'code' => 'GIAM10',
                'type' => 'percent',
                'value' => 10,
                'start_date' => now()->subDays(7),
                'end_date' => now()->addDays(30),
                'is_active' => true,
            ],
            [
                'code' => 'GIAM50K',
                'type' => 'amount',
                'value' => 50000,
                'start_date' => now()->subDays(7),
                'end_date' => now()->addDays(30),
                'is_active' => true,
            ],
        ];
        
        foreach ($discounts as $discount) {
            DiscountCode::firstOrCreate(['code' => $discount['code']], $discount);
        }

        // Showtimes
        $now = now();
        $st1 = Showtime::firstOrCreate([
            'movie_id' => $movie1->id,
            'room_id' => $room1->id,
            'start_time' => $now->copy()->addDay()->setTime(18, 0, 0),
        ], [
            'end_time' => $now->copy()->addDay()->setTime(20, 0, 0),
            'price' => 90000,
        ]);
        
        $st2 = Showtime::firstOrCreate([
            'movie_id' => $movie2->id,
            'room_id' => $room2->id,
            'start_time' => $now->copy()->addDay()->setTime(20, 30, 0),
        ], [
            'end_time' => $now->copy()->addDay()->setTime(22, 10, 0),
            'price' => 80000,
        ]);
    }
}
