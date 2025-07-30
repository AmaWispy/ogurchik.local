<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageController extends Controller
{
    /**
     * Display the specified page.
     */
    public function show(Page $page): View
    {
        if (!$page->is_active) {
            abort(404);
        }

        // Обработка контента для удаления подписей и ссылок с изображений
        $content = preg_replace('/<figcaption.*?<\/figcaption>/is', '', $page->content);
        $content = preg_replace('/<figure(.*?)>(.*?)<\/figure>/is', '$2', $content);
        $content = preg_replace('/<a.*?href="[^"]*".*?>(.*?)<\/a>/is', '$1', $content);

        return view('pages.template.' . ($page->template ? $page->template : 'default'), [
            'page' => $page,
            'content' => $content
        ]);
    }

    public function new(): View
    {
        $pages = Page::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        return view('pages.new', compact('pages'));
    }

    public function random(): View
    {
        $pages = Page::where('is_active', true)
            ->inRandomOrder()
            ->limit(8)
            ->get();

        return view('pages.random', compact('pages'));
    }

    public function search(Request $request): View
    {
        $query = $request->input('q');
        
        $pages = Page::where('is_active', true)
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('content', 'like', "%{$query}%");
            })
            ->when($query, function($q) {
                return $q->orderBy('created_at', 'desc');
            })
            ->paginate(12);

        return view('pages.search', compact('pages'));
    }

    /**
     * Find pages with non-standard characters
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function findNonStandardCharacters()
    {
        $pages = Page::where('is_active', true)->get();
        $results = [];

        // Паттерн для поиска символов, не входящих в стандартный набор
        // Добавлены: і (U+0456), | (pipe), ° (градус), ² (superscript two), ³ (superscript three), ½ (one half), ¾ (three quarters), ÷ (division sign), ≈ (almost equal to)
        $pattern = '/[^а-яА-ЯёЁa-zA-Z0-9\s.,!?:;()\[\]"«»\'\/\\*+=<>@#$%^&_~`{}•—–²|\-°³½¾🌱×₂₄₃÷≈]/u';

        foreach ($pages as $page) {
            // Проверяем только контент
            $contentHasNonStandard = preg_match($pattern, $page->content);

            if ($contentHasNonStandard) {
                // Находим все нестандартные символы в контенте
                preg_match_all($pattern, $page->content, $contentMatches);
                $nonStandardChars = array_unique($contentMatches[0]);

                // Для отладки: показываем первые 100 символов контента
                $contentPreview = mb_substr($page->content, 0, 100);

                $results[] = [
                    'id' => $page->id,
                    'name' => $page->name,
                    'content_preview' => $contentPreview,
                    'non_standard_chars' => array_map(function($char) {
                        return [
                            'char' => $char,
                            'ord' => ord($char),
                            'hex' => bin2hex($char)
                        ];
                    }, $nonStandardChars)
                ];
            }
        }

        return response()->json([
            'pages' => $results,
            'total' => count($results)
        ]);
    }
} 