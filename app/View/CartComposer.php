<?php

namespace App\View;

use App\Models\Cart;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CartComposer
{
    /**
     * Share cartCount & wishlistCount ke navigation layout.
     */
    public function compose(View $view): void
    {
        $cartCount = 0;
        $wishlistCount = 0;

        if (Auth::check()) {
            $cartCount = Cart::where('user_id', Auth::id())->sum('quantity');
            $wishlistCount = Wishlist::where('user_id', Auth::id())->count();
        }

        $view->with('cartCount', $cartCount);
        $view->with('wishlistCount', $wishlistCount);
    }
}
