<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Hotel;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolePermissionSeeder::class);

        $employee = User::firstOrCreate([
            'name' => 'Employee',
            'email' => 'employee@gmail.com',
            'phone' => '8654789344',
            'password' => Hash::make('12345678')
        ]);
        $employee->assignRole('Employee');

        $delivery = User::firstOrCreate([
            'name' => 'DeliveryBoy',
            'email' => 'delivery@gmail.com',
            'phone' => '8688789344',
            'password' => Hash::make('12345678')
        ]);
        $delivery->assignRole('Delivery Person');

        $hotelOwner = User::firstOrCreate([
            'name' => 'HotelOwner',
            'email' => 'hotel@gmail.com',
            'phone' => '8654789569',
            'password' => Hash::make('12345678')
        ]);
        $hotelOwner->assignRole('Hotel Owner');
        $hotel = Hotel::firstOrCreate([
            'name' => 'Velu Biriyani',
            'user_id' => $hotelOwner->id
        ]);

        $category = Category::firstOrCreate([
            'name' => 'Biriyani'
        ]);

        Product::firstOrCreate([
           'name' => 'Chicken Biriyani',
           'category_id' => $category->id,
           'hotel_id' => $hotel->id,
           'price' => 200,
           'image' => 'default.jpg',
           'is_available' => 1
        ]);

        $user = User::firstOrCreate([
          'name' => 'User',
          'email' => 'user@gmail.com',
          'phone' => '8654339569',
          'password' => Hash::make('12345678')
       ]);
        $user->assignRole('Customer');
    }
}
