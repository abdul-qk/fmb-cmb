<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserDetail extends BaseModel
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
  public function experiences(): HasMany
  {
  return $this->hasMany(UserExperience::class, 'user_detail_id');
  }
  public function emails(): HasMany
  {
  return $this->hasMany(UserEmail::class, 'user_detail_id');
  }
  public function contacts(): HasMany
  {
  return $this->hasMany(UserContact::class, 'user_detail_id');
  }
}
