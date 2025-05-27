<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class ProductAnalytics extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'product_analytics';

    protected $fillable = [
        'product_id',
        'event_type',
        'user_id',
        'session_id',
        'user_agent',
        'ip_address',
        'referrer',
        'metadata',
        'timestamp'
    ];

    protected $casts = [
        'product_id' => 'integer',
        'user_id' => 'integer',
        'metadata' => 'array',
    ];

    protected $dates = ['timestamp', 'created_at', 'updated_at'];

    // Log product view
    public static function logView($productId, $userId = null, $metadata = [])
    {
        try {
            return static::create([
                'product_id' => (int)$productId,
                'event_type' => 'view',
                'user_id' => $userId ? (int)$userId : null,
                'session_id' => session()->getId(),
                'user_agent' => request()->userAgent(),
                'ip_address' => request()->ip(),
                'referrer' => request()->header('referer'),
                'metadata' => $metadata,
                'timestamp' => now()
            ]);
        } catch (\Exception $e) {
            \Log::error('ProductAnalytics logView error: ' . $e->getMessage());
            return null;
        }
    }

    // Log cart addition
    public static function logCartAdd($productId, $userId = null, $quantity = 1)
    {
        try {
            return static::create([
                'product_id' => (int)$productId,
                'event_type' => 'cart_add',
                'user_id' => $userId ? (int)$userId : null,
                'session_id' => session()->getId(),
                'metadata' => ['quantity' => $quantity],
                'timestamp' => now()
            ]);
        } catch (\Exception $e) {
            \Log::error('ProductAnalytics logCartAdd error: ' . $e->getMessage());
            return null;
        }
    }

    // Log purchase
    public static function logPurchase($productId, $userId, $orderId, $quantity, $price)
    {
        try {
            return static::create([
                'product_id' => (int)$productId,
                'event_type' => 'purchase',
                'user_id' => (int)$userId,
                'session_id' => session()->getId(),
                'metadata' => [
                    'order_id' => (int)$orderId,
                    'quantity' => $quantity,
                    'price' => (float)$price
                ],
                'timestamp' => now()
            ]);
        } catch (\Exception $e) {
            \Log::error('ProductAnalytics logPurchase error: ' . $e->getMessage());
            return null;
        }
    }

    // Get popular products by views
    public static function getPopularProducts($days = 30, $limit = 10)
    {
        try {
            $cutoffDate = now()->subDays($days);
            $views = static::where('event_type', 'view')
                ->where('timestamp', '>=', $cutoffDate)
                ->get();
            
            $productViews = [];
            foreach ($views as $view) {
                $productId = $view->product_id;
                if (!isset($productViews[$productId])) {
                    $productViews[$productId] = [
                        '_id' => $productId,
                        'views' => 0,
                        'unique_users' => []
                    ];
                }
                $productViews[$productId]['views']++;
                if ($view->user_id) {
                    $productViews[$productId]['unique_users'][] = $view->user_id;
                }
            }

            foreach ($productViews as &$product) {
                $product['unique_user_count'] = count(array_unique($product['unique_users']));
                unset($product['unique_users']);
            }

            usort($productViews, function($a, $b) {
                return $b['views'] <=> $a['views'];
            });

            return array_slice($productViews, 0, $limit);
        } catch (\Exception $e) {
            \Log::error('ProductAnalytics getPopularProducts error: ' . $e->getMessage());
            return [];
        }
    }

    // Get conversion funnel for a product
    public static function getConversionFunnel($productId, $days = 30)
    {
        try {
            $cutoffDate = now()->subDays($days);
            
            $views = static::where('product_id', (int)$productId)
                ->where('event_type', 'view')
                ->where('timestamp', '>=', $cutoffDate)
                ->count();
                
            $cartAdds = static::where('product_id', (int)$productId)
                ->where('event_type', 'cart_add')
                ->where('timestamp', '>=', $cutoffDate)
                ->count();
                
            $purchases = static::where('product_id', (int)$productId)
                ->where('event_type', 'purchase')
                ->where('timestamp', '>=', $cutoffDate)
                ->count();

            return [
                ['_id' => 'view', 'count' => $views],
                ['_id' => 'cart_add', 'count' => $cartAdds],
                ['_id' => 'purchase', 'count' => $purchases]
            ];
        } catch (\Exception $e) {
            \Log::error('ProductAnalytics getConversionFunnel error: ' . $e->getMessage());
            return [];
        }
    }
}