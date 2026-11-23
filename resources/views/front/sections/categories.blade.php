@php($limit = max(1, (int) ($section->value('cards_count') ?: 8)))

<section class="sec" id="{{ $section->anchor() }}">
  <div class="wrap">
    @include('front.partials.section-head', ['title' => $section->value('title'), 'link' => $section->value('link_label')])
    <div class="cats" id="catGrid" data-limit="{{ $limit }}"></div>
    @if ($storefront->categories()->count() > $limit)
      <div class="cats__more reveal">
        <a href="#shop" class="btn btn--line btn--lg">{{ $section->value('more_label') ?: __('front.load_more') }}</a>
      </div>
    @endif
  </div>
</section>
