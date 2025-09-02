<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorContactPerson extends BaseModel
{
  protected $guarded = [];
  protected $table = 'vendor_contact_persons';

  public function createdBy(): BelongsTo
  {
    return $this->belongsTo(User::class, 'created_by');
  }

  public function updatedBy(): BelongsTo
  {
    return $this->belongsTo(User::class, 'updated_by');
  }

  public function vendor()
  {
    return $this->belongsTo(Vendor::class);
  }
}
