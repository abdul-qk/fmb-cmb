<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Dish extends BaseModel
{
    protected $guarded = [];

    public function dishCategory(): BelongsTo
    {
        return $this->belongsTo(DishCategory::class, 'category_id');
    }
    public function createdBy(): BelongsTo
    {
    return $this->belongsTo(User::class,'created_by');
    }
    public function updatedBy(): BelongsTo
    {
    return $this->belongsTo(User::class, 'updated_by');
    }
    public function recipes(): HasMany
    {
      return $this->hasMany(Recipe::class);
    }
    public function recipe(): HasOne
    {
      return $this->hasOne(Recipe::class);
    }
    public function menus(): BelongsToMany
    {
        return $this->belongsToMany(Menu::class);
    }
}
