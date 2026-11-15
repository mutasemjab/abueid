<section class="view" data-view="checkout" hidden>
  <div class="wrap">
    <div class="crumbs"><a href="#home">{{ __('front.home') }}</a><span>/</span><b>{{ __('front.checkout') }}</b></div>
    <div class="co">
      <form class="co__form" id="coForm" novalidate>
        <h1>{{ __('front.checkout') }}</h1>
        <p class="co__sub">{{ __('front.co_sub') }}</p>

        <fieldset>
          <legend><b>1</b><span>{{ __('front.co_contact') }}</span></legend>
          <div class="fields">
            <label><span>{{ __('front.f_name') }}</span><input name="name" autocomplete="name" required><em class="err">{{ __('front.err_name') }}</em></label>
            <label><span>{{ __('front.f_phone') }}</span><input name="phone" inputmode="tel" autocomplete="tel" dir="ltr" placeholder="07X XXX XXXX" required><em class="err">{{ __('front.err_phone') }}</em></label>
          </div>
        </fieldset>

        <fieldset>
          <legend><b>2</b><span>{{ __('front.co_address') }}</span></legend>
          <div class="areas" id="areas"></div>
          <div class="fields">
            <label class="full"><span>{{ __('front.f_address') }}</span><input name="address" autocomplete="street-address" required><em class="err">{{ __('front.err_address') }}</em></label>
            <label class="full"><span>{{ __('front.f_notes') }}</span><input name="notes" placeholder="{{ __('front.notes_ph') }}"></label>
          </div>
        </fieldset>

        <fieldset>
          <legend><b>3</b><span>{{ __('front.co_pay') }}</span></legend>
          <div class="pay">
            @foreach (\App\Enums\PaymentMethod::cases() as $method)
              <label class="pay__opt">
                <input type="radio" name="pay" value="{{ $method->value }}" @checked($loop->first)>
                <span><b>{{ $method->label() }}</b><i>{{ __("front.pay_{$method->value}_d") }}</i></span>
              </label>
            @endforeach
          </div>
        </fieldset>

        <button class="btn btn--primary btn--lg" type="submit" id="placeBtn"><span>{{ __('front.place_order') }}</span> · <b data-co-total></b></button>
        <p class="co__legal">{{ __('front.co_legal') }}</p>
      </form>

      <aside class="co__sum">
        <h3>{{ __('front.order_summary') }}</h3>
        <ul class="sumlist" id="coItems"></ul>
        <dl class="totals">
          <div><dt>{{ __('front.subtotal') }}</dt><dd data-co-sub></dd></div>
          <div><dt>{{ __('front.delivery') }}</dt><dd data-co-del></dd></div>
          <div class="grand"><dt>{{ __('front.total') }}</dt><dd data-co-total></dd></div>
        </dl>
      </aside>
    </div>
  </div>
</section>
