<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Models\PropertySP;
use App\Models\TermsOfDistribution;

class SpHeaderComposer
{
    /**
     * Bind data to the view.
     *
     * @param View $view
     */
    public function compose(View $view)
    {
        // split URL to get property ID
        $url = url()->current();
        $path = parse_url($url, PHP_URL_PATH);
        $segments = explode('/', rtrim($path, '/'));

        $property_id = $segments[2];

        $property = PropertySP::findOrFail($property_id);

        $tod_sp_pending = TermsOfDistribution::where('sp_property_id', $property_id)->where('status', 'pending_sp')->count();

        $view->with([
            'property' => $property,
            'tod_sp_pending' => $tod_sp_pending,
        ]);
    }
}
