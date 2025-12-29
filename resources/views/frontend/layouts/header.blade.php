@php
    $customPages = \App\Models\CustomPage::where('is_active', true)->get();
    $offerSliders = \App\Models\OfferSlider::where('is_active', true)->get();
@endphp

<header class="header-area header-style-1 header-style-5 header-height-2 d-print-none">


    <div class="mobile-promotion">
        <div id="news-flash-mobile" class="d-inline-block">
            <ul>
                @foreach ($offerSliders as $offerSlider)
                    <li><a href="{{ $offerSlider->url }}" class="text-dark">{{ $offerSlider->title }}</a></li>
                @endforeach
            </ul>
        </div>
    </div>

    {{-- Optional: show only on lg+ like your original --}}
    <div class="d-none d-lg-block border-bottom bg-body">
        <div class="container py-2">
            <div class="row align-items-center g-2">

                {{-- Left: account / wishlist / tracking --}}
                <div class="col-lg-6 col-xl-5">
                    <ul class="list-inline mb-0 small {{ app()->getLocale() == 'ar' ? 'text-end' : 'text-start' }}">
                        <li class="list-inline-item">
                            <a class="text-decoration-none link-secondary" href="{{ route('dashboard') }}">
                                {{ __('messages.my_account') }}
                            </a>
                        </li>
                        <li class="list-inline-item ms-3">
                            <a class="text-decoration-none link-secondary" href="{{ route('wishlist.index') }}">
                                {{ __('messages.wishlist') }}
                            </a>
                        </li>
                        <li class="list-inline-item ms-3">
                            <a class="text-decoration-none link-secondary" href="{{ route('track.order.index') }}">
                                {{ __('messages.order_tracking') }}
                            </a>
                        </li>
                    </ul>
                </div>

                {{-- Center: offers (simple scrolling fallback; no custom JS required) --}}
                <div class="col-xl-4 d-none d-xl-block">
                    <div class="text-center small">
                        <div class="d-inline-block text-truncate" style="max-width: 100%;">
                            {{-- If you want a ticker effect, keep JS elsewhere. This is Bootstrap-only display. --}}
                            @foreach ($offerSliders as $offerSlider)
                                <a class="text-decoration-none link-dark me-3"
                                href="{{ $offerSlider->url }}">
                                    {{ $offerSlider->title }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Right: phone + language --}}
                <div class="col-lg-6 col-xl-3">
                    <div class="d-flex flex-wrap justify-content-lg-end align-items-center gap-2 small text-end">

                        <div class="text-nowrap">
                            {{ __('messages.need_help_call') }}
                            <strong>{{ config('settings.site_phone') }}</strong>
                        </div>

                        <span class="d-none d-lg-inline border-start ps-2"></span>

                        <div class="text-nowrap">
                            <a class="text-decoration-none link-secondary"
                            href="{{ route('lang.switch', 'en') }}">
                                {{ __('messages.language_en') }}
                            </a>
                            <span class="mx-1">|</span>
                            <a class="text-decoration-none link-secondary"
                            href="{{ route('lang.switch', 'ar') }}">
                                {{ __('messages.language_ar') }}
                            </a>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <div dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}" class="header-middle header-middle-ptb-1 d-none d-lg-block">
        <div class="container">
            <div class="header-wrap">
                <div class="logo logo-width-1">
                    <a href="{{ url('/') }}"><img src="{{ asset(config('settings.site_logo')) }}"
                            alt="logo" /></a>
                </div>
                <div class="header-right">
                    <div class="search-style-2">
                        <form action="{{ route('products.index') }}">
                            <select class="select-active" name="category">
                                <option value="">{{ __('messages.all_categories') }}</option>
                                @foreach (getNestedCategories() as $category)
                                    <option @selected(request('category') == $category->slug) value="{{ $category->slug }}">
                                        {{ $category->name }}</option>
                                @endforeach

                            </select>
                            <input type="text" name="search" placeholder="{{ __('messages.search_for_items') }}"
                                value="{{ request('search') }}" />
                        </form>
                    </div>
                    <div class="header-action-right">
                        <div class="header-action-2">

                            <div class="header-action-icon-2">
                                <a href="#">
                                    <img class="svgInject" alt="ShopX"
                                        src="{{ asset('assets/frontend/dist/imgs/theme/icons/icon-heart.svg') }}" />
                                    <span class="pro-count blue">{{ wishlistCount() }}</span>
                                </a>
                                <a href="{{ route('wishlist.index') }}"><span class="lable">{{ __('messages.wishlist') }}</span></a>
                            </div>
                            <div class="header-action-icon-2">
                                <a class="mini-cart-icon" href="#">
                                    <img alt="ShopX"
                                        src="{{ asset('assets/frontend/dist/imgs/theme/icons/icon-cart.svg') }}" />
                                    <span class="pro-count blue cart-count">{{ cartCount() }}</span>
                                </a>
                                <a href="{{ route('cart.index') }}"><span class="lable">{{ __('messages.cart') }}</span></a>

                            </div>
                            <div class="header-action-icon-2">
                                <a href="{{ route('login') }}">
                                    <img class="svgInject" alt="ShopX"
                                        src="{{ asset('assets/frontend/dist/imgs/theme/icons/icon-user.svg') }}" />
                                </a>
                                <a href=""><span class="lable ml-0">{{ __('messages.account') }}</span></a>
                                @php
                                    $marginClass = app()->getLocale() == 'ar' ? 'ml-10' : 'mr-10';
                                @endphp

                                @if (Auth::guard('web')->check())
                                    <div class="cart-dropdown-wrap cart-dropdown-hm2 account-dropdown">
                                        <ul>
                                            <li>
                                                <a href="{{ route('dashboard') }}">
                                                    <i class="fi fi-rs-user {{ $marginClass }}"></i>{{ __('messages.dashboard') }}
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('profile') }}">
                                                    <i class="fi fi-rs-user {{ $marginClass }}"></i>{{ __('messages.profile') }}
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('orders.index') }}">
                                                    <i class="fi fi-rs-location-alt {{ $marginClass }}"></i>{{ __('messages.order_tracking') }}
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('wishlist.index') }}">
                                                    <i class="fi fi-rs-heart {{ $marginClass }}"></i>{{ __('messages.my_wishlist') }}
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" onclick="event.preventDefault(); $('.form-logout').submit()">
                                                    <i class="fi fi-rs-sign-out {{ $marginClass }}"></i>{{ __('messages.sign_out') }}
                                                </a>
                                            </li>
                                            <form class="form-logout" action="{{ route('logout') }}" method="POST">
                                                @csrf
                                            </form>
                                        </ul>
                                    </div>
                                @else
                                    <div class="cart-dropdown-wrap cart-dropdown-hm2 account-dropdown">
                                        <ul>
                                            <li>
                                                <a href="{{ route('login') }}">
                                                    <i class="fi fi-rs-user {{ $marginClass }}"></i>{{ __('messages.login') }}
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('register') }}">
                                                    <i class="fi fi-rs-user {{ $marginClass }}"></i>{{ __('messages.register') }}
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}" class="header-bottom header-bottom-bg-color sticky-bar">
        <div class="container">
            <div class="header-wrap header-space-between position-relative">
                <div class="logo logo-width-1 d-block d-lg-none">
                    <a href="{{ url('/') }}"><img src="{{ asset(config('settings.site_logo')) }}" alt="logo" /></a>
                </div>
                <div class="header-nav d-none d-lg-flex">
                    <div class="main-categori-wrap d-none d-lg-block">
                        <a class="categories-button-active" href="#">
                            <span class="fi-rs-apps"></span> {{ __('messages.trending_categories') }}
                            <i class="fi-rs-angle-down"></i>
                        </a>
                        <div
                            class="categories-dropdown-wrap style-2 font-heading categories-dropdown-active-large font-heading">
                            <div class="d-flex categori-dropdown-inner">
                                <ul>
                                    @foreach (getNestedCategories() as $category)
                                        @if ($loop->iteration <= 11)
                                        <li>
                                            <a href="{{ route('products.index', ['category' => $category->slug]) }}">
                                                <img src="{{ asset($category->icon) }}" alt="" />
                                                <span>{{ app()->getLocale() == 'ar' ? $category->name_ar : $category->name }}</span>
                                            </a>

                                            @if (count($category->children_nested) > 0)
                                                <ul>
                                                    @foreach ($category->children_nested as $child)
                                                        <li class="{{ count($child->children_nested) > 0 ? '' : 'no_child' }}">
                                                            <a href="{{ route('products.index', ['category' => $child->slug]) }}">
                                                                {{ app()->getLocale() == 'ar' ? $child->name_ar : $child->name }}
                                                            </a>

                                                            @if (count($child->children_nested) > 0)
                                                                <ul>
                                                                    @foreach ($child->children_nested as $subchild)
                                                                        <li class="no_child">
                                                                            <a href="{{ route('products.index', ['category' => $subchild->slug]) }}">
                                                                                {{ app()->getLocale() == 'ar' ? $subchild->name_ar : $subchild->name }}
                                                                            </a>
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            @endif
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                            <a href="#" class="more_categories">
                                {{ __('messages.view_all') }}
                                <i class="fa-solid {{ app()->getLocale() == 'ar' ? 'fa-arrow-left' : 'fa-arrow-right' }}"></i>
                            </a>
                        </div>
                    </div>
                    <div class="main-menu main-menu-padding-1 main-menu-lh-2 d-none d-lg-block font-heading">
                        <nav>
                            <ul>
                                <li>
                                    <a class="active" href="{{ url('/') }}">{{ __('messages.home') }}</a>
                                </li>

                                <li>
                                    <a href="{{ route('products.index') }}">{{ __('messages.products') }}</a>
                                </li>

                                <li>
                                    <a href="{{ route('vendors.index') }}">{{ __('messages.vendors') }}</a>
                                </li>


                                <li class="hot-deals">
                                    <!-- <img src="assets/imgs/theme/icons/icon-hot-white.svg" alt="hot deals"> -->
                                    <a href="{{ route('flash-sales.index') }}">{{ __('messages.flash_sale') }}</a>
                                </li>

                                <li>
                                    <a href="{{ route('contact.index') }}">{{ __('messages.contact') }}</a>
                                </li>

                                @foreach ($customPages as $page)
                                    <li class="hot-deals">
                                        <a href="{{ route('custom-page.index', ['slug' => $page->slug]) }}">
                                            {{ app()->getLocale() == 'ar' ? $page->title_ar : $page->title }}
                                        </a>
                                    </li>
                                @endforeach

                            </ul>
                        </nav>
                    </div>
                </div>
                <div class="hotline d-none d-lg-flex">
                    <img src="{{ asset('assets/frontend/dist/imgs/theme/icons/icon-headphone-white.svg') }}"
                        alt="hotline" />
                    <p>{{ config('settings.site_phone') }}<span>{{ __('messages.support_center') }}</span></p>
                </div>
                <div class="header-action-icon-2 d-block d-lg-none">
                    <div class="burger-icon burger-icon-white">
                        <span class="burger-icon-top"></span>
                        <span class="burger-icon-mid"></span>
                        <span class="burger-icon-bottom"></span>
                    </div>
                </div>
                <div class="header-action-right d-block d-lg-none">
                    <div class="header-action-2">
                        <div class="header-action-icon-2">
                            <a href="{{ route('wishlist.index') }}">
                                <img alt="ShopX"
                                    src="{{ asset('assets/frontend/dist/imgs/theme/icons/icon-heart.svg') }}" />
                                <span class="pro-count white">{{ wishlistCount() }}</span>
                            </a>
                        </div>
                        <div class="header-action-icon-2">
                            <a class="mini-cart-icon" href="{{ route('cart.index') }}">
                                <img alt="ShopX"
                                    src="{{ asset('assets/frontend/dist/imgs/theme/icons/icon-cart.svg') }}" />
                                <span class="pro-count white cart-count">{{ cartCount() }}</span>
                            </a>
                        </div>
                        <div class="header-action-icon-2">
                            <a class="mini-cart-icon" href="{{ route('login') }}">
                                <img alt="ShopX" src="{{ asset('assets/frontend/dist/imgs/theme/icons/icon-user.svg') }}" />                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
