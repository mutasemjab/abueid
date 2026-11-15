<section class="sec" id="{{ $section->anchor() }}">
  <div class="wrap">
    @include('front.partials.section-head', ['title' => $section->value('title'), 'aside' => $section->value('aside')])
    <div class="branches" id="branchGrid"></div>
  </div>
</section>
