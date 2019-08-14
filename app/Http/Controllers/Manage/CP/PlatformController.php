<?php

namespace App\Http\Controllers\Manage\CP;

use Validator;
use App\Models\Organization;
use Illuminate\Http\Request;
use App\Models\PlatformOauth;
use App\Http\Controllers\Controller;
use App\Services\Storage\Oss\AliOSS;
use Illuminate\Support\Facades\Auth;

class PlatformController extends Controller
{
    public function index($property_id)
    {
        $platformOss = PlatformOauth::where('organization_id', Organization::Organization()->id)
            ->where('property_id', $property_id)
            ->where('platform', PlatformOauth::ALIOSS)
            ->first();

        return view('manage.cp.platform.platforms', compact('property_id', 'platformOss'));
    }

    public function addAliOss($property_id)
    {
        $platforms = PlatformOauth::where('organization_id', Organization::Organization()->id)
            ->where('property_id', $property_id)
            ->where('platform', PlatformOauth::ALIOSS)
            ->first();

        return view('manage.cp.platform.add-alioss', compact('property_id', 'platforms'));
    }

    public function storeAliOss(Request $request, $property_id)
    {
        $validator = $this->validateAliOssParams($request);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        //check oss setting params whether valid
        $res = $this->checkAliOssParamsValid($request);
        if (!$res) {
            return back();
        }

        $platformOauth = new PlatformOauth();
        $platformOauth->platform = PlatformOauth::ALIOSS;
        $platformOauth->api_key = $request->api_key;
        $platformOauth->api_secret = $request->api_secret;
        $platformOauth->display_name = 'AliOSS Oauth';
        $platformOauth->organization_id = Organization::Organization()->id;
        $platformOauth->property_id = $property_id;
        $platformOauth->user_id = Auth::id();
        $platformOauth->oss_outer_endpoint = $request->oss_outer_endpoint;
        $platformOauth->oss_internal_endpoint = $request->oss_internal_endpoint;
        $platformOauth->save();

        session()->flash('success', __('manage/oauth.oss_create_success'));

        return redirect()->route('manage.cp.oauths.show.alioss', [$property_id, $platformOauth->id]);
    }

    public function updateAliOss(Request $request, $property_id, $id)
    {
        $validator = $this->validateAliOssParams($request);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        //check oss setting params whether valid
        $res = $this->checkAliOssParamsValid($request);
        if (!$res) {
            return back();
        }

        $platformOauth = PlatformOauth::findOrFail($id);
        $platformOauth->api_key = $request->api_key;
        $platformOauth->api_secret = $request->api_secret;
        $platformOauth->oss_outer_endpoint = $request->oss_outer_endpoint;
        $platformOauth->oss_internal_endpoint = $request->oss_internal_endpoint;
        $platformOauth->save();

        session()->flash('success', __('manage/oauth.oss_update_success'));

        return redirect()->route('manage.cp.oauths.index', [$property_id]);
    }

    public function showAliOss(Request $request, $property_id, $id)
    {
        $platformOss = PlatformOauth::where('organization_id', Organization::Organization()->id)
        ->where('property_id', $property_id)
        ->where('platform', PlatformOauth::ALIOSS)
        ->first();

        return view('manage.cp.platform.show-alioss', compact('property_id', 'platformOss'));
    }

    public function deleteAliOss(Request $request, $property_id, $id)
    {
        $platformOss = PlatformOauth::find($id);
        $platformOss->delete();

        session()->flash('success', __('manage/oauth.oss_delete_success'));

        return redirect()->route('manage.cp.oauths.index', [$property_id]);
    }

    private function validateAliOssParams($request)
    {
        return Validator::make($request->all(), [
            'api_key' => 'required',
            'api_secret' => 'required',
            'oss_outer_endpoint' => 'required',
            'oss_internal_endpoint' => 'required',
        ]);
    }

    private function checkAliOssParamsValid($request)
    {
        $oss = new AliOSS($request->api_key, $request->api_secret, $request->oss_internal_endpoint);
        $res = $oss->checkConnection();
        if (false == $res) {
            session()->flash('warning', __('manage/oauth.invalid_oss_params'));

            return false;
        }

        return true;
    }
}
