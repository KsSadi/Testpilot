<?php

namespace App\Modules\RolePermission\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RolePermissionController extends Controller
{
    /**
     * Display a listing of roles.
     */
    public function rolesIndex()
    {
        $roles = Role::withCount('permissions', 'users')->get();
        $totalPermissions = Permission::count();
        $activeRoles = $roles->count();
        $customRoles = $roles->where('name', '!=', 'superadmin')->count();
        
        return view('RolePermission::roles.index', compact('roles', 'totalPermissions', 'activeRoles', 'customRoles'));
    }

    /**
     * Show the form for creating a new role.
     */
    public function createRole()
    {
        $permissions = Permission::all()->groupBy(function($permission) {
            // Group permissions by prefix (e.g., 'view-users' -> 'users')
            $parts = explode('-', $permission->name);
            return count($parts) > 1 ? ucfirst($parts[1]) : 'General';
        });
        
        return view('RolePermission::roles.form', compact('permissions'));
    }

    /**
     * Store a newly created role.
     */
    public function storeRole(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        DB::beginTransaction();
        try {
            $role = Role::create([
                'name' => $validated['name'],
                'guard_name' => 'web'
            ]);

            if (!empty($validated['permissions'])) {
                $role->syncPermissions($validated['permissions']);
            }

            DB::commit();
            return redirect()->route('roles.index')->with('success', 'Role created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create role: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show the form for editing the specified role.
     */
    public function editRole(Role $role)
    {
        $permissions = Permission::all()->groupBy(function($permission) {
            $parts = explode('-', $permission->name);
            return count($parts) > 1 ? ucfirst($parts[1]) : 'General';
        });
        
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        
        return view('RolePermission::roles.form', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Update the specified role.
     */
    public function updateRole(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        DB::beginTransaction();
        try {
            $role->update([
                'name' => $validated['name']
            ]);

            $role->syncPermissions($validated['permissions'] ?? []);

            DB::commit();
            return redirect()->route('roles.index')->with('success', 'Role updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update role: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified role.
     */
    public function destroyRole(Role $role)
    {
        // Prevent deletion of superadmin role
        if ($role->name === 'superadmin') {
            return back()->with('error', 'Cannot delete superadmin role!');
        }

        try {
            $role->delete();
            return redirect()->route('roles.index')->with('success', 'Role deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete role: ' . $e->getMessage());
        }
    }

    /**
     * Display permissions management page.
     */
    public function permissionsIndex(Request $request)
    {
        $selectedRole = null;
        $roles = Role::all();
        
        if ($request->has('role')) {
            $selectedRole = Role::with('permissions')->findOrFail($request->role);
        }
        
        $permissions = Permission::all()->groupBy(function($permission) {
            $parts = explode('-', $permission->name);
            return count($parts) > 1 ? ucfirst($parts[1]) : 'General';
        });
        
        return view('RolePermission::permissions.index', compact('permissions', 'roles', 'selectedRole'));
    }

    /**
     * Update permissions for a role.
     */
    public function updatePermissions(Request $request)
    {
        $validated = $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        try {
            $role = Role::findOrFail($validated['role_id']);
            $role->syncPermissions($validated['permissions'] ?? []);
            
            return redirect()->route('permissions.index', ['role' => $role->id])
                ->with('success', 'Permissions updated successfully for ' . $role->name . '!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update permissions: ' . $e->getMessage());
        }
    }
}
