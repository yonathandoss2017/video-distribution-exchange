<?php

namespace App\Http\Controllers\Manage\SP;

use Validator;
use App\Models\User;
use App\Models\Property;
use App\Models\PropertySP;
use Illuminate\Http\Request;
use Webpatser\Countries\Countries;
use App\Models\LicenseNotification;
use App\Http\Controllers\Controller;
use App\Services\Storage\StorageService;

class PropertyController extends Controller
{
    public function propertySettings(PropertySP $property)
    {
        $property_id = $property->id;

        $countries_keys = Countries::pluck('iso_3166_2')->toArray();
        $countries = [];
        foreach ($countries_keys as $countries_key) {
            $countries[$countries_key] = __('country.'.$countries_key);
        }
        asort($countries);

        return view('manage.sp.settings.property_settings', compact('property', 'property_id', 'countries'));
    }

    public function updateProperty(PropertySP $property, Request $request, StorageService $storageService)
    {
        $rules = [
            'name' => 'required|string|max:150|min:1',
            'country' => 'required',
            'logoImage' => 'sometimes|mimes:jpeg,png|max:1024',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator);
        }

        if ($request->hasFile('logoImage')) {
            $image_file = $storageService->store($request->file('logoImage'), $property->organization_id.'/'.$property->id);
            if (200 == $image_file->getData()->statusCode) {
                $property->logo_path = $image_file->getData()->pathImg;
            } else {
                session()->flash('error', __('manage/cp/settings/property_settings.logo_uploaded_failed'));

                return back()->withInput();
            }
        }

        $property->name = $request->name;
        $property->country = $request->country;
        $property->save();

        session()->flash('success', __('manage/sp/setting/property_settings.updated_success'));

        return back();
    }

    public function notification(PropertySP $property)
    {
        $property_id = $property->id;
        $users = User::whereHas('roles', function ($q) use ($property) {
            $q->where('property_id', $property->id);
            $q->orWhereRaw('(organization_id = '.$property->organization_id.' and property_id = '.Property::ID_FOR_ADMIN.')');
        })
            ->with(['roles' => function ($q) use ($property) {
                $q->where('property_id', $property->id);
                $q->orWhereRaw('(organization_id = '.$property->organization_id.' and property_id = '.Property::ID_FOR_ADMIN.')');
            }])
            ->with('licenseNotifications')
            ->paginate(10);

        return view('manage.sp.settings.notification', compact('property', 'property_id', 'users'));
    }

    public function updateNotification(PropertySP $property, User $user, Request $request)
    {
        if (!$request->ajax()) {
            return abort(404);
        }

        $request->validate([
            'notification_status' => 'required|boolean',
        ]);

        $notification = LicenseNotification::firstOrNew([
            'property_id' => $property->id,
            'user_id' => $user->id,
        ]);
        $notification->status = $request->get('notification_status');
        $notification->save();

        return response()->json([
            'status' => 'success',
            'data' => $notification,
        ]);
    }
}
