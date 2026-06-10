<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $rolePermissions = [
            'Admin' => [
                'users.index',
                'users.show',
                'users.create',
                'users.store',
                'users.edit',
                'users.update',
                'users.destroy',
                'roles.index',
                'roles.show',
                'roles.create',
                'roles.store',
                'roles.edit',
                'roles.update',
                'roles.destroy',
                'hotels.index',
                'hotels.show',
                'hotels.create',
                'hotels.store',
                'hotels.edit',
                'hotels.update',
                'hotels.destroy',
                'categories.index',
                'categories.show',
                'categories.create',
                'categories.store',
                'categories.edit',
                'categories.update',
                'categories.destroy',
                'products.index',
                'products.show',
                'products.create',
                'products.store',
                'products.edit',
                'products.update',
                'products.destroy',
                'orders.index',
                'orders.show',
                'orders.create',
                'orders.store',
                'orders.edit',
                'orders.update',
                'orders.destroy',
                'queries.index',
                'queries.show',
                'queries.edit',
                'queries.update',
                'queries.destroy',
                'dashboard.admin',
                'report.sales'
            ],
            'Employee' => [
                'users.index',
                'users.show',
                'users.edit',
                'users.update',
                'hotels.index',
                'hotels.show',
                'hotels.edit',
                'hotels.update',
                'categories.index',
                'categories.show',
                'categories.edit',
                'categories.update',
                'orders.index',
                'orders.show',
                'orders.edit',
                'orders.update',
                'queries.index',
                'queries.show',
                'queries.edit',
                'queries.update',
                'dashboard.admin'
            ],
            'Hotel Owner' => [
                'hotels.index',
                'hotels.show',
                'hotels.edit',
                'hotels.update',
                'categories.index',
                'categories.show',
                'products.index',
                'products.show',
                'products.create',
                'products.store',
                'products.edit',
                'products.update',
                'products.destroy',
                'orders.index','orders.show','orders.update',
                'report.sales'
            ],
            'Delivery Person' => [
                'orders.index',
                'orders.show',
                'orders.update'
            ],
            'Customer' => ['hotels.index', 'hotels.show', 'categories.index', 'categories.show', 'products.index', 'products.show', 'orders.create', 'orders.store','orders.index','orders.show', 'queries.create', 'queries.store',
            'cart.index', 'cart.store', 'cart.update', 'cart.destroy'],
        ];

        $allPermissions = collect($rolePermissions)->flatten()->unique();
        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        foreach ($rolePermissions as $roleName => $permissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($permissions);
        }

        $adminUser = User::firstOrCreate([
            'email' => env('ADMIN_MAIL', 'admin@gmail.com')
        ], [
            'name' => env('ADMIN_NAME', 'Admin'),
            'password' => Hash::make(env('ADMIN_PASSWORD', '12345678'))
        ]);

        $adminUser->assignRole('Admin');
    }
}
