@extends('frontend.layouts.app')

@section('contents')
    <x-frontend.breadcrumb :items="[['label' => __('messages.home'), 'url' => '/'], ['label' => __('messages.dashboard')]]" />
    <div class="page-content pt-70 pb-60">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="row">
                        <div class="col-md-3 d-print-none">
                            <div class="dashboard-menu">
                                @php
                                    $marginClass = app()->getLocale() == 'ar' ? 'ml-10' : 'mr-10';
                                @endphp
                                <ul class="nav flex-column" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link {{ setActive(['dashboard']) }}" href="{{ route('dashboard') }}">
                                            <i class="fi-rs-settings-sliders {{ $marginClass }}"></i>{{ __('messages.dashboard') }}
                                        </a>
                                    </li>

                                    <li class="nav-item">
                                        <a class="nav-link {{ setActive(['orders.*']) }}" href="{{ route('orders.index') }}">
                                            <i class="fi-rs-shopping-bag {{ $marginClass }}"></i>{{ __('messages.orders') }}
                                        </a>
                                    </li>

                                    <li class="nav-item">
                                        <a class="nav-link {{ setActive(['purchased.*']) }}" href="{{ route('purchased.products') }}">
                                            <i class="fi-rs-shopping-bag {{ $marginClass }}"></i>{{ __('messages.purchased_products') }}
                                        </a>
                                    </li>

                                    <li class="nav-item">
                                        <a class="nav-link {{ setActive(['track.order.*']) }}" href="{{ route('track.order.index') }}">
                                            <i class="fi-rs-shopping-cart-check {{ $marginClass }}"></i>{{ __('messages.track_your_order') }}
                                        </a>
                                    </li>

                                    <li class="nav-item">
                                        <a class="nav-link {{ setActive(['reviews.*']) }}" href="{{ route('reviews.index') }}">
                                            <i class="fi fi-rs-star {{ $marginClass }}"></i>{{ __('messages.reviews') }}
                                        </a>
                                    </li>

                                    <li class="nav-item">
                                        <a class="nav-link {{ setActive(['wishlist.index']) }}" href="{{ route('wishlist.index') }}">
                                            <i class="fi fi-rs-star {{ $marginClass }}"></i>{{ __('messages.wishlist') }}
                                        </a>
                                    </li>

                                    <li class="nav-item">
                                        <a class="nav-link {{ setActive(['address.*']) }}" href="{{ route('address.index') }}">
                                            <i class="fi-rs-marker {{ $marginClass }}"></i>{{ __('messages.my_address') }}
                                        </a>
                                    </li>

                                    <li class="nav-item">
                                        <a class="nav-link {{ setActive(['profile']) }}" href="{{ route('profile') }}">
                                            <i class="fi-rs-user {{ $marginClass }}"></i>{{ __('messages.account_details') }}
                                        </a>
                                    </li>

                                    <li class="nav-item">
                                        <a class="nav-link" onclick="event.preventDefault(); $('.form-logout').submit()" href="#">
                                            <i class="fi-rs-sign-out {{ $marginClass }}"></i>{{ __('messages.sign_out') }}
                                        </a>
                                    </li>

                                    <form class="form-logout" action="{{ route('logout') }}" method="POST">
                                        @csrf
                                    </form>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="tab-content account dashboard-content pl-50">
                                @yield('dashboard_contents')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
