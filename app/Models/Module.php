<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Module extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Module::class, 'parent_id')->with('parent');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Module::class, 'parent_id')
        ->orderBy('display_order')
        ->with('children');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function permissions(): HasMany
    {
        return $this->hasMany(Permission::class);
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

    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($parent) {
            $parent->children()->each(function ($child) {
                $child->delete();
            });
            $parent->permissions()->each(function ($permission) {
                $permission->delete();
            });
        }); 
    }
}
