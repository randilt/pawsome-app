<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'duration_in_days',
        'features',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'features' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the customer subscriptions for the subscription plan.
     */
    public function customerSubscriptions()
    {
        return $this->hasMany(CustomerSubscription::class);
    }

    /**
     * Scope a query to only include active subscription plans.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

