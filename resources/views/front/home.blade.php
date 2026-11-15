@extends('layouts.front')

@section('content')
  <section class="view" data-view="home">
    @php($number = 0)
    @foreach ($sections as $section)
      @include("front.sections.{$section->key}", [
          'section' => $section,
          'number'  => $section->isNumbered() ? sprintf('%02d', ++$number) : null,
      ])
    @endforeach
  </section>

  @include('front.views.shop')
  @include('front.views.product')
  @include('front.views.checkout')
  @include('front.views.done')
  @include('front.views.account')
@endsection
