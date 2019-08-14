<?php

namespace App\Http\ViewComposers;

use Auth;
use Route;
use App\Models\User;
use App\Models\Property;
use Illuminate\View\View;
use App\Models\Organization;

class NavbarComposer
{
    /**
     * Bind data to the view.
     *
     * @param View $view
     */
    public function compose(View $view)
    {
        $orgName = Organization::Organization()->name;
        $routeParams = Route::current()->parameters();
        $property = $routeParams['property'] ?? null;
        $notAcceptedNumber = null;

        if (is_null($property)) {
            $properties = $types = [];
        } else {
            $properties = Organization::Organization()->properties;
            $user = Auth::user();
            foreach ($properties as $property_key => $property_item) {
                if (!$user->can('manage-property', $property_item)) {
                    unset($properties[$property_key]);
                }
            }

            if (!is_object($property)) {
                $property = Property::findOrFail($property);
            }
            $displayType = str_replace('_', '-', $property->type);
            $color = 'cp';
            if (Property::TYPE_SP == $property->type) {
                $color = 'sp-wp';
                $displayType = 'sp';
            } elseif (Property::TYPE_CP == $property->type) {
                $displayType = 'cp';
            } else {
                $displayType = 'sp-plus';
            }
            $property->display_type = $displayType;
            $property->color = $color;
            $types = [
                'sp' => 'SP',
                'cp' => 'CP',
                'sp-plus' => 'SP_PLUS',
            ];

            if (!array_key_exists($displayType, $types)) {
                $types[$displayType] = $displayType;
            }
        }

        $organizations = [];
        if (Auth::user()->isSuperAdmin() || Auth::user()->isOperator()) {
            $organizations = Organization::all()->except([Organization::ID_FOR_SUPER_ADMIN, Organization::Organization()->id]);
        } else {
            $organizations = User::find(Auth::id())->organizations()->get()->except([Organization::ID_FOR_SUPER_ADMIN, Organization::Organization()->id]);
        }
        $navbarDetails = [
            'org_name' => $orgName,
            'properties' => $properties,
            'types' => $types,
            'property' => $property,
            'organizations' => $organizations,
        ];

        $view->with('navbarDetails', $navbarDetails);
    }
}
