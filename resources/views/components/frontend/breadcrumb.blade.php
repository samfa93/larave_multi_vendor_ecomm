<div class="page-header breadcrumb-wrap d-print-none">
    <div class="container">
        <div class="breadcrumb">
            @foreach ($items as $item)
                @if(!$loop->last)
                <a href="{{ $item['url'] }}" rel="nofollow"><i class="fi-rs-home {{ app()->getLocale() == 'ar' ? 'ml-5' : 'mr-5' }}"></i>{{ $item['label'] }}</a>
                @else
                <span></span> {{ $item['label'] }}
                @endif
            @endforeach
        </div>
    </div>
</div>
