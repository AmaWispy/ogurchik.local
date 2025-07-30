@extends('layouts.main')

@section('title', $page->title)
@section('meta_description', Str::limit(strip_tags($page->content), 160))

@push('styles')
<style>
    .page-header {
        margin-bottom: 2rem;
    }
    .page-title {
        font-size: 2.5rem;
        margin-bottom: 1rem;
    }
    .page-content {
        display: grid;
        grid-template-columns: repeat({{ $page->column_number }}, 1fr);
        gap: 2rem;
        margin-bottom: 2rem;
    }
    .page-content img {
        max-width: 100%;
        height: auto;
        border-radius: 0.5rem;
    }
    .page-images {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
        margin-top: 2rem;
    }
    .page-images img {
        width: 100%;
        height: 250px;
        object-fit: cover;
        border-radius: 0.5rem;
        transition: transform 0.3s;
    }
    .page-images img:hover {
        transform: scale(1.05);
    }
    @media (max-width: 768px) {
        .page-content {
            grid-template-columns: 1fr;
        }
        .page-title {
            font-size: 2rem;
        }
    }
</style>
@endpush

@section('content')
    <article>
        <header class="page-header">
            <h1 class="page-title">{{ $page->name }}</h1>
        </header>

        <div class="page-content">
            {!! $page->content !!}
        </div>

        @if($page->images)
            <div class="page-images">
                @foreach($page->images as $image)
                    <img src="{{ Storage::url($image) }}" alt="">
                @endforeach
            </div>
        @endif
    </article>
@endsection 