<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RemoveUnusedRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define allowed roles
        $allowedRoles = ['administrator', 'pengurus', 'anggota'];
        
        // Get all roles
        $allRoles = Role::all();
        
        // Delete roles that are not in allowed list
        foreach ($allRoles as $role) {
            if (!in_array($role->name, $allowedRoles)) {
                // Get users with this role
                $usersWithRole = User::role($role->name)->get();
                
                // Assign anggota role (default) to them
                $anggotaRole = Role::firstOrCreate(['name' => 'anggota']);
                foreach ($usersWithRole as $user) {
                    $user->syncRoles([$anggotaRole]);
                }
                
                // Delete the role
                $role->delete();
                $this->command->info("Role '{$role->name}' telah dihapus dan user dipindahkan ke role 'anggota'");
            }
        }

        $this->command->info('Pembersihan role selesai! Role yang tersisa: administrator, pengurus, anggota');
    }
}
