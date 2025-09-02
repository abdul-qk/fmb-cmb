<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission as SpatiePermission;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Scopes\OrderByIdDescScope;

class Permission extends SpatiePermission
{
    use SoftDeletes;

    protected $guarded = [];

    public function userPermission()
    {
      return $this->belongsToMany(Role::class, 'role_has_permissions', 'permission_id', 'role_id');
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Perform a database operation within a transaction.
     *
     * @param callable $callback
     * @return mixed
     */
    protected static function runInTransaction(callable $callback)
    {
        return DB::transaction($callback);
    }

    /**
     * Create a new record with a transaction.
     *
     * @param array $attributes
     * @return static
     */
    public static function createWithTransaction(array $attributes)
    {
        return static::runInTransaction(function () use ($attributes) {
            $instance = new static($attributes);
            $instance->created_by = Auth::id();
            $instance->updated_at = null;
            $instance->save();
            return $instance;
        });
    }

    public static function updateWithTransaction(int $id, array $attributes)
    {
        return static::runInTransaction(function () use ($id, $attributes) {
            $instance = static::findOrFail($id);
            $instance->fill($attributes);
            $instance->updated_by = Auth::id();
            $instance->save();
            return $instance;
        });
    }

    public static function deleteWithTransaction(int $id)
    {
        return static::runInTransaction(function () use ($id) {
            $instance = static::findOrFail($id);
            $instance->deleted_by = Auth::id();
            $instance->save();
            return $instance->delete();
        });
    }

    protected static function booted()
    {
        static::addGlobalScope(new OrderByIdDescScope);
    }

    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($permission) {
            Role::whereHas('permissions', function ($query) use ($permission) {
                $query->where('permission_id', $permission->id);
            })->each(function ($role) use ($permission) {
                $role->revokePermissionTo($permission);
            });
            User::whereHas('permissions', function ($query) use ($permission) {
                $query->where('permission_id', $permission->id);
            })->each(function ($user) use ($permission) {
                $user->revokePermissionTo($permission);
            });
        });
    }
}
