@php
  $footer = $storefront->section('footer');
  $whatsapp = $settings->get('whatsapp');
  $phone = $settings->get('phone');
  $socials = array_filter([
      'Facebook'  => $settings->get('facebook_url'),
      'Instagram' => $settings->get('instagram_url'),
  ]);
@endphp

<footer class="ftr">
  <div class="wrap ftr__in">
    <div class="ftr__brand">
      @if ($logo = $settings->imageUrl('logo_light'))
        <img src="{{ $logo }}" alt="{{ $settings->get('site_name') }}" width="150" height="50" loading="lazy">
      @endif
      <p>{{ $footer?->value('about') }}</p>
    </div>

    <div>
      <h4>{{ __('front.ftr_shop') }}</h4>
      <ul>
        @foreach ($storefront->categories() as $category)
          <li><a href="#cat-{{ $category->slug }}">{{ $category->name }}</a></li>
        @endforeach
      </ul>
    </div>

    <div>
      <h4>{{ __('front.ftr_help') }}</h4>
      <ul>
        @foreach ($storefront->menuSections() as $section)
          <li><a href="#home" data-scroll="{{ $section->anchor() }}">{{ $section->value('menu_label') }}</a></li>
        @endforeach
        @if ($note = $footer?->value('delivery_note'))
          <li><span>{{ $note }}</span></li>
        @endif
      </ul>
    </div>

    <div>
      <h4>{{ __('front.ftr_contact') }}</h4>
      <ul>
        <li>
          @if ($whatsapp)
            <a class="wa" href="https://wa.me/{{ $whatsapp }}" target="_blank" rel="noopener">WhatsApp</a>
          @endif
          @if ($whatsapp && $phone) · @endif
          @if ($phone)
            <span dir="ltr">{{ $phone }}</span>
          @endif
        </li>
        @if ($socials)
          <li>
            @foreach ($socials as $network => $url)
              @unless ($loop->first) · @endunless<a href="{{ $url }}" target="_blank" rel="noopener">{{ $network }}</a>
            @endforeach
          </li>
        @endif
      </ul>
    </div>
  </div>

  <div class="wrap ftr__bottom">
    <span>© {{ now()->year }} {{ $footer?->value('copyright') }}</span>
    <span>{{ $footer?->value('trademark_note') }}</span>
  </div>
</footer>
