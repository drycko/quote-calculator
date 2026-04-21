<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class InstallCommand extends Command
{
    protected $signature = 'app:install
                            {--name=     : Admin full name}
                            {--email=    : Admin email address}
                            {--password= : Admin password}';

    protected $description = 'Finalise installation: seed roles/permissions and create the first admin user.';

    public function handle(): int
    {
        // Ensure all roles exist (idempotent — safe to run even if already seeded)
        $roles = ['support', 'admin', 'manager', 'stock_controller'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        $name     = $this->option('name')     ?: $this->ask('Admin full name');
        $email    = $this->option('email')    ?: $this->ask('Admin email');
        $password = $this->option('password') ?: $this->secret('Admin password');

        if (User::where('email', $email)->exists()) {
            $this->warn("A user with email {$email} already exists — skipping.");
            return self::SUCCESS;
        }

        $user = User::create([
            'name'     => $name,
            'email'    => $email,
            'password' => Hash::make($password),
        ]);

        $user->assignRole('admin');

        $this->info("Admin user created: {$email}");

        return self::SUCCESS;
    }
}
