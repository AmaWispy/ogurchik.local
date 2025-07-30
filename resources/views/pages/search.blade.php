@extends('layouts.main')

@section('title', 'Поиск - ' . config('app.name'))
@section('meta_description', 'Поиск статей на сайте ' . config('app.name'))

@section('content')
    <section class="main-title">
        <h1>Поиск статей</h1>
        <p>Найдите интересующие вас материалы</p>
    </section>

    <section class="search-section section-villa">
        <form action="{{ route('search') }}" method="GET" class="search-form">
            <div class="input-group">
                <input type="text" name="q" placeholder="Введите запрос..." value="{{ request('q') }}" class="search-input">
                <button type="submit" class="btn-primary">Поиск</button>
            </div>
        </form>
    </section>

    <section class="articles-grid section-villa">
        @forelse($pages as $page)
            <?php $route = 'pages.show'; ?>
            <article class="article-card" onclick="window.location.href='{{ route($route, $page->slug) }}'" style="cursor: pointer;">
                <div class="article-content">
                    <h2>{{ Str::limit($page->title, 65) }}</h2>
                    {{ Str::limit(strip_tags(preg_replace('/<h2>.*?<\/h2>/is', '', $page->content)), 140) }}
                    <div class="article-footer">
                        <span class="article-date">{{ $page->created_at->format('d.m.Y') }}</span>
                        <span class="read-more">Читать далее</span>
                    </div>
                </div>
            </article>
        @empty
            <p class="no-articles">Статьи пока не добавлены</p>
        @endforelse
    </section>
@endsection 