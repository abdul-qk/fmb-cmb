<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inventory extends BaseModel
{
  protected $guarded = [];

  public function createdBy(): BelongsTo
  {
    return $this->belongsTo(User::class, 'created_by');
  }
  public function store(): BelongsTo
  {
    return $this->belongsTo(Store::class);
  }
  public function detail(): HasMany
  {
    return $this->hasMany(PurchaseOrderDetail::class);
  }
  public function approves(): BelongsTo
  {
    return $this->belongsTo(ApprovedPurchaseOrderDetail::class,Inventory::class,"approved_purchase_order_detail_id","id");
  }
}
