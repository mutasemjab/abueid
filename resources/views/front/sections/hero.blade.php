<div class="hero">
  <div class="wrap hero__in">
    <div class="hero__copy">
      @if ($eyebrow = $section->value('eyebrow'))
        <p class="eyebrow reveal"><span class="dot"></span><span>{{ $eyebrow }}</span></p>
      @endif

      <h1 class="hero__title">
        <span class="line"><span>{{ $section->value('title_line_1') }}</span></span>
        @if ($line = $section->value('title_line_2'))
          <span class="line"><span>{{ $line }}</span></span>
        @endif
        @if ($highlight = $section->value('title_highlight'))
          <span class="line"><em>{{ $highlight }}</em></span>
        @endif
      </h1>

      @if ($lead = $section->value('lead'))
        <p class="hero__lead reveal">{{ $lead }}</p>
      @endif

      <div class="hero__cta reveal">
        <a href="#shop" class="btn btn--primary"><span>{{ $section->value('primary_button') }}</span></a>
        @if (($secondary = $section->value('secondary_button')) && $storefront->has('branches'))
          <a href="#home" data-scroll="branches" class="btn btn--ghost"><span>{{ $secondary }}</span></a>
        @endif
      </div>

      @if ($section->activeItems->isNotEmpty())
        <dl class="hero__facts reveal">
          @foreach ($section->activeItems as $fact)
            @php
              $value = (string) $fact->value('value');
              $suffix = $fact->value('suffix');
              $countUp = ctype_digit($value) && blank($suffix);
            @endphp
            <div>
              <dt @if ($countUp) data-count="{{ $value }}" @endif>{{ $countUp ? 0 : $value }}@if ($suffix)<small>{{ $suffix }}</small>@endif</dt>
              <dd>{{ $fact->value('label') }}</dd>
            </div>
          @endforeach
        </dl>
      @endif
    </div>

    <div class="hero__stage" id="heroStage">
      <div class="hero__disc"></div>
      <div class="hero__grid"></div>
      <span class="hero__model" id="heroModel"></span>
      <div class="hero__obj" id="heroObj"></div>
      <a class="hero__card" id="heroCard" href="#shop">
        <span class="hero__cat" id="heroCat"></span>
        <b id="heroName"></b>
        <span class="hero__price" id="heroPrice"></span>
      </a>
      <div class="hero__dots" id="heroDots" role="tablist"></div>
    </div>
  </div>

  <div class="ticker" aria-hidden="true">
    <div class="ticker__track" id="ticker"></div>
  </div>
</div>
