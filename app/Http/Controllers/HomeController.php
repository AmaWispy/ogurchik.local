<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Show the home page.
     */
    public function index(): View
    {

        $pagesNew = Page::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->get();

        $pagesRandom = Page::where('is_active', true)
            ->inRandomOrder()
            ->limit(4)  
            ->get();

        return view('home', [
            'pagesNew' => $pagesNew,
            'pagesRandom' => $pagesRandom,
        ]);
    }
} 