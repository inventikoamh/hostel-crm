<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LandingController extends Controller
{
    /**
     * Display the landing page
     */
    public function index()
    {
        return view('landing');
    }

    /**
     * Display the second landing page with new color scheme
     */
}
