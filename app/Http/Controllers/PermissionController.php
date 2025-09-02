<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Module;
use App\Models\Role;
use App\Models\User;
use App\Http\Requests\Permission\PermissionRequest;
use Illuminate\Support\Facades\DB;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $permissions = Permission::with(['module', 'createdBy', 'updatedBy'])->get();
        return view($this->view, compact('permissions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $modules = Module::all();
        return view($this->view, compact('modules'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PermissionRequest $request)
    {
        try {
            $validated = $request->validated();
            Permission::createWithTransaction($validated);
            return redirect()->route($this->redirect)->with('success', 'Permission created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create Permission: ' . $e->getMessage());
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
            $permission = Permission::with('module')
            ->findOrFail($id);
            return view($this->view, compact('permission'));
        } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Failed to find Permission: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PermissionRequest $request, string $id)
    {
        try {
            $validated = $request->validated();
            Permission::updateWithTransaction($id, $validated);
            return redirect()->route($this->redirect)->with('success', 'Permission updated successfully.');
        } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Failed to update Permission: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $permission = Permission::findOrFail($id);
            Permission::deleteWithTransaction($id);
            return redirect()->route($this->redirect)->with('success', 'Permission deleted successfully.');
          } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete Permission: ' . $e->getMessage());
        }
    }
}
