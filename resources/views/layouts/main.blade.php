<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr">

<head>
    <!-- Yandex.Metrika counter -->
    <script type="text/javascript">
        (function(m, e, t, r, i, k, a) {
            m[i] = m[i] || function() {
                (m[i].a = m[i].a || []).push(arguments)
            };
            m[i].l = 1 * new Date();
            for (var j = 0; j < document.scripts.length; j++) {
                if (document.scripts[j].src === r) {
                    return;
                }
            }
            k = e.createElement(t), a = e.getElementsByTagName(t)[0], k.async = 1, k.src = r, a.parentNode.insertBefore(k, a)
        })
        (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

        ym(102136656, "init", {
            clickmap: true,
            trackLinks: true,
            accurateTrackBounce: true
        });
    </script>
    <noscript>
        <div><img src="https://mc.yandex.ru/watch/102136656" style="position:absolute; left:-9999px;" alt="" /></div>
    </noscript>
    <!-- /Yandex.Metrika counter -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name'))</title>
    <meta name="description" content="@yield('meta_description', config('app.name'))">
    <link href="/template/css/style.css" type="text/css" rel="stylesheet">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    @stack('styles')
</head>

<body>
    <img class="bg-green" src="/template/img/bg-green.svg" alt="Green background">
    @if(request()->routeIs('home'))
    <img class="bg-woman" src="/template/img/custom/bg-woman-custom.png" alt="Woman background">
    @endif
    <div class="out-header">
        <header>
            <span class="hamburger">
                <img src="/template/img/icon-hamburger.svg" alt="Icon hamburger">
            </span>
            <a class="logo" href="{{ route('home') }}">
                <img style="width: 125px;" src="/template/img/custom/logo.png" alt="Garden logo">
            </a>
            <ul>
                <li>
                    <a href="{{ route('home') }}" @if(request()->routeIs('home')) class="active" @endif>
                        ГЛАВНАЯ
                    </a>
                </li>
                <li>
                    <a href="{{ route('new') }}" @if(request()->routeIs('new')) class="active" @endif>
                        НОВЫЕ СТАТЬИ
                    </a>
                </li>
                <li>
                    <a href="{{ route('random') }}" @if(request()->routeIs('random')) class="active" @endif>
                        СЛУЧАЙНЫЕ СТАТЬИ
                    </a>
                </li>
                <li>
                    <a href="{{ route('search') }}" @if(request()->routeIs('search')) class="active" @endif>
                        ПОИСК
                    </a>
                </li>
            </ul>
            <!-- <a class="btn-primary" href="#quote">REQUEST A QUOTE</a> -->
        </header>
    </div>
    <ul class="mobile">
        <li>
            <a href="{{ route('home') }}">ГЛАВНАЯ</a>
        </li>
        <li>
            <a href="{{ route('new') }}">НОВЫЕ СТАТЬИ</a>
        </li>
        <li>
            <a href="{{ route('random') }}">СЛУЧАЙНЫЕ СТАТЬИ</a>
        </li>
        <li>
            <a href="{{ route('search') }}">ПОИСК</a>
        </li>
    </ul>

    @yield('content')

    <footer>
        <hr>
        <div class="footer-end">
            <img style="width: 125px;" src="/template/img/custom/logo.png" alt="Garden logo">
            <div class="footer-link">
                <ul>
                    <li><a href="{{ route('home') }}">ГЛАВНАЯ</a></li>
                    <li><a href="{{ route('new') }}">НОВЫЕ СТАТЬИ</a></li>
                    <li><a href="{{ route('random') }}">СЛУЧАЙНЫЕ СТАТЬИ</a></li>
                    <li><a href="{{ route('search') }}">ПОИСК</a></li>
                </ul>
            </div>
            <span>© {{ str_replace(['http://', 'https://'], '', config('app.url')) }} {{ date('Y') }}</span>
        </div>
    </footer>

    @stack('scripts')
    <script src="/template/js/mobile.js" type="text/javascript"></script>
</body>

</html>