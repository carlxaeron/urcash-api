<?php

use Illuminate\Database\Seeder;
use App\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create(['name' => 'Administrator', 'slug' => 'administrator']);
        Role::create(['name' => 'Customer Support', 'slug' => 'customer-support']);
        Role::create(['name' => 'Technical Support', 'slug' => 'technical-support']);
        Role::create(['name' => 'Staff', 'slug' => 'staff']);
        Role::create(['name' => 'Merchant', 'slug' => 'merchant']);
    }
}
