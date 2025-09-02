<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use App\Models\Role;
use App\Http\Requests\User\UserRequest;
use App\Models\Place;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with(['roles','profile', 'createdBy', 'updatedBy'])->get();
        return view($this->view, compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $places = Place::get();
        $roles = Role::all();
        return view($this->view, compact('roles','places'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        try {
            DB::beginTransaction();
            $validated = $request->only(['name', 'email', 'password','place_id']);
            $role = $request->only('role')['role'];
            $user = User::createWithTransaction($validated);
            $user->assignRole($role);
            DB::commit();
            return redirect()->route($this->redirect)->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to create User: ' . $e->getMessage());
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
            $places = Place::get();
            $roles = Role::all();
            $user = User::findOrFail($id);
            $userRole = $user->first_role;
            return view($this->view, compact('roles', 'user', 'userRole','places'));
        } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Failed to find User: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, string $id)
    {
        try {
            $user = User::findOrFail($id);
            $validated = $request->only(['name', 'email', 'place_id']);

            if ($request->filled('password')) {
              $validated['password'] = $request->input('password');
            }
            $role = $request->only('role')['role'];
            User::updateWithTransaction($id, $validated);
            $user->syncRoles([$role]);
            return redirect()->route($this->redirect)->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Failed to update User: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            User::deleteWithTransaction($id);
            return redirect()->route($this->redirect)->with('success', 'User deleted successfully.');
          } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete User: ' . $e->getMessage());
        }
    }
}
