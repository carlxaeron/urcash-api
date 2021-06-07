<?php

use Illuminate\Database\Seeder;
use App\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create(['name' => 'Create Merchants', 'slug' => 'create-merchants']);
        Permission::create(['name' => 'Delete Merchants', 'slug' => 'delete-merchants']);
        Permission::create(['name' => 'Edit Merchants', 'slug' => 'edit-merchants']);
        Permission::create(['name' => 'Update Merchants', 'slug' => 'update-merchants']);

        Permission::create(['name' => 'Create Permissions', 'slug' => 'create-permissions']);
        Permission::create(['name' => 'Delete Permissions', 'slug' => 'delete-permissions']);
        Permission::create(['name' => 'Edit Permissions', 'slug' => 'edit-permissions']);
        Permission::create(['name' => 'Update Permissions', 'slug' => 'update-permissions']);

        Permission::create(['name' => 'Create Products', 'slug' => 'create-products']);
        Permission::create(['name' => 'Delete Products', 'slug' => 'delete-products']);
        Permission::create(['name' => 'Edit Products', 'slug' => 'edit-products']);
        Permission::create(['name' => 'Update Products', 'slug' => 'update-products']);

        Permission::create(['name' => 'Create Roles', 'slug' => 'create-roles']);
        Permission::create(['name' => 'Delete Roles', 'slug' => 'delete-roles']);
        Permission::create(['name' => 'Edit Roles', 'slug' => 'edit-roles']);
        Permission::create(['name' => 'Update Roles', 'slug' => 'update-roles']);
    }
}
