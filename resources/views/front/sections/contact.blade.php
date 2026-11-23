<section class="bulk" id="{{ $section->anchor() }}">
  <div class="wrap bulk__in">
    @if ($image = $section->imageUrl('image'))
      <div class="bulk__img reveal"><img src="{{ $image }}" alt="" loading="lazy"></div>
    @endif
    <div class="bulk__copy reveal">
      @if ($number)
        <span class="sec__num sec__num--light">{{ $number }}</span>
      @endif
      <h2>{{ $section->value('title') }}</h2>
      @if ($lead = $section->value('lead'))
        <p>{{ $lead }}</p>
      @endif

      @php
        $phone = $settings->get('phone');
        $email = $settings->get('email');
      @endphp
      @if ($phone || $email)
        <ul class="bulk__contact">
          @if ($phone)
            <li>
              <a href="{{ tel_href($phone) }}">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6.6 10.8a15.1 15.1 0 0 0 6.6 6.6l2.2-2.2a1 1 0 0 1 1-.25 11.4 11.4 0 0 0 3.6.57 1 1 0 0 1 1 1V20a1 1 0 0 1-1 1A17 17 0 0 1 3 4a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1c0 1.25.2 2.45.57 3.57a1 1 0 0 1-.25 1z"/></svg>
                <span><small>{{ __('front.contact_phone') }}</small><b dir="ltr">{{ $phone }}</b></span>
              </a>
            </li>
          @endif
          @if ($email)
            <li>
              <a href="mailto:{{ $email }}">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 5h18a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1zm9 7.2L4 7.1V17h16V7.1zm0-2.3L19.2 7H4.8z"/></svg>
                <span><small>{{ __('front.contact_email') }}</small><b dir="ltr">{{ $email }}</b></span>
              </a>
            </li>
          @endif
        </ul>
      @endif

      <form class="bulk__form" id="bulkForm" novalidate>
        <label><span>{{ __('front.f_name') }}</span><input name="name" required></label>
        <label><span>{{ __('front.f_phone') }}</span><input name="phone" inputmode="tel" dir="ltr" placeholder="07X XXX XXXX" required></label>
        <label class="full">
          <span>{{ $section->value('list_label') }}</span>
          <textarea name="list" rows="3" placeholder="{{ $section->value('list_placeholder') }}"></textarea>
        </label>
        <button class="btn btn--primary full" type="submit"><span>{{ $section->value('submit_label') }}</span></button>
        <p class="form-msg full" id="bulkMsg" aria-live="polite"></p>
      </form>
    </div>
  </div>
</section>
