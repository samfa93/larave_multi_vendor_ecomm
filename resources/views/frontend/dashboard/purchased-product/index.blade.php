@extends('frontend.dashboard.dashboard-app')

@section('dashboard_contents')

<div class="tab-pane fade active show" id="orders" role="tabpanel" aria-labelledby="orders-tab">
    <div class="card">
        <div class="card-header p-0">
            <h3 class="mb-0">{{ __('messages.your_purchased_products') }}</h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="order_table table m-0 mt-20">
                    <thead>
                        <tr>
                            <th>{{ __('messages.order') }}</th>
                            <th>{{ __('messages.product') }}</th>
                            <th>{{ __('messages.date') }}</th>
                            <th>{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($digitalProducts as $product)
                        <tr>
                            <td>#{{ $loop->iteration }}</td>
                            <td>{{ $product->product_name }}</td>
                            <td>{{ date('Y-m-d', strtotime($product->created_at)) }}</td>
                            <td><a href="{{ route('purchased.products.show', $product->id) }}">{{ __('messages.view') }}</a></td>

                        </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
