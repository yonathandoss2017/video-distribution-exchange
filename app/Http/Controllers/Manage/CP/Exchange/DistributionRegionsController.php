<?php

namespace App\Http\Controllers\Manage\CP\Exchange;

use Validator;
use App\Models\Property;
use App\Models\GeoRegion;
use Illuminate\Http\Request;
use App\Services\ExchangeService;
use App\Models\TermsOfMarketplace;
use Illuminate\Support\Facades\DB;
use App\Models\TermsOfDistribution;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\DistributionRegionRight;

class DistributionRegionsController extends Controller
{
    public function create($property_id, $id)
    {
        $regions = GeoRegion::getAll(App::getLocale());

        $terms = TermsOfMarketplace::where('property_id', $property_id)->pluck('name', 'id');

        $marketplaceTerm = null;

        return view('manage.cp.exchange.distribution.regions.create', compact('property_id', 'id', 'terms', 'regions', 'marketplaceTerm'));
    }

    public function marketplaceTerm(Request $request, $property_id)
    {
        $term = intval($request->get('term'));
        $show_date = $request->get('show_date') ?? true;
        $isEdit = false;
        $marketplaceTerm = null;
        if ($term > 0) {
            $marketplaceTerm = TermsOfMarketplace::where('property_id', $property_id)->where('id', $term)->first();
        }

        $regions = GeoRegion::getAll(App::getLocale());

        return view('manage.cp.exchange.distribution.regions.term', compact('marketplaceTerm', 'regions', 'marketplaceTerm', 'show_date', 'isEdit'))->render();
    }

