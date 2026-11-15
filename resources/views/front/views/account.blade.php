<section class="view" data-view="account" hidden>
  <div class="wrap">
    <div class="crumbs"><a href="#home">{{ __('front.home') }}</a><span>/</span><b>{{ __('front.account') }}</b></div>

    {{-- Guest: sign in / sign up --}}
    <div class="acc acc--guest" id="accGuest">
      <form class="acc__card" id="loginForm" novalidate>
        <h1>{{ __('front.acc_login_t') }}</h1>
        <p class="co__sub">{{ __('front.acc_login_p') }}</p>
        <div class="fields">
          <label class="full"><span>{{ __('front.f_phone') }}</span><input name="phone" inputmode="tel" autocomplete="tel" dir="ltr" placeholder="07X XXX XXXX" required></label>
          <label class="full"><span>{{ __('front.f_password') }}</span><input type="password" name="password" autocomplete="current-password" required></label>
        </div>
        <label class="check"><input type="checkbox" name="remember" value="1" checked><span>{{ __('front.acc_remember') }}</span></label>
        <button class="btn btn--primary btn--lg btn--block" type="submit">{{ __('front.acc_login_btn') }}</button>
        <p class="form-msg" data-form-msg aria-live="polite"></p>
        <a class="link-arrow" target="_blank" rel="noopener" data-forgot>{{ __('front.acc_forgot') }}</a>
      </form>

      <form class="acc__card" id="registerForm" novalidate>
        <h2>{{ __('front.acc_register_t') }}</h2>
        <p class="co__sub">{{ __('front.acc_register_p') }}</p>
        <div class="fields">
          <label class="full"><span>{{ __('front.f_name') }}</span><input name="name" autocomplete="name" required></label>
          <label class="full"><span>{{ __('front.f_phone') }}</span><input name="phone" inputmode="tel" autocomplete="tel" dir="ltr" placeholder="07X XXX XXXX" required></label>
          <label><span>{{ __('front.f_password') }}</span><input type="password" name="password" autocomplete="new-password" required></label>
          <label><span>{{ __('front.f_password_confirm') }}</span><input type="password" name="password_confirmation" autocomplete="new-password" required></label>
        </div>
        <button class="btn btn--dark btn--lg btn--block" type="submit">{{ __('front.acc_register_btn') }}</button>
        <p class="form-msg" data-form-msg aria-live="polite"></p>
      </form>
    </div>

    {{-- Signed in --}}
    <div class="acc" id="accUser" hidden>
      <header class="acc__head">
        <div>
          <h1 id="accHello"></h1>
          <p id="accPhone" dir="ltr"></p>
        </div>
        <button class="btn btn--line btn--sm" type="button" data-logout>{{ __('front.acc_logout') }}</button>
      </header>

      <nav class="acc__tabs" role="tablist">
        @foreach (['orders', 'addresses', 'favourites', 'profile'] as $tab)
          <button type="button" role="tab" data-acc-tab="{{ $tab }}">{{ __("front.acc_tab_{$tab}") }}</button>
        @endforeach
      </nav>

      <div data-acc-panel="orders">
        <div class="acc__list" id="accOrders"></div>
      </div>

      <div data-acc-panel="addresses" hidden>
        <div class="acc__cols">
          <div class="acc__list" id="accAddresses"></div>
          <form class="acc__card" id="addressForm" novalidate>
            <h2 data-address-title>{{ __('front.acc_add_address') }}</h2>
            <input type="hidden" name="id">
            <div class="areas" id="addrAreas"></div>
            <div class="fields">
              <label class="full"><span>{{ __('front.f_address') }}</span><input name="address" autocomplete="street-address" required></label>
            </div>
            <label class="check"><input type="checkbox" name="is_default" value="1"><span>{{ __('front.acc_set_default') }}</span></label>
            <div class="acc__btns">
              <button class="btn btn--primary" type="submit">{{ __('front.acc_save') }}</button>
              <button class="btn btn--line" type="reset">{{ __('front.acc_cancel') }}</button>
            </div>
            <p class="form-msg" data-form-msg aria-live="polite"></p>
          </form>
        </div>
      </div>

      <div data-acc-panel="favourites" hidden>
        <div class="grid" id="accFavourites"></div>
      </div>

      <div data-acc-panel="profile" hidden>
        <form class="acc__card acc__card--narrow" id="profileForm" novalidate>
          <div class="fields">
            <label class="full"><span>{{ __('front.f_name') }}</span><input name="name" autocomplete="name" required></label>
            <label class="full"><span>{{ __('front.f_current_password') }}</span><input type="password" name="current_password" autocomplete="current-password"></label>
            <label><span>{{ __('front.f_new_password') }}</span><input type="password" name="password" autocomplete="new-password"></label>
            <label><span>{{ __('front.f_password_confirm') }}</span><input type="password" name="password_confirmation" autocomplete="new-password"></label>
          </div>
          <p class="co__sub">{{ __('front.acc_password_hint') }}</p>
          <button class="btn btn--primary" type="submit">{{ __('front.acc_save') }}</button>
          <p class="form-msg" data-form-msg aria-live="polite"></p>
        </form>
      </div>
    </div>
  </div>
</section>
