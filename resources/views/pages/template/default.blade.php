@extends('layouts.main')

@section('title', $page->name)
@section('meta_description', Str::limit(strip_tags($page->content), 160))

@section('content')
    <article>
        <!-- <header class="page-header">
            <h1>{{ $page->title }}</h1>
            @if($page->images)
                <div class="header-img">
                    <img src="{{ Storage::url($page->images[0]) }}" alt="{{ $page->title }}">                
                </div>
            @endif
        </header> -->

        <section class="section-villa">
            {!! $content !!}
        </section>

        <footer class="article-footer section-villa">
            <div class="creation-date">
                Дата публикации: {{ $page->created_at->format('d.m.Y') }}
            </div>
        </footer>
    </article>
@endsection 