    public function store(Request $request, $property_id, $id)
    {
        $tod = TermsOfDistribution::where('cp_property_id', $property_id)->findOrFail($id);

        $validator = $this->get_region_validator($request);
        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator);
        }

        $date_arr = explode('~', $request->availabilty_period);

        $payment = ExchangeService::getPaymentDetail($request);

        DB::beginTransaction();
        try {
            DistributionRegionRight::create([
                'tod_id' => $tod->id,
                'allowed_regions' => implode(',', $request->allowed_regions),
                'excepted_regions' => $request->excepted_regions ? implode(',', $request->excepted_regions) : null,
                'started_at' => $date_arr[0],
                'ended_at' => $date_arr[1],
                'payment_mode' => $request->payment_mode,
                'price' => $payment['price'],
                'update_count' => $payment['update_count'],
                'exclusivity' => $payment['exclusivity'],
                'revenue_share_cp' => $payment['revenue_share_cp'],
                'revenue_share_sp' => $payment['revenue_share_sp'],
                'payment_comments' => $request->payment_comments,
                'api_share_to' => $payment['api_share_to'],
                'download_resolution' => $payment['download_resolution'],
                'extra_terms' => $request->extra_terms,
            ]);

            if (1 == intval($request->get('save_to_marketplace_terms'))) {
                TermsOfMarketplace::create([
                    'user_id' => Auth::user()->id,
                    'property_id' => $property_id,
                    'name' => $request->tom_name,
                    'region_allowed' => implode(',', $request->allowed_regions),
                    'region_excepted' => $request->excepted_regions ? implode(',', $request->excepted_regions) : null,
                    'payment_mode' => $request->payment_mode,
                    'price' => $payment['price'],
                    'update_count' => $payment['update_count'],
                    'exclusivity' => $payment['exclusivity'],
                    'revenue_share_cp' => $payment['revenue_share_cp'],
                    'revenue_share_sp' => $payment['revenue_share_sp'],
                    'payment_comments' => $request->payment_comments,
                    'api_share_to' => $payment['api_share_to'],
                    'download_resolution' => $payment['download_resolution'],
                ]);
            }
            DB::commit();

            session()->flash('success', __('manage/cp/exchange/distribution.region_created_successfully'));

            return redirect()->route('manage.cp.exchange.distribution.edit', [$property_id, $id]);
        } catch (\Exception $exception) {
            DB::rollBack();

            session()->flash('error', __('manage/cp/exchange/distribution.region_created_failed'));

            return back()->withInput();
        }
    }

    public function edit($property_id, $id, $region)
    {
        $distribution_region = DistributionRegionRight::whereHas('tod', function ($query) use ($property_id, $id) {
            return $query->where('cp_property_id', $property_id)->where('id', $id);
        })->findOrFail($region);

        $regions = GeoRegion::getAll(App::getLocale());

        $terms = TermsOfMarketplace::where('property_id', $property_id)->pluck('name', 'id');

        if (Property::ID_FOR_ADMIN === $distribution_region->termsOfDistribution->sp_property_id) {
            return view('manage.cp.exchange.distribution.own_sp.regions.edit', compact('property_id', 'id', 'terms', 'regions', 'distribution_region'));
        }

        return view('manage.cp.exchange.distribution.regions.edit', compact('property_id', 'id', 'terms', 'regions', 'distribution_region'));
    }

    public function update(Request $request, $property_id, $id, $region)
    {
        $region = DistributionRegionRight::whereHas('tod', function ($query) use ($property_id, $id) {
            return $query->where('cp_property_id', $property_id)->where('id', $id);
        })->findOrFail($region);

        $validator = $this->get_region_validator($request);
        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator);
        }

        $payment = ExchangeService::getPaymentDetail($request);

        DB::beginTransaction();
        try {
            if ($request->availabilty_period) {
                $date_arr = explode('~', $request->availabilty_period);

                $region->update([
                    'allowed_regions' => implode(',', $request->allowed_regions),
                    'excepted_regions' => $request->excepted_regions ? implode(',', $request->excepted_regions) : null,
                    'started_at' => $date_arr[0],
                    'ended_at' => $date_arr[1],
                    'payment_mode' => $request->payment_mode,
                    'price' => $payment['price'],
                    'update_count' => $payment['update_count'],
                    'exclusivity' => $payment['exclusivity'],
                    'revenue_share_cp' => $payment['revenue_share_cp'],
                    'revenue_share_sp' => $payment['revenue_share_sp'],
                    'payment_comments' => $request->payment_comments,
                    'api_share_to' => $payment['api_share_to'],
                    'download_resolution' => $payment['download_resolution'],
                    'extra_terms' => $request->extra_terms,
                ]);
            } else {
                $region->update([
                    'allowed_regions' => implode(',', $request->allowed_regions),
                    'excepted_regions' => $request->excepted_regions ? implode(',', $request->excepted_regions) : null,
                    'payment_mode' => $request->payment_mode,
                    'price' => $payment['price'],
                    'update_count' => $payment['update_count'],
                    'exclusivity' => $payment['exclusivity'],
                    'revenue_share_cp' => $payment['revenue_share_cp'],
                    'revenue_share_sp' => $payment['revenue_share_sp'],
                    'payment_comments' => $request->payment_comments,
                    'api_share_to' => $payment['api_share_to'],
                    'download_resolution' => $payment['download_resolution'],
                    'extra_terms' => $request->extra_terms,
                ]);
            }

            if (1 == intval($request->get('save_to_marketplace_terms'))) {
                TermsOfMarketplace::create([
                    'user_id' => Auth::user()->id,
                    'property_id' => $property_id,
                    'name' => $request->tom_name,
                    'region_allowed' => implode(',', $request->allowed_regions),
                    'region_excepted' => $request->excepted_regions ? implode(',', $request->excepted_regions) : null,
                    'payment_mode' => $request->payment_mode,
                    'price' => $payment['price'],
                    'update_count' => $payment['update_count'],
                    'exclusivity' => $payment['exclusivity'],
                    'revenue_share_cp' => $payment['revenue_share_cp'],
                    'revenue_share_sp' => $payment['revenue_share_sp'],
                    'payment_comments' => $request->payment_comments,
                    'api_share_to' => $payment['api_share_to'],
                    'download_resolution' => $payment['download_resolution'],
                ]);
            }
            DB::commit();

            session()->flash('success', __('manage/cp/exchange/distribution.region_updated_successfully'));

            return redirect()->route('manage.cp.exchange.distribution.edit', [$property_id, $id]);
        } catch (\Exception $exception) {
            DB::rollBack();

            session()->flash('error', __('manage/cp/exchange/distribution.region_updated_failed'));

            return back()->withInput();
        }
    }

    public function show($property_id, $id, $region)
    {
        $distribution_region = DistributionRegionRight::whereHas('tod', function ($query) use ($property_id, $id) {
            return $query->where('cp_property_id', $property_id)->where('id', $id);
        })->findOrFail($region);

        return view('manage.cp.exchange.distribution.regions.show', compact('property_id', 'id', 'distribution_region'));
    }

    public function destroy($property_id, $id, $region)
    {
        $regioin_right = DistributionRegionRight::whereHas('tod', function ($query) use ($property_id, $id) {
            return $query->where('cp_property_id', $property_id)->where('id', $id);
        })->findOrFail($region);

        $regioin_right->delete();

        session()->flash('success', __('manage/cp/exchange/distribution.region_deleted_successfully'));

        return redirect()->route('manage.cp.exchange.distribution.edit', [$property_id, $id]);
    }

    private function get_region_validator($request)
    {
        $validator = Validator::make($request->all(), [
            'allowed_regions' => 'required',
            'payment_mode' => 'required',
        ]);
        $validator->after(function ($validator) use ($request) {
            ExchangeService::validateRegion($request, $validator);
            ExchangeService::validatePaymentMode($request, $validator);
        });

        return $validator;
    }
}
