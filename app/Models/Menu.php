<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Menu extends BaseModel
{
  protected $guarded = [];

  public function event(): BelongsTo
  {
    return $this->belongsTo(Event::class, 'event_id');
  }

  public function menuServings(): HasMany
  {
    return $this->HasMany(MenuServing::class, 'menu_id');
  }

  public function createdBy(): BelongsTo
  {
    return $this->belongsTo(User::class, 'created_by');
  }
  public function updatedBy(): BelongsTo
  {
    return $this->belongsTo(User::class, 'updated_by');
  }
  public function place(): BelongsTo
  {
    return $this->belongsTo(Place::class);
  }

  public function dishes(): BelongsToMany
  {
    return $this->belongsToMany(Dish::class);
  }

  public function recipes(): BelongsToMany
  {
    return $this->belongsToMany(Recipe::class);
  }
  public function recipe(): BelongsTo
  {
    return $this->belongsTo(Recipe::class);
  }
  public function purchaseOrder(): HasOne
  {
    return $this->hasOne(PurchaseOrder::class, "menu_id");
  }
  public function chefMenuItems(): HasMany
  {
    return $this->hasMany(ChefRecipeItem::class, 'menu_id');
  }

  protected static function boot()
  {
    parent::boot();
    static::deleting(function ($result) {
      $result->menuServings()->each(function ($menuServing) {
        $menuServing->forceDelete();
      });
      if ($result->purchaseOrder) {
        $result->purchaseOrder->detail()->each(function ($detail) {
          // Delete approved detail if it exists
          if ($detail->approvedDetail) {
            $detail->approvedDetail->delete();
          }

          $detail->delete();
        });

        $result->purchaseOrder->delete();
      }
      $result->recipes()->detach();
      // $result->event()->detach();
    });
  }
}
