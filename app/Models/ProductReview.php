<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class ProductReview extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'product_reviews';

    protected $fillable = [
        'product_id',
        'user_id',
        'user_name',
        'rating',
        'title',
        'comment',
        'verified_purchase',
        'helpful_votes',
        'metadata',
    ];

    protected $casts = [
        'product_id' => 'integer',
        'user_id' => 'integer',
        'rating' => 'integer',
        'verified_purchase' => 'boolean',
        'helpful_votes' => 'integer',
        'metadata' => 'array',
    ];

    // Get average rating for a product
    public static function getProductAverageRating($productId)
    {
        try {
            $reviews = static::where('product_id', (int)$productId)->get();
            if ($reviews->isEmpty()) {
                return 0;
            }
            $total = $reviews->sum('rating');
            return round($total / $reviews->count(), 1);
        } catch (\Exception $e) {
            \Log::error('ProductReview getProductAverageRating error: ' . $e->getMessage());
            return 0;
        }
    }

    // Get rating distribution for a product
    public static function getProductRatingDistribution($productId)
    {
        try {
            $reviews = static::where('product_id', (int)$productId)->get();
            $distribution = $reviews->groupBy('rating')->map->count();
            
            $result = [];
            foreach ($distribution as $rating => $count) {
                $result[] = ['_id' => (int)$rating, 'count' => $count];
            }
            
            return $result;
        } catch (\Exception $e) {
            \Log::error('ProductReview getProductRatingDistribution error: ' . $e->getMessage());
            return [];
        }
    }

    // Get reviews for a product
    public static function getProductReviews($productId, $limit = 10)
    {
        try {
            return static::where('product_id', (int)$productId)
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        } catch (\Exception $e) {
            \Log::error('ProductReview getProductReviews error: ' . $e->getMessage());
            return collect();
        }
    }
}