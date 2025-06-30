<?php

namespace App\Http\Controllers;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AdminController extends Controller
{
    public function roleView()
    {
        $permissions = Permission::all();
        return Inertia::render('Admin/Roles', [
            'permissions' => $permissions,
        ]);
    }

    public function permissionView()
    {
        return Inertia::render('Admin/Permissions');
    }

    public function roleTable()
    {
        $roles = Role::with('permissions')->get();
        return DataTables::of($roles)
            ->make(true);
    }

    public function permissionTable()
    {
        $permissions = Permission::all();
        return DataTables::of($permissions)
            ->make(true);
    }

    public function createRole(Request $request)
    {
        $input = $request->all();
        $validated = Validator::make($input, [
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'array',
        ])->validate();

        $role = Role::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);
        if (isset($validated['permissions'])) {
            $role->permissions()->sync($validated['permissions']);
        }

        return back()->with('success', 'Role created successfully.');
    }

    public function createPermission(Request $request)
    {
        $input = $request->all();
        $validated = Validator::make($input, [
            'name' => 'required|string|max:255|unique:permissions,name',
            'description' => 'nullable|string|max:255',
        ])->validate();

        Permission::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);

        return back()->with('success', 'Permission created successfully.');
    }

    public function updateRole(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        $input = $request->all();
        $validated = Validator::make($input, [
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'array',
        ])->validate();

        $role->name = $validated['name'];
        $role->save();

        if (isset($validated['permissions'])) {
            $role->permissions()->sync($validated['permissions']);
        }

        return back()->with('success', 'Role updated successfully.');
    }

    public function updatePermission(Request $request, $id)
    {
        $permission = Permission::findOrFail($id);
        $input = $request->all();
        $validated = Validator::make($input, [
            'name' => 'required|string|max:255|unique:permissions,name,' . $permission->id,
            'description' => 'nullable|string|max:255',
        ])->validate();

        $permission->name = $validated['name'];
        $permission->description = $validated['description'];
        $permission->save();

        return back()->with('success', 'Permission updated successfully.');
    }
}
