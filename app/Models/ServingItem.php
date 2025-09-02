<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServingItem extends BaseModel
{
  protected $guarded = [];
  
  public function tiffinSize(): BelongsTo
  {
    return $this->belongsTo(TiffinSize::class, 'tiffin_size_id');
  }

  public function event(): BelongsTo
  {
    return $this->belongsTo(Event::class);
  }
}
