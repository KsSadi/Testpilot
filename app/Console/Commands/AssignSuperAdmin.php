<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Modules\User\Models\User;
use Spatie\Permission\Models\Role;

class AssignSuperAdmin extends Command
{
    protected $signature = 'user:make-superadmin {email}';
    protected $description = 'Assign superadmin role to a user';

    public function handle()
    {
        $email = $this->argument('email');
        
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("User with email {$email} not found!");
            return 1;
        }
        
        $superAdminRole = Role::where('name', 'superadmin')->first();
        
        if (!$superAdminRole) {
            $this->error("Superadmin role not found!");
            return 1;
        }
        
        // Remove all existing roles and assign superadmin
        $user->syncRoles(['superadmin']);
        
        $this->info("✓ User {$user->name} ({$email}) is now a superadmin!");
        $this->info("✓ Current roles: " . $user->roles->pluck('name')->implode(', '));
        $this->info("✓ Total permissions: " . $user->getAllPermissions()->count());
        
        return 0;
    }
}
