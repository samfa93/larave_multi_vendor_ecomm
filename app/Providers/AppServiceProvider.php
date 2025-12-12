<?php

namespace App\Providers;

use App\Http\Controllers\Frontend\WishlistController;
use App\Models\BannerAd;
use App\Models\Wishlist;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        Gate::before(function ($user, $ability) {
            return $user->hasRole('Super Admin') ? true : null;
        });

        view()->composer('*', function ($view) {


            $wishlist = Wishlist::where('user_id', auth('web')?->user()?->id);
            $ads = BannerAd::all()->groupBy('banner_id');
            $view->with([
                'wishlistCount' => $wishlist->count() ?? 0,
                'wishlistsProductIds' => $wishlist->pluck('product_id')->toArray() ?? []
            ]);
            $view->with('ads', $ads);
        });
    }
}
