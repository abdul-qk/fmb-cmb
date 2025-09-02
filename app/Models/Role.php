<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Scopes\OrderByIdDescScope;

class Role extends SpatieRole
{
    use SoftDeletes;

    protected $guarded = [];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'model_has_roles', 'role_id', 'model_id')
        ->where('model_type', User::class);
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
        static::deleting(function ($role) {
            $role->users()->each(function ($user) {
                $user->delete();
            });
            $role->permissions()->each(function ($permission) use ($role) {
                $role->revokePermissionTo($permission);
            });
        }); 
    }
}
