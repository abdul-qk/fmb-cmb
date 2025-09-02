<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\Module;
use App\Models\Permission;

abstract class Controller
{
    protected $redirect, $view, $controller, $route, $routeAction, $method, $permission, $moduleSlug;

    public function __construct()
    {
        $this->route = Route::current();
        $this->routeAction =  Route::currentRouteAction();
        $this->moduleSlug = explode('/', $this->route->uri())[0];
        // $this->method = explode('@', $this->routeAction)[1];
        // $currectModule = Module::where('slug', $this->moduleSlug)->first();
        // $modulePermissions = Permission::where('module_id', $currectModule->id)->pluck('name')->toArray();
        // if (!Auth::user()->hasAnyPermission($modulePermissions)) {
        //     abort(403, 'Unauthorized action.');
        // }
        $this->redirect = $this->moduleSlug.'.index';
        $this->view = Str::replace('-', '_', $this->route->getName());
        $this->controller = $this->currentControllerName($this->routeAction);
    }

    public function currentControllerName($action)
    {
        $parts = explode('@', $action);
        $controllerName = $parts[0];
        $controllerName = class_basename($controllerName);
        return Str::replace('_', ' ', Str::title(Str::snake(Str::replace('Controller', '', $controllerName))));
    }
}
