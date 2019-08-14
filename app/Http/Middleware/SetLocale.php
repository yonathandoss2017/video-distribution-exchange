<?php

namespace App\Http\Middleware;

use Closure;
use Session;
use Carbon\Carbon;
use App\Models\Site;
use Illuminate\Support\Facades\App;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (isset($request->subdomain)) {
            $site = Site::with('property')->where('domain', $request->subdomain)->first();
        }
        if (isset($site->locale) && array_key_exists($site->locale, config('language_code.codes'))) {
            $this->setLocale($site->locale);
        } elseif (isset($request->locale) && array_key_exists($request->locale, config('language_code.codes'))) {
            $this->setLocale($request->locale);
        } elseif ($request->session()->has('locale') && array_key_exists($request->session()->get('locale'), config('language_code.codes'))) {
            $this->setLocale($request->session()->get('locale'));
        } else { // This is optional as Laravel will automatically set the fallback language if there is none specified
            $this->setLocale(config('app.fallback_locale'));
        }

        return $next($request);
    }

    private function setLocale($locale)
    {
        Carbon::setLocale($locale);
        App::setLocale($locale);
        Session::put('locale', $locale);
    }
}
