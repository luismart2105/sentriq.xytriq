<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('welcome', [
            'reviews' => Review::approved()->limit(6)->get(),
        ]);
    }
}
