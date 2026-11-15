<section class="sec sec--tint">
  <div class="wrap">
    @include('front.partials.section-head', ['title' => $section->value('title')])
    <div class="why">
      @foreach ($section->activeItems as $item)
        <article class="why__item reveal">
          @if ($image = $item->imageUrl('image'))
            <figure><img src="{{ $image }}" alt="" loading="lazy"></figure>
          @endif
          <h3>{{ $item->value('title') }}</h3>
          <p>{{ $item->value('description') }}</p>
        </article>
      @endforeach
    </div>
  </div>
</section>
