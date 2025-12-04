<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create AI-related permissions
        $permissions = [
            'ai-access' => 'Access AI features',
            'ai-configure' => 'Configure AI settings',
            'ai-view-logs' => 'View AI usage logs',
            'ai-test-generation' => 'Use AI test generation',
            'ai-code-optimization' => 'Use AI code optimization',
            'ai-bug-analysis' => 'Use AI bug analysis',
        ];

        foreach ($permissions as $name => $description) {
            Permission::firstOrCreate(
                ['name' => $name],
                ['guard_name' => 'web']
            );
        }

        // Assign permissions to Super Admin role
        $superAdmin = Role::where('name', 'Super Admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo(array_keys($permissions));
        }

        // Assign basic AI access to Admin role
        $admin = Role::where('name', 'Admin')->first();
        if ($admin) {
            $admin->givePermissionTo(['ai-access', 'ai-test-generation', 'ai-code-optimization', 'ai-bug-analysis']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove AI permissions
        $permissions = [
            'ai-access',
            'ai-configure',
            'ai-view-logs',
            'ai-test-generation',
            'ai-code-optimization',
            'ai-bug-analysis',
        ];

        foreach ($permissions as $permission) {
            Permission::where('name', $permission)->delete();
        }
    }
};
