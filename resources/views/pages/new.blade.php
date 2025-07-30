@extends('layouts.main')

@section('title', 'Новые статьи - ' . config('app.name'))
@section('meta_description', 'Последние добавленные статьи на сайте ' . config('app.name'))

@section('content')
    <section class="main-title">
        <h1>Новые статьи</h1>
        <p>Ознакомьтесь с нашими последними публикациями</p>
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