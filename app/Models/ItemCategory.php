<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemCategory extends BaseModel
{
    protected $guarded = [];

  public function items(): HasMany
  {
    return $this->hasMany(Item::class, 'category_id');
  }
  public function createdBy(): BelongsTo
  {
  return $this->belongsTo(User::class,'created_by');
  }
  public function updatedBy(): BelongsTo
  {
  return $this->belongsTo(User::class, 'updated_by');
  }

  // Boot method to handle cascading soft deletes
  protected static function boot()
  {
    parent::boot();

    static::deleting(function ($category) {
      $category->items()->each(function ($item) {
        $item->delete();
      });
    });
  }
}
