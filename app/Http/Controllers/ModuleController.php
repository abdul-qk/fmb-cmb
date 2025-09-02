<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Module\ModuleRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ModuleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
      $modules = Module::with(['parent', 'createdBy', 'updatedBy'])->where('is_active', 1)->get();
      return view($this->view, compact('modules'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $modules = Module::where('is_active', 1)->get();
        $moduleDisplayOrder = $modules->whereNull('parent_id')->sortByDesc('display_order')->pluck('display_order')->first() + 1;
        return view($this->view, compact('modules', 'moduleDisplayOrder'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ModuleRequest $request)
    {
        try {
            $validated = $request->validated();
            $validated["slug"] = Str::slug($validated['name']);
            Module::createWithTransaction($validated);
            return redirect()->route($this->redirect)->with('message', 'Module created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create Module: ' . $e->getMessage());
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
            $modules = Module::where('is_active', 1)->get();
            $editableModule = Module::findOrFail($id);
            return view($this->view, compact('modules', 'editableModule'));
          } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to find Module: ' . $e->getMessage());
          }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ModuleRequest $request, string $id)
    {
      try {
        $validated = $request->validated();
        Module::updateWithTransaction($id, $validated);
        return redirect()->route($this->redirect)->with('message', 'Module updated successfully.');
      } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Failed to update Module: ' . $e->getMessage());
      }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            Module::deleteWithTransaction($id);
            return redirect()->route($this->redirect)->with('message', 'Module deleted successfully.');
          } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete Module: ' . $e->getMessage());
        }
    }
}
