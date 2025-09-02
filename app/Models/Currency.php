<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Currency extends BaseModel
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

  // protected static function boot()
  // {
  //   parent::boot();
  //   static::deleting(function ($item) {
  //     $item->vendors()->detach();
  //   });
  // }
}