<div class="mobile-header-active mobile-header-wrapper-style">
    <div class="mobile-header-wrapper-inner">
        <div class="mobile-header-top">
            <div class="mobile-header-logo">
                <a href="{{ url('/') }}"><img src="{{ asset(config('settings.site_logo')) }}"
                        alt="logo" /></a>
            </div>
            <div class="mobile-menu-close close-style-wrap close-style-position-inherit">
                <button class="close-style search-close">
                    <i class="icon-top"></i>
                    <i class="icon-bottom"></i>
                </button>
            </div>
        </div>
        <div class="mobile-header-content-area">
            <div class="mobile-search search-style-3 mobile-header-border">
                <form action="{{ route('products.index') }}">
                    <input type="text" placeholder="{{ __('messages.search_for_items') }}" name="search" />
                    <button type="submit"><i class="fi-rs-search"></i></button>
                </form>
            </div>
            <div class="mobile-menu-wrap mobile-header-border">
                <!-- mobile menu start -->
                <nav>
                    <ul class="mobile-menu font-heading">
                        <li class="">
                            <a href="{{ route('home.index') }}">{{ __('messages.home') }}</a>
                        </li>

                        <li class="">
                            <a href="{{ route('products.index') }}">{{ __('messages.products') }}</a>
                        </li>
                        <li class="">
                            <a href="{{ route('vendors.index') }}">{{ __('messages.vendors') }}</a>
                        </li>
                        <li class="">
                            <a href="{{ route('flash-sales.index') }}">{{ __('messages.flash_sale') }}</a>
                        </li>
                        <li class="">
                            <a href="{{ route('contact.index') }}">{{ __('messages.contact') }}</a>
                        </li>

                        <li class="menu-item-has-children">
                            <a href="index.html">{{ __('messages.categories') }}</a>
                            <ul class="dropdown">
                                @foreach (getNestedCategories() as $category)
                                    <li>
                                        <a href="{{ route('products.index', ['category' => $category->slug]) }}">
                                            {{ app()->getLocale() == 'ar' ? $category->name_ar : $category->name }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>

                    </ul>
                </nav>
                <!-- mobile menu end -->
            </div>
            @php
                $socialLinks = App\Models\SocialLink::whereStatus(true)->get();
            @endphp
            <div class="mobile-social-icon mb-50">
                <h6 class="mb-15">{{ __('messages.follow_us') }}</h6>
                @foreach ($socialLinks as $socialLink)
                    <a href="{{ $socialLink->url }}"><img src="{{ asset($socialLink->icon) }}"
                            alt="" /></a>
                @endforeach
            </div>
            <div class="site-copyright">{{ config('settings.site_copyright') }}</div>
        </div>
    </div>
</div>
