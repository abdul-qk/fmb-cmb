<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Item extends BaseModel
{
    protected $guarded = [];

  public function itemCategory(): BelongsTo
  {
    return $this->belongsTo(ItemCategory::class, 'category_id');
  }

  public function createdBy(): BelongsTo
  {
    return $this->belongsTo(User::class,'created_by');
  }

  public function updatedBy(): BelongsTo
  {
    return $this->belongsTo(User::class, 'updated_by');
  }

  public function vendors(): BelongsToMany
  {
    return $this->belongsToMany(Vendor::class);
  }
  public function recipeItems(): HasMany
  {
    return $this->hasMany(RecipeItem::class);
  }
  
  public function itemBase(): HasOne
  {
    return $this->hasOne(ItemBaseUom::class,"item_id");
  }

  public function purchaseOrderDetail(): HasOne
  {
    return $this->hasOne(PurchaseOrderDetail::class);
  }
  public function purchaseOrderDetails(): HasMany
  {
    return $this->hasMany(PurchaseOrderDetail::class);
  }

  public function inventoryDetails(): HasMany
  {
    return $this->hasMany(InventoryDetail::class);
  }
  public function inventoryDetail(): HasOne
  {
    return $this->hasOne(InventoryDetail::class);
  }
  public function supplierReturn(): HasOne
  {
    return $this->hasOne(SupplierReturn::class);
  }
  public function supplierReturns(): HasMany
  {
    return $this->hasMany(SupplierReturn::class);
  }
  
  public function detail(): HasOne
  {
    return $this->hasOne(ItemDetail::class);
  }

  protected static function boot()
  {
    parent::boot();
    static::deleting(function ($item) {
      $item->vendors()->detach();
    });
  }
}
