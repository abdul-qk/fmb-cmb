<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use App\Models\Module;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (Schema::hasTable('modules')) {
            $sidebarModules = Module::where('is_active', 1)
            ->with('children')
            ->whereNull('parent_id')
            ->orderBy('display_order')
            ->get();
            View::share('sidebarModules', $sidebarModules);
        }
        View::composer('*', function ($view) {
            $viewName = $view->getName();
            if (strpos($viewName, 'layout.') === false) {
                $route = Route::current();
                if ($route) {
                    $moduleSlug = explode('/', $route->uri())[0];
                    $currentModuleId = Module::where('slug', $moduleSlug)->pluck('id')->first();
                    $indexRoute = $moduleSlug.'.index';
                    $create = $moduleSlug.'.create';
                    $store = $moduleSlug.'.store';
                    $edit = $moduleSlug.'.edit';
                    $update = $moduleSlug.'.update';
                    $destroy = $moduleSlug.'.destroy';
                    $show = $moduleSlug.'.show';
                    $title = Str::title(Str::replace('-', ' ', $moduleSlug));
                    $moduleNameSingular = Str::singular($title);
                    $view->with('title', $title)
                    ->with('indexRoute', $indexRoute)
                    ->with('create', $create)
                    ->with('store', $store)
                    ->with('edit', $edit)
                    ->with('update', $update)
                    ->with('show', $show)
                    ->with('destroy', $destroy)
                    ->with('currentModuleId', $currentModuleId)
                    ->with('moduleNameSingular', $moduleNameSingular);
                }
            }
        });
        Gate::before(function ($user, $ability) {
            return $user->hasRole('developer') ? true : null;
        });
    }
}
