<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Models\Scopes\OrderByIdDescScope;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    use Notifiable, HasRoles, SoftDeletes;

    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected $appends = [
        'first_role',
    ];

    protected function firstRole(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->roles()->first(), // Remove the string type hint
        );
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
        static::deleting(function ($user) {
            $user->getDirectPermissions()->each(function ($permission) use ($user) {
                $user->revokePermissionTo($permission);
            });
            $user->roles()->each(function ($role) use ($user) {
                $user->removeRole($role);
            });
        }); 
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class,'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
   function profile(): HasOne
    {
    return $this->hasOne(UserDetail::class, 'user_id');
    }

    public function place(): BelongsTo
    {
        return $this->belongsTo(Place::class);
    }
}
