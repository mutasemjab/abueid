<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ is_rtl() ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $settings->get('site_title'))</title>
    <meta name="description" content="{{ $settings->get('meta_description') }}">
    @foreach (locales() as $locale)
        <link rel="alternate" hreflang="{{ $locale }}" href="{{ LaravelLocalization::getLocalizedURL($locale, null, [], true) }}">
    @endforeach
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@500;600;700&family=IBM+Plex+Mono:wght@400;500&family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link href="{{ asset('assets_front/css/style.css') }}?v={{ filemtime(base_path('assets_front/css/style.css')) }}" rel="stylesheet">
    @stack('styles')
</head>

<body>

    @include('front.partials.topbar')
    @include('front.partials.header')

    <main id="app">
        @yield('content')
    </main>

    @include('front.partials.footer')
    @include('front.partials.cart-drawer')
    @include('front.partials.mobile-bar')

    <div class="toasts" id="toasts" aria-live="polite"></div>

    <script>window.STOREFRONT = @json($payload, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);</script>
    <script src="{{ asset('assets_front/js/app.js') }}?v={{ filemtime(base_path('assets_front/js/app.js')) }}"></script>
    @stack('scripts')
</body>

</html>
