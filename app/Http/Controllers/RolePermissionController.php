<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionController extends Controller
{
    public function index(): View
    {
        $roles = Role::with(['permissions'])->withCount('users')->get();
        $permissions = Permission::with(['roles'])->get();
        $users = User::with(['roles', 'permissions', 'studyProgram'])->paginate(15);

        return view('superadmin.roles-permissions.index', compact('roles', 'permissions', 'users'));
    }

    public function storeRole(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,name'],
        ]);

        $role = Role::create([
            'name' => trim($validated['name']),
            'guard_name' => 'web',
        ]);

        if (!empty($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->back()->with('success', "Peran '{$role->name}' berhasil ditambahkan.");
    }

    public function updateRole(Request $request, Role $role): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name,' . $role->id],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,name'],
        ]);

        $role->update(['name' => trim($validated['name'])]);
        $role->syncPermissions($validated['permissions'] ?? []);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->back()->with('success', "Peran '{$role->name}' dan izin aksesnya berhasil diperbarui.");
    }

    public function destroyRole(Role $role): RedirectResponse
    {
        if (in_array($role->name, ['Superadmin', 'Admin Prodi', 'Peserta'])) {
            return redirect()->back()->with('error', "Peran bawaan sistem ('{$role->name}') tidak dapat dihapus!");
        }

        $roleName = $role->name;
        $role->delete();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->back()->with('success', "Peran '{$roleName}' berhasil dihapus.");
    }

    public function storePermission(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name'],
        ]);

        $permission = Permission::create([
            'name' => trim($validated['name']),
            'guard_name' => 'web',
        ]);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->back()->with('success', "Izin akses '{$permission->name}' berhasil ditambahkan.");
    }

    public function updatePermission(Request $request, Permission $permission): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name,' . $permission->id],
        ]);

        $permission->update(['name' => trim($validated['name'])]);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->back()->with('success', "Izin akses '{$permission->name}' berhasil diperbarui.");
    }

    public function destroyPermission(Permission $permission): RedirectResponse
    {
        $permissionName = $permission->name;
        $permission->delete();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->back()->with('success', "Izin akses '{$permissionName}' berhasil dihapus.");
    }

    public function updateUserPermissions(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', 'exists:roles,name'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,name'],
        ]);

        $user->syncRoles([$validated['role']]);
        $user->syncPermissions($validated['permissions'] ?? []);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->back()->with('success', "Peran dan hak akses khusus untuk pengguna '{$user->name}' berhasil diperbarui.");
    }
}
