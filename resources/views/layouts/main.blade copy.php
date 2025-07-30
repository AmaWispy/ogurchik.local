<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name'))</title>
    <meta name="description" content="@yield('meta_description', config('app.name'))">
    <link href="/template/css/style.css" type="text/css" rel="stylesheet">
    @stack('styles')
</head>
<body>
    <img class="bg-green" src="/template/img/bg-green.svg" alt="Green background">
    <img class="bg-woman" src="/template/img/custom/bg-woman-custom.png" alt="Woman background">
    <div class="out-header">
        <header>
            <span class="hamburger">
                <img src="/template/img/icon-hamburger.svg" alt="Icon hamburger">
            </span>
            <a class="logo" href="{{ route('home') }}">
                <img src="/template/img/logo.svg" alt="Garden logo">
            </a>
            <ul>
                @foreach($pages as $page)
                    <li>
                        <a href="{{ route('pages.show', $page->slug) }}" 
                           @if(request()->is('pages/' . $page->slug)) class="active" @endif>
                            {{ $page->name }}
                        </a>
                    </li>
                @endforeach
            </ul>
            <a class="btn-primary" href="#quote">REQUEST A QUOTE</a>
        </header>
    </div>
    <ul class="mobile">
        @foreach($pages as $page)
            <li>
                <a href="{{ route('pages.show', $page->slug) }}">
                    {{ $page->name }}
                </a>
            </li>
        @endforeach
    </ul>

    @yield('content')

    <footer>
        <div class="footer-start">
            <div class="case">
                <img src="/template/img/icon-phone.svg" alt="Phone icon">
                <div class="text">
                    <p>Call us</p>
                    <span>(541) 754-3010</span>
                </div>
            </div>
            <div class="case">
                <img src="/template/img/icon-mail.svg" alt="Mail icon">
                <div class="text">
                    <p>Email us</p>
                    <span>info@garden.com</span>
                </div>
            </div>
            <div class="case">
                <img src="/template/img/icon-location.svg" alt="Location icon">
                <div class="text">
                    <p>Address</p>
                    <span>79 Strawberry Lane Beverly</span>
                </div>
            </div>
        </div>
        <hr>
        <div class="footer-end">
            <img src="/template/img/logo.svg" alt="Garden logo">
            <div class="footer-link">
                <ul>
                    @foreach($pages as $page)
                        <li>
                            <a href="{{ route('pages.show', $page->slug) }}">
                                {{ $page->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
            <span>© Copyright Garden {{ date('Y') }}</span>
        </div>
    </footer>

    @stack('scripts')
    <script src="/template/js/mobile.js" type="text/javascript"></script>
</body>
</html> 