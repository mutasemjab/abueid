<header class="hdr" id="hdr">
  <div class="wrap hdr__in">
    <a href="#home" class="brand" aria-label="{{ $settings->get('site_name') }}">
      @if ($logo = $settings->imageUrl('logo'))
        <img src="{{ $logo }}" alt="{{ $settings->get('site_name') }}" width="140" height="46">
      @endif
      <span class="brand__sub">{{ $settings->get('tagline') }}</span>
    </a>

    <nav class="nav" aria-label="main">
      <a href="#shop">{{ __('front.nav_shop') }}</a>
      @foreach ($storefront->menuSections() as $section)
        <a href="#home" data-scroll="{{ $section->anchor() }}">{{ $section->value('menu_label') }}</a>
      @endforeach
    </nav>

    <div class="search" id="search">
      <svg viewBox="0 0 20 20" aria-hidden="true"><circle cx="8.5" cy="8.5" r="5.5"/><path pathLength="1" d="M13 13l4 4"/></svg>
      <input type="search" id="q" autocomplete="off" placeholder="{{ __('front.search_ph') }}">
      <div class="search__drop" id="searchDrop" role="listbox"></div>
    </div>

    <div class="hdr__actions">
      <a class="hbtn" href="#account" id="accountBtn" aria-label="{{ __('front.account') }}" title="{{ __('front.account') }}">
        <svg viewBox="0 0 20 20" aria-hidden="true"><circle cx="10" cy="6.5" r="3.5"/><path d="M3.5 17.5c.8-3.3 3.4-5 6.5-5s5.7 1.7 6.5 5"/></svg>
      </a>
      <button class="hbtn hbtn--fav" type="button" data-go-fav aria-label="{{ __('front.wishlist') }}">
        <svg viewBox="0 0 20 20" aria-hidden="true"><path pathLength="1" d="M10 17s-6.5-3.9-6.5-8.6A3.6 3.6 0 0 1 10 6.3a3.6 3.6 0 0 1 6.5 2.1C16.5 13.1 10 17 10 17z"/></svg>
        <b class="badge" data-fav-count>0</b>
      </button>
      <button class="cartbtn" type="button" data-open-cart id="cartBtn">
        <span>{{ __('front.cart') }}</span>
        <b class="cartbtn__n" data-cart-count>0</b>
      </button>
    </div>
  </div>
</header>
