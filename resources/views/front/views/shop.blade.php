<section class="view" data-view="shop" hidden>
  <div class="wrap">
    <div class="crumbs"><a href="#home">{{ __('front.home') }}</a><span>/</span><b>{{ __('front.nav_shop') }}</b></div>
    <div class="shop__head">
      <h1 id="shopTitle">{{ __('front.all_products') }}</h1>
      <p class="shop__count" id="shopCount"></p>
    </div>
    <div class="shop">
      <aside class="filters" id="filters">
        <div class="filters__top"><b>{{ __('front.filter') }}</b><button type="button" class="x" data-close-filters aria-label="close">×</button></div>
        <div class="fgroup">
          <h4>{{ __('front.category') }}</h4>
          <div id="fCats"></div>
        </div>
        <div class="fgroup">
          <h4>{{ __('front.brand') }}</h4>
          <div id="fBrands"></div>
        </div>
        <div class="fgroup">
          <h4>{{ __('front.price') }}</h4>
          <div class="price-in">
            <label><span>{{ __('front.up_to') }}</span> <output id="fPriceOut"></output></label>
            <input type="range" id="fPrice" min="0" max="0" step="0.5">
          </div>
        </div>
        <div class="fgroup">
          <label class="check"><input type="checkbox" id="fStock"><span>{{ __('front.in_stock_only') }}</span></label>
          <label class="check"><input type="checkbox" id="fFav"><span>{{ __('front.fav_only') }}</span></label>
        </div>
        <button type="button" class="btn btn--line" id="fReset">{{ __('front.reset') }}</button>
      </aside>

      <div class="shop__main">
        <div class="toolbar">
          <button type="button" class="btn btn--line btn--sm only-m" data-open-filters>{{ __('front.filter') }}</button>
          <div class="toolbar__q"><input type="search" id="shopQ" placeholder="{{ __('front.search_short') }}"></div>
          <label class="sort"><span>{{ __('front.sort_by') }}</span>
            <select id="sort">
              <option value="feat">{{ __('front.sort_feat') }}</option>
              <option value="low">{{ __('front.sort_low') }}</option>
              <option value="high">{{ __('front.sort_high') }}</option>
            </select>
          </label>
        </div>
        <div class="chips" id="activeChips"></div>
        <div class="grid grid--shop" id="shopGrid"></div>
        <div class="empty" id="shopEmpty" hidden>
          @if ($image = $settings->imageUrl('empty_state_image'))
            <img src="{{ $image }}" alt="">
          @endif
          <h3>{{ __('front.empty_t') }}</h3>
          <p>{{ __('front.empty_p') }}</p>
          <div class="empty__btns">
            <a class="btn btn--primary" id="emptyWa" target="_blank" rel="noopener" hidden>{{ __('front.empty_wa_btn') }}</a>
            <button class="btn btn--line" type="button" id="emptyReset">{{ __('front.reset') }}</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
