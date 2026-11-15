<section class="sec">
  <div class="wrap">
    @include('front.partials.section-head', ['title' => $section->value('title'), 'link' => $section->value('link_label')])
    <div class="grid" id="featGrid" data-limit="{{ (int) $section->value('products_count') }}"></div>
  </div>
</section>
