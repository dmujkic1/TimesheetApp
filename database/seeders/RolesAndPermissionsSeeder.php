<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::create(['name' => 'view-employees']);
        Permission::create(['name' => 'create-employee']);
        Permission::create(['name' => 'view-employee']);
        Permission::create(['name' => 'edit-employee']);
        Permission::create(['name' => 'update-employee']);
        Permission::create(['name' => 'delete-employee']);
        Permission::create(['name' => 'view-managers']);



        // update cache to know about the newly created permissions (required if using WithoutModelEvents in seeders)
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $userRole = Role::create(['name' => 'user']);
        //$userRole->givePermissionTo(['view-employees']);

        $employeeRole = Role::create(['name' => 'employee']);
        $employeeRole->givePermissionTo(['view-employees']);

        $managerRole = Role::create(['name' => 'manager']);
        $managerRole->givePermissionTo(['view-employees']);

        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        /* $svi = User::all();
        foreach ($svi as $user) { $user->assignRole('user'); } //observer odraditi ovo - dugorocno
        User::where("email", "dinom@gmail.com")->first()->assignRole('admin'); //od ovog poceti i onda u nekom Controlleru napraviti CRUD da admin moze role davati ljudima
        User::where("email", "danim@gmail.com")->first()->assignRole('user'); */
        
        User::where("email", "dinom@gmail.com")->first()->assignRole('admin');
        
    }
}
