<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class AssignSuperAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:assign-super-admin-user {userEmail}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign a super admin role to a user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userEmail = $this->argument('userEmail');

        $user = User::query()->where('email', $userEmail)->first();
        if (! $user) {
            $this->error("User not found: {$userEmail}");

            return;
        }

        $superAdminRole = Role::query()->firstOrCreate([
            'name' => 'Super Admin',
        ]);

        $user->assignRole($superAdminRole);
        $this->info("Super admin role assigned to user: {$userEmail}");
    }
}
