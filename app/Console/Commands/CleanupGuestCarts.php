<?php

namespace App\Console\Commands;

use App\Models\CartItem;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CleanupGuestCarts extends Command
{
    protected $signature = 'cart:cleanup-guest';
    protected $description = 'Clean up old guest cart items';

    public function handle()
    {
        $deletedCount = CartItem::where('user_id', null)
            ->where('created_at', '<', Carbon::now()->subDays(7))
            ->delete();

        $this->info("Cleaned up {$deletedCount} old guest cart items.");
        
        return 0;
    }
}