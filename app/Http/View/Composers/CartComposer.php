<?php

namespace App\Http\View\Composers;

use App\Services\CartService;
use Illuminate\View\View;

class CartComposer
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function compose(View $view): void
    {
        $view->with('cartCount', $this->cartService->getCartCount());
    }
}