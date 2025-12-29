<article class="row align-items-center hover-up">
    <figure class="col-sm-4 mb-0">
        <a href="#"><img src="{{ asset($product?->primaryImage?->path) }}" alt="" /></a>
    </figure>
    <div class="col-sm-8 mb-0">
        <h6>
            <a href="{{ route('products.show', $product->slug) }}">{{ app()->getLocale() == 'ar' ? $product->name_ar : $product->name }}</a>
        </h6>
        <div class="product-rate-cover">
            <div class="product-rate d-inline-block">
                <div class="product-rating" style="width: {{ ratingPercent($product->reviews_avg_rating) }}%"></div>
            </div>
            <span class="font-small {{ app()->getLocale() == 'ar' ? 'mr-5' : 'ml-5' }} text-muted"> ({{ round($product->reviews_avg_rating, 2) }})</span>
        </div>
        <div class="product-price">
            @php
                $price = $product->getEffectivePriceAndStock();
            @endphp

            @if ($price['in_stock'])
                @if ($price['old_price'] > 0)
                    <span>${{ $price['price'] }}</span>
                    <span class="old-price">${{ $price['old_price'] }}</span>
                @else
                    <span>${{ $price['price'] }}</span>
                @endif
            @else
                <span class="text-danger">{{ __('messages.out_of_stock') }}</span>
            @endif
        </div>
    </div>
</article>
