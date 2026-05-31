<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // MUST run first to create permissions and roles
            RolesAndPermissionsSeeder::class,
            
            // Then create users
            AdministratorSeeder::class,
            UserSeeder::class,
            CategorySeeder::class,
            TopicSeeder::class,
            TemplateSeeder::class,
        ]);
    }
}
