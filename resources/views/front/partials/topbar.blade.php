@php($topbar = $storefront->section('topbar'))

<div class="topbar">
  <div class="wrap topbar__in">
    <p class="topbar__ticker">
      @foreach ($topbar?->activeItems ?? [] as $message)
        @unless ($loop->first)<i></i>@endunless<span>{{ $message->value('text') }}</span>
      @endforeach
    </p>
    <div class="topbar__end">
      @if ($phone = $settings->get('phone'))
        <span class="topbar__phone" dir="ltr">{{ $phone }}</span>
      @endif
      @foreach (locales() as $locale)
        @continue($locale === app()->getLocale())
        <a class="lang" href="{{ LaravelLocalization::getLocalizedURL($locale, null, [], true) }}" hreflang="{{ $locale }}" data-lang-link>{{ __('front.switch_lang') }}</a>
      @endforeach
    </div>
  </div>
</div>
