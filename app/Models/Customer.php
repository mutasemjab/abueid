<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Storefront customer. Identified by a Jordanian mobile number (07XXXXXXXX) and a password.
 */
class Customer extends Authenticatable
{
    protected $fillable = ['name', 'phone', 'password'];

    protected $hidden = ['password', 'remember_token'];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(CustomerAddress::class)->orderByDesc('is_default')->latest('id');
    }

    public function favourites(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'customer_favourites');
    }
}
