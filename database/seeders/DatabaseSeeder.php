<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Setting;
use App\Models\Category;
use App\Models\Service;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (Schema::hasTable('settings') && Setting::count() === 0) {
            Setting::factory()->create();
        }

        $this->ensureRolesAndPermissions();

        if (Schema::hasTable('users') && User::count() === 0) {
            $this->createUsers();
        }

        if (Schema::hasTable('categories') && Category::count() === 0) {
            $this->createCategoriesAndMaterials();
        }
    }

    protected function ensureRolesAndPermissions(): void
    {
        $permissions = [
            'permissions.view',
            'permissions.create',
            'permissions.edit',
            'permissions.delete',
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'appointments.view',
            'appointments.create',
            'appointments.edit',
            'appointments.delete',
            'categories.view',
            'categories.create',
            'categories.edit',
            'categories.delete',
            'services.view',
            'services.create',
            'services.edit',
            'services.delete',
            'transactions.view',
            'transactions.create',
            'reports.view',
            'settings.edit',
            'setting update',
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
        }

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $inventoryRole = Role::firstOrCreate(['name' => 'inventory_personnel']);
        $managementRole = Role::firstOrCreate(['name' => 'management']);

        Role::firstOrCreate(['name' => 'moderator']);
        Role::firstOrCreate(['name' => 'employee']);
        Role::firstOrCreate(['name' => 'subscriber']);

        $adminRole->syncPermissions(Permission::all());

        $inventoryRole->syncPermissions(Permission::whereIn('name', [
            'categories.view',
            'categories.create',
            'categories.edit',
            'categories.delete',
            'services.view',
            'services.create',
            'services.edit',
            'services.delete',
            'transactions.view',
            'transactions.create',
            'reports.view',
        ])->get());

        $managementRole->syncPermissions(Permission::whereIn('name', [
            'services.view',
            'transactions.view',
            'reports.view',
            'categories.view',
        ])->get());
    }

    protected function createUsers(): void
    {
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'phone' => '1234567890',
            'status' => 1,
            'email_verified_at' => now(),
            'password' => Hash::make('admin123'),
        ]);
        $admin->assignRole('admin');

        $inventory = User::create([
            'name' => 'Inventory Personnel',
            'email' => 'inventory@example.com',
            'phone' => '1234567891',
            'status' => 1,
            'email_verified_at' => now(),
            'password' => Hash::make('inventory123'),
        ]);
        $inventory->assignRole('inventory_personnel');

        $management = User::create([
            'name' => 'Management',
            'email' => 'management@example.com',
            'phone' => '1234567892',
            'status' => 1,
            'email_verified_at' => now(),
            'password' => Hash::make('management123'),
        ]);
        $management->assignRole('management');
    }

    protected function createCategoriesAndMaterials(): void
    {
        $catalog = [
            'Soil' => [
                ['title' => 'Fill Soil', 'unit' => 'cu.m', 'quantity' => 120, 'min_stock_level' => 30, 'price' => 450, 'excerpt' => 'General fill soil for site preparation.'],
                ['title' => 'Topsoil', 'unit' => 'cu.m', 'quantity' => 40, 'min_stock_level' => 15, 'price' => 680, 'excerpt' => 'Screened topsoil for finishing.'],
            ],
            'Sand' => [
                ['title' => 'Fine Sand', 'unit' => 'cu.m', 'quantity' => 85, 'min_stock_level' => 25, 'price' => 900, 'excerpt' => 'Fine sand for plastering and mix.'],
                ['title' => 'Washed Sand', 'unit' => 'cu.m', 'quantity' => 18, 'min_stock_level' => 20, 'price' => 1100, 'excerpt' => 'Washed sand for concrete works.'],
            ],
            'Gravel' => [
                ['title' => '3/4 Gravel', 'unit' => 'cu.m', 'quantity' => 60, 'min_stock_level' => 20, 'price' => 1350, 'excerpt' => 'Three-quarter gravel for concrete.'],
                ['title' => 'Pea Gravel', 'unit' => 'cu.m', 'quantity' => 22, 'min_stock_level' => 10, 'price' => 1500, 'excerpt' => 'Pea gravel for drainage and fill.'],
            ],
            'Crushed Rock' => [
                ['title' => 'Crushed Base Course', 'unit' => 'cu.m', 'quantity' => 75, 'min_stock_level' => 25, 'price' => 1600, 'excerpt' => 'Crushed rock for road base.'],
                ['title' => 'Crushed Aggregate', 'unit' => 'cu.m', 'quantity' => 12, 'min_stock_level' => 15, 'price' => 1750, 'excerpt' => 'Crushed aggregate for structural mix.'],
            ],
        ];

        foreach ($catalog as $categoryTitle => $materials) {
            $category = Category::create([
                'title' => $categoryTitle,
                'slug' => str($categoryTitle)->slug(),
                'body' => $categoryTitle.' used in construction inventory.',
                'status' => 1,
            ]);

            foreach ($materials as $material) {
                Service::create([
                    'title' => $material['title'],
                    'slug' => str($material['title'])->slug(),
                    'price' => $material['price'],
                    'excerpt' => $material['excerpt'],
                    'unit' => $material['unit'],
                    'quantity' => $material['quantity'],
                    'min_stock_level' => $material['min_stock_level'],
                    'category_id' => $category->id,
                    'status' => 1,
                ]);
            }
        }
    }
}
