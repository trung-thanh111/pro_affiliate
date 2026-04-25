<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Services\V1\Core\CartService;
use Gloudemans\Shoppingcart\Facades\Cart;

class CartComposer
{

    protected $cartService;
    protected static $cartData = null;

    public function __construct(
        CartService $cartService,
    ) {
        $this->cartService = $cartService;
    }

    public function compose(View $view)
    {
        if (static::$cartData === null) {
            $carts = Cart::instance('shopping')->content();
            $carts = $this->cartService->remakeCart($carts);
            $cartCaculate = $this->cartService->reCaculateCart();
            $wishlistCount = Cart::instance('wishlist')->count();
            $compareCount = Cart::instance('compare')->count();

            static::$cartData = [
                'cartShare' => $cartCaculate,
                'wishlistCount' => $wishlistCount,
                'compareCount' => $compareCount,
            ];
        }

        $view->with('cartShare', static::$cartData['cartShare']);
        $view->with('wishlistCount', static::$cartData['wishlistCount']);
        $view->with('compareCount', static::$cartData['compareCount']);
    }
}
