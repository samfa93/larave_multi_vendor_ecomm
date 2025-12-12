@extends('frontend.dashboard.dashboard-app')

@section('dashboard_contents')

<div class="tab-pane fade active show" id="orders" role="tabpanel" aria-labelledby="orders-tab">
    <div class="card">
        <div class="card-header p-0">
            <h3 class="mb-0">{{ __('messages.dashboard_your_orders') }}</h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="order_table table m-0 mt-20">
                    <thead>
                        <tr>
                            <th>{{ __('messages.order') }}</th>
                            <th>{{ __('messages.store') }}</th>
                            <th>{{ __('messages.date') }}</th>
                            <th>{{ __('messages.payment_status') }}</th>
                            <th>{{ __('messages.order_status') }}</th>
                            <th>{{ __('messages.total') }}</th>
                            <th>{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                        <tr>
                            <td>#{{ $order->id }}</td>
                            <td>{{ $order->store->name }}</td>
                            <td>{{ date('Y-m-d', strtotime($order->created_at)) }}</td>
                            <td>
                                @if($order->payment_status == 'paid')
                                    <span class="badge bg-success">{{ __('messages.status_paid') }}</span>
                                @elseif($order->payment_status == 'pending')
                                    <span class="badge bg-warning">{{ __('messages.status_pending') }}</span>
                                @else
                                    <span class="badge bg-danger">{{ __('messages.status_failed') }}</span>
                                @endif
                            </td>
                            <td>
                                {{ $order->order_status }}
                            </td>
                            <td> {{ $order->currency }} {{ $order->total }}</td>
                                <td><a href="{{ route('orders.show', $order) }}" class="btn-small d-block">{{ __('messages.view') }}</a></td>
                        </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
