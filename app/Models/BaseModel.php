<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use App\Models\Scopes\OrderByIdDescScope;

abstract class BaseModel extends Model
{
    use SoftDeletes;
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
}
