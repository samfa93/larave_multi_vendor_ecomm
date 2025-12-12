@extends('frontend.dashboard.dashboard-app')

@section('dashboard_contents')
<div class="tab-pane fade active show" id="dashboard" role="tabpanel" aria-labelledby="dashboard-tab">
    <div class="card">
        <div class="card-header p-0 pb-10">
            <h3 class="mb-0">{{ __('messages.hello_user', ['name' => user()->name]) }}</h3>
        </div>
        <div class="card-body p-0">
            <p>
                {{ __('messages.dashboard_intro') }}
                <a href="{{ route('orders.index') }}">{{ __('messages.recent_orders') }}</a>,<br />
                {{ __('messages.manage_your') }} <a href="{{ route('address.index') }}">{{ __('messages.shipping_billing_addresses') }}</a>
                {{ __('messages.and') }} <a href="{{ route('profile') }}">{{ __('messages.edit_password_account_details') }}</a>
            </p>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-4 col-sm-6">
            <div class="dashboard_card blue">
                <span><i class="fa-solid fa-cart-shopping"></i></span>
                <h3>{{ $totalOrders }}</h3>
                <p>{{ __('messages.total_order') }}</p>
            </div>
        </div>
        <div class="col-lg-4 col-sm-6">
            <div class="dashboard_card red">
                <span><i class="fa-solid fa-xmark"></i></span>
                <h3>{{ $totalCanceledOrders }}</h3>
                <p>{{ __('messages.cancel_order') }}</p>
            </div>
        </div>
        <div class="col-lg-4 col-sm-6">
            <div class="dashboard_card orange">
                <span><i class="fa-solid fa-spinner"></i></span>
                <h3>{{ $totalPendingOrders }}</h3>
                <p>{{ __('messages.pending_order') }}</p>
            </div>
        </div>
        <div class="col-lg-4 col-sm-6">
            <div class="dashboard_card green">
                <span><i class="fa-solid fa-star"></i></span>
                <h3>{{ $totalReviews }}</h3>
                <p>{{ __('messages.total_reviews') }}</p>
            </div>
        </div>
        <div class="col-lg-4 col-sm-6">
            <div class="dashboard_card pink">
                <span><i class="fa-solid fa-location-dot"></i></span>
                <h3>{{ $totalAddresses }}</h3>
                <p>{{ __('messages.total_addresses') }}</p>
            </div>
        </div>
        <div class="col-lg-4 col-sm-6">
            <div class="dashboard_card purple">
                <span><i class="fi-rs-shopping-bag"></i></span>
                <h3>{{ $totalWishlists }}</h3>
                <p>{{ __('messages.total_wishlist') }}</p>
            </div>
        </div>
    </div>
</div>

@endsection
