<section class="sec">
  <div class="wrap">
    @include('front.partials.section-head', ['title' => $section->value('title')])
    <ol class="steps">
      @foreach ($section->activeItems as $step)
        <li class="reveal">
          <b>{{ $loop->iteration }}</b>
          <h3>{{ $step->value('title') }}</h3>
          <p>{{ $step->value('description') }}</p>
        </li>
      @endforeach
    </ol>
  </div>
</section>
