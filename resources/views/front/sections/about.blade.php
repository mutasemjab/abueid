<section class="about" id="{{ $section->anchor() }}">
  <div class="wrap about__in">
    <div class="about__media reveal">
      @foreach (['a' => 'image_1', 'b' => 'image_2', 'c' => 'image_3'] as $slot => $field)
        @if ($image = $section->imageUrl($field))
          <figure class="about__{{ $slot }}"><img src="{{ $image }}" alt="" loading="lazy"></figure>
        @endif
      @endforeach
      <span class="about__since" id="aboutSince"></span>
    </div>
    <div class="about__copy">
      @if ($number)
        <span class="sec__num reveal">{{ $number }}</span>
      @endif
      <h2 class="reveal">{{ $section->value('title') }}</h2>
      @if ($lead = $section->value('lead'))
        <p class="about__lead reveal">{{ $lead }}</p>
      @endif
      @foreach (['paragraph_1', 'paragraph_2'] as $field)
        @if ($paragraph = $section->value($field))
          <p class="reveal">{{ $paragraph }}</p>
        @endif
      @endforeach
      <dl class="about__stats reveal" id="aboutStats"></dl>
    </div>
  </div>
</section>
