<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\App;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth')->except('setLocale');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //return redirect()->intended('');
        return view('home');
    }

    /**
     * IVX setLocale.
     *
     * @param $locale
     */
    public function setLocale($locale)
    {
        if (array_key_exists($locale, config('language_code.codes'))) {
            session()->put('locale', $locale);
            App::setLocale(session()->get('locale'));
        }

        return back();
    }
}
