@extends('admin.payment-setting.index')

@section('settings_contents')
    <div class="card-body">
        <h2 class="mb-4">Cash on Delivery</h2>

        <form action="{{ route('admin.cod-settings.store') }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="form-label">Cash on Delivery Status</div>
                    <select name="cod_status" class="form-control">
                        <option @selected(config('settings.cod_status') == 'active') value="active">Active</option>
                        <option @selected(config('settings.cod_status') == 'inactive') value="inactive">Inactive</option>
                    </select>
                    <x-input-error :messages="$errors->get('cod_status')" class="mt-2" />
                </div>
            </div>

            <div class="btn-list justify-content-end mt-5">
                <button type="submit" class="btn btn-primary btn-2">Submit</button>
            </div>
        </form>
    </div>
@endsection
