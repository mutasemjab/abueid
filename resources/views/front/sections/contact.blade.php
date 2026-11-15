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
