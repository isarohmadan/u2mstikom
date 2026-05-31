<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class CleanupRolesSeeder extends Seeder
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
        
        // Get or create allowed roles
        $administratorRole = Role::firstOrCreate(['name' => 'administrator']);
        $pengurusRole = Role::firstOrCreate(['name' => 'pengurus']);
        $anggotaRole = Role::firstOrCreate(['name' => 'anggota']);

        // Rename existing roles if needed
        $staffRole = Role::where('name', 'staff')->first();
        if ($staffRole) {
            // Get all users with staff role
            $usersWithStaff = User::role('staff')->get();
            
            // Assign pengurus role to them
            foreach ($usersWithStaff as $user) {
                $user->syncRoles([$pengurusRole]);
            }
            
            // Delete staff role
            $staffRole->delete();
            $this->command->info('Role "staff" telah diubah menjadi "pengurus"');
        }

        $userRole = Role::where('name', 'user')->first();
        if ($userRole) {
            // Get all users with user role
            $usersWithUser = User::role('user')->get();
            
            // Assign anggota role to them
            foreach ($usersWithUser as $user) {
                $user->syncRoles([$anggotaRole]);
            }
            
            // Delete user role
            $userRole->delete();
            $this->command->info('Role "user" telah diubah menjadi "anggota"');
        }

        // Get all roles
        $allRoles = Role::all();
        
        // Delete roles that are not in allowed list
        foreach ($allRoles as $role) {
            if (!in_array($role->name, $allowedRoles)) {
                // Get users with this role
                $usersWithRole = User::role($role->name)->get();
                
                // Assign anggota role (default) to them
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
