<div class="scrim" id="scrim" hidden></div>
<aside class="drawer" id="drawer" aria-hidden="true" aria-label="{{ __('front.cart') }}">
  <header class="drawer__head">
    <h3><span>{{ __('front.cart') }}</span> <small data-cart-count>0</small></h3>
    <button type="button" class="x" data-close-cart aria-label="close">×</button>
  </header>
  <div class="ship" id="shipBar"></div>
  <ul class="drawer__items" id="cartItems"></ul>
  <div class="drawer__empty" id="cartEmpty">
    @if ($image = $settings->imageUrl('empty_state_image'))
      <img src="{{ $image }}" alt="">
    @endif
    <p>{{ __('front.cart_empty') }}</p>
    <a href="#shop" class="btn btn--line" data-close-cart>{{ __('front.shop_now') }}</a>
  </div>
  <footer class="drawer__foot" id="cartFoot">
    <div class="drawer__sub"><span>{{ __('front.subtotal') }}</span><b data-cart-sub></b></div>
    <p class="drawer__hint">{{ __('front.delivery_hint') }}</p>
    <a href="#checkout" class="btn btn--primary btn--lg btn--block" data-close-cart>{{ __('front.go_checkout') }}</a>
  </footer>
</aside>
