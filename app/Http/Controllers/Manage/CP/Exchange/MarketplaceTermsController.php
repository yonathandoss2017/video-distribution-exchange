<?php

namespace App\Http\Controllers\Manage\CP\Exchange;

use App\Models\GeoRegion;
use App\Models\PropertyCP;
use Illuminate\Http\Request;
use App\Services\ExchangeService;
use App\Models\TermsOfMarketplace;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MarketplaceTermsController extends Controller
{
    public function index(Request $request, PropertyCP $property)
    {
        $property_id = $property->id;
        $search = $request->get('search') ?? '';
        $sort = $request->get('sort') ?? 'desc';

        $terms = TermsOfMarketplace::where('property_id', $property->id)
            ->when($search, function ($query) use ($search) {
                return $query->where('name', 'like', '%'.$search.'%');
            })
            ->withCount(['playlists' => function ($qurey) {
                $qurey->published();
            }])
            ->orderby('updated_at', $sort)
            ->paginate(10);

        return view('manage.cp.exchange.marketplace-terms.index', compact('property_id', 'search', 'sort', 'terms'));
    }

    public function create($property_id)
    {
        $regions = GeoRegion::getAll(App::getLocale());

        return view('manage.cp.exchange.marketplace-terms.create', compact('property_id', 'regions'));
    }

    public function store(Request $request, PropertyCP $property)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'allowed_regions' => 'required',
            'payment_mode' => 'required',
        ]);

        $validator->after(function ($validator) use ($request) {
            ExchangeService::validateRegion($request, $validator);
            ExchangeService::validatePaymentMode($request, $validator);
        });

        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator);
        }

        $payment = ExchangeService::getPaymentDetail($request);

        TermsOfMarketplace::create([
            'user_id' => Auth::user()->id,
            'property_id' => $property->id,
            'name' => $request->name,
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

        session()->flash('success', __('manage/cp/exchange/marketplace_terms.created_successfully'));

        return redirect()->route('manage.cp.exchange.marketplace-terms.index', $property->id);
    }

    public function edit(Request $request, $property_id, TermsOfMarketplace $marketplaceTerm)
    {
        $regions = GeoRegion::getAll(App::getLocale());

        return view('manage.cp.exchange.marketplace-terms.edit', compact('property_id', 'marketplaceTerm', 'regions'));
    }

    public function update(Request $request, $property_id, TermsOfMarketplace $marketplaceTerm)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'allowed_regions' => 'required',
            'payment_mode' => 'required',
        ]);

        $validator->after(function ($validator) use ($request) {
            ExchangeService::validateRegion($request, $validator);
            ExchangeService::validatePaymentMode($request, $validator);
        });

        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator);
        }

        $payment = ExchangeService::getPaymentDetail($request);

        $marketplaceTerm->name = $request->name;
        $marketplaceTerm->region_allowed = implode(',', $request->allowed_regions);
        $marketplaceTerm->region_excepted = $request->excepted_regions ? implode(',', $request->excepted_regions) : null;
        $marketplaceTerm->payment_mode = $request->payment_mode;
        $marketplaceTerm->price = $payment['price'];
        $marketplaceTerm->update_count = $payment['update_count'];
        $marketplaceTerm->exclusivity = $payment['exclusivity'];
        $marketplaceTerm->revenue_share_cp = $payment['revenue_share_cp'];
        $marketplaceTerm->revenue_share_sp = $payment['revenue_share_sp'];
        $marketplaceTerm->payment_comments = $request->payment_comments;
        $marketplaceTerm->api_share_to = $payment['api_share_to'];
        $marketplaceTerm->download_resolution = $payment['download_resolution'];
        $marketplaceTerm->save();

        session()->flash('success', __('manage/cp/exchange/marketplace_terms.updated_successfully'));

        return redirect()->route('manage.cp.exchange.marketplace-terms.index', $property_id);
    }

    public function show($property_id, TermsOfMarketplace $marketplaceTerm)
    {
        return view('manage.cp.exchange.marketplace-terms.show', compact('property_id', 'marketplaceTerm'));
    }

    public function destroy($property_id, TermsOfMarketplace $marketplaceTerm)
    {
        $marketplaceTerm->delete();

        session()->flash('success', __('manage/cp/exchange/marketplace_terms.deleted_successfully'));

        return redirect()->route('manage.cp.exchange.marketplace-terms.index', $property_id);
    }
}
