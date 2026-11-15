<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    private const CRUD = ['table', 'add', 'edit', 'delete'];

    public function run(): void
    {
        $permissions = [
            ...$this->crud('role'),
            ...$this->crud('employee'),
            ...$this->crud('product'),
            ...$this->crud('category'),
            ...$this->crud('brand'),
            ...$this->crud('branch'),
            ...$this->crud('area'),
            'order-table', 'order-edit', 'order-delete',
            'section-table', 'section-edit',
            'setting-edit',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'admin']);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    private function crud(string $prefix): array
    {
        return array_map(fn (string $action) => "{$prefix}-{$action}", self::CRUD);
    }
}
