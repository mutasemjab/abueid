<section class="brands" id="{{ $section->anchor() }}">
  <div class="wrap">
    <header class="brands__head reveal">
      @if ($number)
        <span class="sec__num">{{ $number }}</span>
      @endif
      <div>
        <h2>{{ $section->value('title') }}</h2>
        @if ($lead = $section->value('lead'))
          <p>{{ $lead }}</p>
        @endif
      </div>
    </header>
    <div class="brandgrid" id="brandGrid"></div>
  </div>
  <div class="logoband" aria-hidden="true"><div class="logoband__track" id="logoBand"></div></div>
</section>
