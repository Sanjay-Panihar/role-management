<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        Permission::updateOrCreate(['name' => 'add product']);
        Permission::updateOrCreate(['name' => 'edit product']);
        Permission::updateOrCreate(['name' => 'delete product']);
        Permission::updateOrCreate(['name' => 'view product']);
        Permission::updateOrCreate(['name' => 'view users']);
        Permission::updateOrCreate(['name' => 'view permissions']);

        // create roles and assign created permissions

        // this can be done as separate statements
        $role1 = Role::updateOrCreate(['name' => 'User']);
        $role1->givePermissionTo('view product');

        $role2 = Role::updateOrCreate(['name' => 'super-admin']);
        $role2->givePermissionTo(Permission::all());

        $user = User::updateOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super-Admin',
                'password' => bcrypt('superadmin@123'),
            ]
        );
        
        $user->assignRole($role2);
    }
}
