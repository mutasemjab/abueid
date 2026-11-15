<nav class="mbar" aria-label="mobile">
  <a href="#home">{{ __('front.home') }}</a>
  <a href="#shop">{{ __('front.nav_shop') }}</a>
  @if ($branches = $storefront->menuSections()->get('branches'))
    <a href="#home" data-scroll="{{ $branches->anchor() }}">{{ $branches->value('menu_label') }}</a>
  @endif
  <button type="button" data-open-cart><span>{{ __('front.cart') }}</span> <b data-cart-count>0</b></button>
</nav>
