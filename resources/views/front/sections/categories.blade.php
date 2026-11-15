<section class="sec" id="{{ $section->anchor() }}">
  <div class="wrap">
    @include('front.partials.section-head', ['title' => $section->value('title'), 'link' => $section->value('link_label')])
    <div class="cats" id="catGrid"></div>
  </div>
</section>
