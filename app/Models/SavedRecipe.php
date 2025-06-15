<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavedRecipe extends Model
{
    // The table associated with the model.
    protected $table = 'saved_recipes'; // Optional if the table name matches the plural form of the model

    // Specify the fillable fields for mass assignment
    protected $fillable = ['user_id', 'recipe_id'];

    // Define the relationship with the User model (a saved recipe belongs to a user)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Define the relationship with the Recipe model (a saved recipe belongs to a recipe)
    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }
}
