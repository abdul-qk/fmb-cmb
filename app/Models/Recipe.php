<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Recipe extends BaseModel
{
  protected $guarded = [];
  public function dish(): BelongsTo
  {
    return $this->belongsTo(Dish::class, 'dish_id');
  }
  public function dishCategory(): BelongsTo
  {
    return $this->belongsTo(DishCategory::class, 'category_id');
  }
  
  public function place(): BelongsTo
  {
    return $this->belongsTo(Place::class, 'place_id');
  }
  public function chefRecipeItems(): HasMany
  {
    return $this->hasMany(ChefRecipeItem::class, 'recipe_id');
  }
  


  public function location()
  {
    return $this->hasOneThrough(
      Location::class,
      Place::class,
      'id', // Foreign key on Place
      'id', // Foreign key on Location
      'place_id', // Local key on current model
      'location_id' // Local key on Place
    );
  }

  public function recipeItem(): BelongsTo
  {
    return $this->belongsTo(RecipeItem::class, 'id');
  }
  public function recipeItems()
  {
    return $this->hasMany(RecipeItem::class);
  }
  public function createdBy(): BelongsTo
  {
  return $this->belongsTo(User::class,'created_by');
  }
  public function updatedBy(): BelongsTo
  {
  return $this->belongsTo(User::class, 'updated_by');
  }
  public function chefUser(): BelongsTo
  {
    return $this->belongsTo(User::class, 'chef');
  }
}
