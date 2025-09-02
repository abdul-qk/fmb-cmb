<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorBank extends BaseModel
{
  protected $guarded = [];

  public function createdBy(): BelongsTo
  {
    return $this->belongsTo(User::class,'created_by');
  }

  public function updatedBy(): BelongsTo
  {
    return $this->belongsTo(User::class, 'updated_by');
  }
  public function vendor(): BelongsTo
  {
    return $this->belongsTo(Vendor::class, 'vendor_id');
  }
}
