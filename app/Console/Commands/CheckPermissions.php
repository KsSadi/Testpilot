<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Modules\User\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CheckPermissions extends Command
{
    protected $signature = 'user:check-permissions {email}';
    protected $description = 'Check user permissions and role details';

    public function handle()
    {
        $email = $this->argument('email');
        
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("User with email {$email} not found!");
            return 1;
        }
        
        $this->info("=== User Information ===");
        $this->info("Name: {$user->name}");
        $this->info("Email: {$user->email}");
        $this->line("");
        
        $this->info("=== Roles ===");
        $roles = $user->roles;
        if ($roles->count() > 0) {
            foreach ($roles as $role) {
                $this->info("- {$role->name} (Guard: {$role->guard_name})");
            }
        } else {
            $this->warn("No roles assigned!");
        }
        $this->line("");
        
        $this->info("=== Direct Permissions ===");
        $directPermissions = $user->permissions;
        if ($directPermissions->count() > 0) {
            foreach ($directPermissions as $perm) {
                $this->info("- {$perm->name}");
            }
        } else {
            $this->comment("No direct permissions");
        }
        $this->line("");
        
        $this->info("=== All Permissions (via roles + direct) ===");
        $allPermissions = $user->getAllPermissions();
        if ($allPermissions->count() > 0) {
            foreach ($allPermissions as $perm) {
                $this->info("- {$perm->name}");
            }
            $this->line("");
            $this->info("Total: {$allPermissions->count()} permissions");
        } else {
            $this->error("No permissions at all!");
        }
        
        $this->line("");
        $this->info("=== Permission Checks ===");
        $testPermissions = ['view-users', 'create-users', 'view-roles', 'view-permissions'];
        foreach ($testPermissions as $perm) {
            $has = $user->can($perm);
            $status = $has ? '✓ YES' : '✗ NO';
            $this->line("{$perm}: {$status}");
        }
        
        return 0;
    }
}
