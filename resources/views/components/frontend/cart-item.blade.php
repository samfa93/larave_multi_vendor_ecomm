 @foreach($cartItems as $cartItem)
  <tr class="pt-30">
      <td class="custome-checkbox pl-30">
          <input class="form-check-input" type="checkbox" name="checkbox" id="exampleCheckbox1" value="">
          <label class="form-check-label" for="exampleCheckbox1"></label>
      </td>
      <td class="image product-thumbnail pt-40"><img src="{{ asset($cartItem->product?->primaryImage?->path) }}"
              alt="#"></td>
      <td class="product-des product-name">
          <h6 class="mb-5"><a class="product-name mb-10 text-heading"
                  href="shop-product-right.html">{{ $cartItem->product?->name }}</a></h6>
          <div class="product-rate-cover">
              <span>{{ $cartItem->product?->variants()->where('id', $cartItem->variant_id)->first()->name ?? '' }}</span>
          </div>
          <div class="product-rate-cover">
              <div class="product-rate d-inline-block">
                  <div class="product-rating" style="width:90%">
                  </div>
              </div>
              <span class="font-small {{ app()->getLocale() == 'ar' ? 'mr-5' : 'ml-5' }} text-muted"> (4.0)</span>
          </div>
      </td>
      @php
          $price = $cartItem->product->getVariantOrProductPriceAndStock($cartItem->variant_id);
      @endphp

      @if($price['in_stock'])
      <td class="price" data-title="Price">

          @if ($price['old_price'])
              <h4 class="text-body">$ {{ $price['price'] }}</h4>
              <h4 class="text-danger" style="font-size: 18px;text-decoration: line-through;">$
                  {{ $price['old_price'] }}</h4>
          @else
              <h4 class="text-body">$ {{ $price['price'] }}</h4>
          @endif

      </td>
      <td class="text-center detail-info" data-title="Stock">
          <div class="detail-extralink mr-15">
              <div class="detail-qty border radius">
                  <a href="#" class="qty-down"><i class="fi-rs-angle-small-down"></i></a>
                  <input type="text" data-cart-item="{{ $cartItem->id }}" name="quantity" class="qty-val"
                      value="{{ $cartItem->quantity }}" min="1" readonly>
                  <a href="#" class="qty-up"><i class="fi-rs-angle-small-up"></i></a>
              </div>
          </div>
      </td>
      <td class="price" data-title="Price">
          <h4 class="text-brand">$ {{ $price['price'] * $cartItem->quantity }} </h4>
      </td>
      @else
      <td colspan="3"><h4 class="text-brand">{{ __('messages.out_of_stock') }}</h4></td>
      @endif
      <td class="action text-center" data-title="{{ __('messages.remove') }}"><a href="#" class="text-body"><i
                  class="fi-rs-trash"></i></a></td>
  </tr>
@endforeach


