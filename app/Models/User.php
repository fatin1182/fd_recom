<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'age',
        'gender',
        'height',
        'weight',
        'blood_pressure',
        'blood_sugar',
        'cholesterol'
    ];

    // Accessors for systolic/diastolic
    public function getBloodPressureSystolicAttribute()
    {
        if (!$this->blood_pressure) return null;
        return explode('/', $this->blood_pressure)[0] ?? null;
    }

    public function getBloodPressureDiastolicAttribute()
    {
        if (!$this->blood_pressure) return null;
        return explode('/', $this->blood_pressure)[1] ?? null;
    }

    // Mutator to ensure proper format
    public function setBloodPressureAttribute($value)
    {
        if (is_array($value)) { // If coming from separate fields
            $this->attributes['blood_pressure'] = "{$value['systolic']}/{$value['diastolic']}";
        } else {
            $this->attributes['blood_pressure'] = $value;
        }
    }

    public function savedRecipes()
    {
        return $this->belongsToMany(Recipe::class, 'saved_recipes');
    }


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
