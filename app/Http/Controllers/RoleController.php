<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Module;
use App\Models\Permission;
use App\Http\Requests\Role\RoleRequest;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::with(['createdBy', 'updatedBy'])->get();
        return view($this->view, compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $modules = Module::with('permissions')->get();
        return view($this->view, compact('modules'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RoleRequest $request)
    {
        try {
            DB::beginTransaction();
            $roleName = $request->only('name');
            $modulePermissions = $request->only('modules');
            $role = Role::createWithTransaction($roleName);
            foreach ($modulePermissions['modules'] as $moduleId => $permissions) {
                foreach ($permissions as $permissionId) {
                    $permission = Permission::findById($permissionId);
                    if ($permission) {
                        $role->givePermissionTo($permission);
                    }
                }
            }
            DB::commit();
            return redirect()->route($this->redirect)->with('success', 'Role created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to create Role: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $role = Role::findOrFail($id);
            $modules = Module::with('permissions')->get();
            $rolePermissions = $role->permissions->pluck('id')->toArray();
            return view($this->view, compact('role', 'modules', 'rolePermissions'));
        } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Failed to find Role: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RoleRequest $request, string $id)
    {
        try {
            DB::beginTransaction();
            $roleName = $request->only('name');
            $modulePermissions = $request->only('modules');
            Role::updateWithTransaction($id, $roleName);
            $role = Role::findById($id);
            $role->syncPermissions([]);
            foreach ($modulePermissions['modules'] as $moduleId => $permissions) {
                foreach ($permissions as $permissionId) {
                    $permission = Permission::findById($permissionId);
                    if ($permission) {
                        $role->givePermissionTo($permission);
                    }
                }
            }
            DB::commit();
            return redirect()->route($this->redirect)->with('success', 'Role updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
        return redirect()->back()->with('error', 'Failed to update Role: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            Role::deleteWithTransaction($id);
            return redirect()->route($this->redirect)->with('success', 'Role deleted successfully.');
          } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete Role: ' . $e->getMessage());
        }
    }
}
