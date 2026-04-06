<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Content\AboutPage;
use Illuminate\View\View;

class AboutController extends Controller
{
    public function show(): View
    {
        $about = AboutPage::query()->visible()->firstOrFail();

        return view('frontend.about.show', [
            'about' => $about,
        ]);
    }
}

