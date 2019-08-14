<?php

namespace App\Http\Controllers\Manage\CP;

use Validator;
use App\Models\PropertyCP;
use Illuminate\Http\Request;
use Webpatser\Countries\Countries;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Services\Storage\StorageService;

class PropertyController extends Controller
{
    public function propertySettings(PropertyCP $property)
    {
        $property_id = $property->id;

        $countries_keys = Countries::pluck('iso_3166_2')->toArray();
        $countries = [];
        foreach ($countries_keys as $countries_key) {
            $countries[$countries_key] = __('country.'.$countries_key);
        }
        asort($countries);

        return view('manage.cp.settings', compact('property', 'property_id', 'countries'));
    }

    public function updateProperty(PropertyCP $property, Request $request, StorageService $storageService)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:150|min:1',
            'description' => 'string',
            'country' => 'required',
            'logoImage' => 'sometimes|mimes:jpeg,png|max:1024',
            'featureImage' => 'sometimes|mimes:jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator);
        }

        if ($request->hasFile('logoImage')) {
            $upload_image_path = $this->upload_image($property, $request, 'logoImage', 'logo_uploaded_failed', $storageService);
            if ($upload_image_path) {
                $property->logo_path = $upload_image_path;
            } else {
                return back()->withInput();
            }
        }

        if ($request->hasFile('featureImage')) {
            $upload_image_path = $this->upload_image($property, $request, 'featureImage', 'feature_uploaded_failed', $storageService);
            if ($upload_image_path) {
                $property->feature_path = $upload_image_path;
            } else {
                return back()->withInput();
            }
        }

        $property->name = $request->name;
        $property->description = $request->description;
        $property->country = $request->country;
        $property->save();

        if (Gate::allows('super-admin')) {
            $propertyContent = $property->propertyContent;
            $propertyContent->save();
        }

        session()->flash('success', __('manage/cp/settings/property_settings.settings_update_successfully'));

        return back();
    }

    private function upload_image(PropertyCP $property, Request $request, $image_name, $error_message_label, StorageService $storageService)
    {
        $image_file = $storageService->store($request->file($image_name), $property->organization_id.'/'.$property->id);
        if (200 == $image_file->getData()->statusCode) {
            return $image_file->getData()->pathImg;
        } else {
            session()->flash('error', __('manage/cp/settings/property_settings.'.$error_message_label));

            return false;
        }
    }
}
