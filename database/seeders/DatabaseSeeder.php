<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed WordPress-like roles
        $roles = ['subscriber', 'contributor', 'author', 'editor', 'administrator'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'tino@ukuyila.design',
        ]);

        $user->assignRole('administrator');

        $this->call([
            LineItemTemplateSeeder::class,
        ]);
    }
}
