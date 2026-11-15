{{-- Shared heading of numbered home sections. Params: $number, $title, optional $link (label of the "all products" link) and $aside. --}}
<header class="sec__head reveal">
  @if ($number)
    <span class="sec__num">{{ $number }}</span>
  @endif
  <h2>{{ $title }}</h2>
  @if (! empty($link))
    <a href="#shop" class="link-arrow">{{ $link }}</a>
  @elseif (! empty($aside))
    <span class="sec__aside">{{ $aside }}</span>
  @endif
</header>
