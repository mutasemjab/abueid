<?php

namespace App\Http\Controllers;

use App\Services\Storefront;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function index(Storefront $storefront): View
    {
        return view('front.home', [
            'sections' => $storefront->homeSections(),
        ]);
    }
}
