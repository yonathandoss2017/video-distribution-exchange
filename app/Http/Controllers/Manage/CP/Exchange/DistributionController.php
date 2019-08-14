<?php

namespace App\Http\Controllers\Manage\CP\Exchange;

use Validator;
use Carbon\Carbon;
use App\Models\Property;
use App\Models\GeoRegion;
use App\Models\PropertyCP;
use App\Models\PropertySP;
use App\Models\RequestLog;
use Illuminate\Http\Request;
use App\Models\TermsOfMarketplace;
use Illuminate\Support\Facades\DB;
use App\Models\TermsOfDistribution;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\DistributionRegionRight;
use App\Services\Storage\StorageService;
use App\Http\Resources\PropertyCollection;
use App\Services\Serve\PropertyImageService;
use App\Notifications\TermsOfDistributionPublishedNotification;

class DistributionController extends Controller
{
    public function index(Request $request, $property_id)
    {
        $status = $request->status;
        $search = $request->input('search');
        $sort = $request->input('sort');
        $show_sort = $sort ?? 'desc';

        $cp = PropertyCP::find($property_id);
        $ownSpTod = TermsOfDistribution::where('cp_property_id', $property_id)->where('sp_property_id', Property::ID_FOR_ADMIN)->first();
        if (!$ownSpTod) {
            $ownSpTod = TermsOfDistribution::create([
                'cp_organization_id' => $cp->organization_id,
                'cp_property_id' => $property_id,
                'sp_property_id' => Property::ID_FOR_ADMIN,
                'status' => TermsOfDistribution::STATUS_ACTIVE,
                'name' => 'Whitelist for own SP',
                'published_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'creator' => Auth::User()->id,
                'updated_at' => null,
            ]);
        }

        if (0 == $ownSpTod->regionRights->count()) {
            DistributionRegionRight::create([
                'tod_id' => $ownSpTod->id,
                'allowed_regions' => GeoRegion::REGION_GLOBAL_CODE,
            ]);
        }

        $termsOfDistributions = $cp->termsOfDistributions()->withCount('playlists')
            ->with('serviceProvider.organization')
            ->when($search, function ($query) use ($search) {
                return $query->where('name', 'like', '%'.$search.'%');
            })
            ->when($status, function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->exceptOwnSpTod()
            ->orderby('updated_at', $show_sort)
            ->paginate(10);

        return view('manage.cp.exchange.distribution.index', compact('termsOfDistributions', 'ownSpTod', 'property_id', 'status', 'search', 'sort'));
    }

    public function create($property_id)
    {
        return view('manage.cp.exchange.distribution.create', compact('property_id'));
    }

    public function store(Request $request, PropertyCP $property)
    {
        $log_id = intval($request->get('log_id'));
        $tom_id = intval($request->get('tom_id'));

        $marketplace = null;
        if ($tom_id > 0) {
            $marketplace = TermsOfMarketplace::where('property_id', $property->id)->findOrFail($tom_id);
        }

        $requestLog = RequestLog::with('serviceProviders')
            ->with(['playlists' => function ($query) use ($property, $tom_id) {
                $query->where('property_id', $property->id);
                $query->when($tom_id > 0, function ($q) use ($tom_id) {
                    $q->where('tom_id', $tom_id);
                });
            }])
            ->findOrFail($log_id);

        if ($requestLog->serviceProviders->count() < 1) {
            session()->flash('error', __('manage/cp/exchange/distribution.terms_of_distribution_created_failed'));

            return back()->withInput();
        }

        if (!$marketplace) {
            $tom_ids = $requestLog->playlists->pluck('tom_id')->unique()->toArray();
            if (1 == count($tom_ids) && $tom_ids[0] > 0) {
                $marketplace = TermsOfMarketplace::where('property_id', $property->id)->findOrFail($tom_ids[0]);
            }
        }

        DB::beginTransaction();
        try {
            $user = Auth::user();

            foreach ($requestLog->serviceProviders as $serviceProvider) {
                $distribution = TermsOfDistribution::create([
                    'cp_organization_id' => $property->organization_id,
                    'cp_property_id' => $property->id,
                    'sp_property_id' => $serviceProvider->id,
                    'name' => $serviceProvider->name,
                    'internal_remarks' => null,
                    'contract' => null,
                    'contract_name' => null,
                    'creator' => $user->id,
                    'updater' => $user->id,
                ]);

                if ($marketplace) {
                    DistributionRegionRight::create([
                        'tod_id' => $distribution->id,
                        'allowed_regions' => implode(',', $marketplace->region_allowed),
                        'excepted_regions' => implode(',', $marketplace->region_excepted),
                        'payment_mode' => $marketplace->payment_mode,
                        'price' => $marketplace->price,
                        'update_count' => $marketplace->update_count,
                        'exclusivity' => $marketplace->exclusivity,
                        'revenue_share_cp' => $marketplace->revenue_share_cp,
                        'revenue_share_sp' => $marketplace->revenue_share_sp,
                        'payment_comments' => $marketplace->payment_comments,
                        'api_share_to' => implode(',', $marketplace->api_share_to),
                        'download_resolution' => implode(',', $marketplace->download_resolution),
                    ]);
                }

                $distribution->playlists()->attach($requestLog->playlists->pluck('id')->toArray());
            }

            DB::commit();

            session()->flash('success', __('manage/cp/exchange/distribution.terms_of_distribution_created_successfully'));

            return redirect()->route('manage.cp.exchange.distribution.index', $property->id);
        } catch (\Exception $e) {
            DB::rollback();

            session()->flash('error', __('manage/cp/exchange/distribution.terms_of_distribution_created_failed'));

            return back()->withInput();
        }
    }

    public function revoke(Request $request, $property_id, $distribution_id)
    {
        $termsOfDistribution = TermsOfDistribution::with('contentProvider')->where('cp_property_id', $property_id)->findOrFail($distribution_id);

        if (Property::ID_FOR_ADMIN === $termsOfDistribution->sp_property_id
            || !in_array($termsOfDistribution->status, [TermsOfDistribution::STATUS_ACTIVE, TermsOfDistribution::STATUS_SP_PENDING])) {
            abort(403);
        }

        $termsOfDistribution->revoke();
        session()->flash('success', __('manage/cp/exchange/distribution.revoke_successfully'));

        return redirect()->route('manage.cp.exchange.distribution.index', $property_id);
    }

    public function destroy(Request $request, $property_id, $distribution_id)
    {
        $termsOfDistribution = TermsOfDistribution::where('cp_property_id', $property_id)->findOrFail($distribution_id);

        if (Property::ID_FOR_ADMIN === $termsOfDistribution->sp_property_id) {
            abort(403);
        }

        $message = __('manage/cp/exchange/distribution.delete_successfully');
        if ($request->isRevoke) {
            $termsOfDistribution->revoke();
            $message = __('manage/cp/exchange/distribution.revoke_delete_successfully');
        }
        $termsOfDistribution->cp_deleted_at = Carbon::now();
        $termsOfDistribution->save();

        session()->flash('success', $message);

        return redirect()->route('manage.cp.exchange.distribution.index', $property_id);
    }

    public function publish($property_id, $distribution_id)
    {
        $termsOfDistribution = TermsOfDistribution::with('serviceProvider.license_notifications')
            ->withCount(['serviceProvider', 'regionRights', 'playlists'])
            ->where('cp_property_id', $property_id)->findOrFail($distribution_id);

        if (!$termsOfDistribution->service_provider_count || !$termsOfDistribution->region_rights_count || !$termsOfDistribution->playlists_count) {
            abort(403);
        }

        $serviceProvider = $termsOfDistribution->serviceProvider;
        $licenseNotifications = $serviceProvider->license_notifications;

        foreach ($licenseNotifications as $licenseNotification) {
            $licenseNotification->notify(new TermsOfDistributionPublishedNotification($licenseNotification, $serviceProvider, App::getLocale()));
        }

        $termsOfDistribution->status = TermsOfDistribution::STATUS_SP_PENDING;
        $termsOfDistribution->published_at = Carbon::now();
        $termsOfDistribution->save();

        session()->flash('success', __('manage/cp/exchange/distribution.successfully_published'));

        return redirect()->route('manage.cp.exchange.distribution.index', $property_id);
    }

    public function duplicate($property_id, $id)
    {
        $distribution = TermsOfDistribution::with('regionRights', 'playlists')->where('cp_property_id', $property_id)->findOrFail($id)->replicate();

        DB::beginTransaction();
        try {
            $distribution->name = 'Copy of '.$distribution->name;
            $distribution->status = TermsOfDistribution::STATUS_DRAFT;
            $distribution->show_new_mark = true;
            $distribution->save();
            $distribution->regionRights->map(function ($regionRight) use ($distribution) {
                $regionRight->tod_id = $distribution->id;
                $regionRight->replicate()->save();
            });
            $distribution->playlists()->attach($distribution->playlists->pluck('id')->toArray());
            session()->flash('success', __('manage/cp/exchange/distribution.been_duplicated'));
            DB::commit();
        } catch (\Exception $e) {
            session()->flash('error', __('manage/cp/exchange/distribution.not_been_duplicated'));
            DB::rollBack();
        }

        return redirect()->route('manage.cp.exchange.distribution.index', $property_id);
    }

    public function revert_to_draft($property_id, $id)
    {
        $distribution = TermsOfDistribution::where('cp_property_id', $property_id)->findOrFail($id);

        $status_allowed_revert = [
            TermsOfDistribution::STATUS_SP_PENDING,
            TermsOfDistribution::STATUS_SP_DECLINED,
            TermsOfDistribution::STATUS_CP_REVOKED,
            TermsOfDistribution::STATUS_SP_DISCONTINUE,
            TermsOfDistribution::STATUS_PLATFORM_REVIEW,
            TermsOfDistribution::STATUS_PLATFORM_REJECTED,
        ];

        if (!in_array($distribution->status, $status_allowed_revert)) {
            abort(403);
        }

        $distribution->status = TermsOfDistribution::STATUS_DRAFT;
        $distribution->save();

        session()->flash('success', __('manage/cp/exchange/distribution.been_reverted'));

        return redirect()->route('manage.cp.exchange.distribution.edit', [$property_id, $distribution->id]);
    }

    public function edit($property_id, $id)
    {
        $distribution = TermsOfDistribution::with(['serviceProvider.organization', 'regionRights', 'playlists' => function ($query) {
            $query->withCount('readyEntries');
        }])
            ->where('cp_property_id', $property_id)->findOrFail($id);

        if (Property::ID_FOR_ADMIN === $distribution->sp_property_id) {
            return view('manage.cp.exchange.distribution.own_sp.edit', compact('property_id', 'id', 'distribution'));
        }

        if (TermsOfDistribution::STATUS_ACTIVE == $distribution->status || TermsOfDistribution::STATUS_SP_PENDING == $distribution->status) {
            abort(403);
        }

        $distribution->viewed();

        return view('manage.cp.exchange.distribution.edit', compact('property_id', 'id', 'distribution'));
    }

    public function show(Request $request, $property_id, $id)
    {
        $distribution = TermsOfDistribution::with([
            'serviceProvider.organization',
            'regionRights',
            'playlistsWithTrashed' => function ($q) {
                $q->withCount('readyEntries');
            },
        ])->where('cp_property_id', $property_id)->findOrFail($id);

        if (TermsOfDistribution::STATUS_DRAFT == $distribution->status) {
            abort(403);
        }

        $distribution->viewed();

        $show_pop = $request->get('show_pop') ?? 0;

        return view('manage.cp.exchange.distribution.show', compact('property_id', 'id', 'distribution', 'show_pop'));
    }

    public function summary_create($property_id)
    {
        return view('manage.cp.exchange.distribution.summary.create', compact('property_id'));
    }

    public function summary_store(Request $request, $property_id, StorageService $storageService)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'contract' => 'mimes:jpg,jpeg,png,pdf,doc,docx',
        ]);
        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator);
        }

        $cp = PropertyCP::find($property_id);
        DB::beginTransaction();
        try {
            $distribution = TermsOfDistribution::create([
                'cp_organization_id' => $cp->organization_id,
                'cp_property_id' => $property_id,
                'name' => $request->name,
                'internal_remarks' => $request->internal_remarks,
                'contract' => null,
                'contract_name' => null,
                'creator' => Auth::User()->id,
                'updater' => Auth::User()->id,
            ]);

            $contract = $this->storeContract($request, $storageService, $distribution);
            if (!is_array($contract)) {
                DB::rollBack();
                session()->flash('error', __('manage/cp/exchange/distribution.summary_updated_failed'));

                return back()->withInput();
            }
            if (!empty($contract['path'])) {
                $distribution->contract = $contract['path'];
                $distribution->contract_name = $contract['name'];
                $distribution->save();
            }

            DB::commit();
            session()->flash('success', __('manage/cp/exchange/distribution.summary_updated_successfully'));
        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('error', __('manage/cp/exchange/distribution.summary_updated_failed'));
        }

        return redirect()->route('manage.cp.exchange.distribution.edit', [$property_id, $distribution->id]);
    }

    public function summary_edit($property_id, $id)
    {
        $summary = TermsOfDistribution::where('cp_property_id', $property_id)->findOrFail($id);

        if (Property::ID_FOR_ADMIN === $summary->sp_property_id) {
            return view('manage.cp.exchange.distribution.own_sp.summary.edit', compact('property_id', 'summary'));
        }

        return view('manage.cp.exchange.distribution.summary.edit', compact('property_id', 'summary'));
    }

    public function summary_update(Request $request, $property_id, $id, StorageService $storageService)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'contract' => 'mimes:jpg,jpeg,png,pdf,doc,docx',
        ]);
        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator);
        }

        $distribution = TermsOfDistribution::where('cp_property_id', $property_id)->findOrFail($id);

        $update_datas = [
            'name' => $request->name,
            'internal_remarks' => $request->internal_remarks,
            'updater' => Auth::User()->id,
        ];
        if (empty($distribution->created_at)) {
            $update_datas['created_at'] = Carbon::now();
            $update_datas['creator'] = Auth::User()->id;
        }

        $contract = $this->storeContract($request, $storageService, $distribution);
        if (!is_array($contract)) {
            session()->flash('error', __('manage/cp/exchange/distribution.contract_upload_failed_warn_message'));

            return back()->withInput();
        }
        if ($contract['path']) {
            $update_datas['contract'] = $contract['path'];
            $update_datas['contract_name'] = $contract['name'];
            if (!empty($distribution->contract)) {
                $storageService->delete($distribution->contract);
            }
        }

        $distribution->update($update_datas);

        if (Property::ID_FOR_ADMIN === $distribution->sp_property_id) {
            session()->flash('success', __('manage/cp/exchange/distribution.internal_summary_updated_successfully'));
        } else {
            session()->flash('success', __('manage/cp/exchange/distribution.summary_updated_successfully'));
        }

        return redirect()->route('manage.cp.exchange.distribution.edit', [$property_id, $id]);
    }

    private function storeContract(Request $request, StorageService $storageService, $tod)
    {
        $contractPath = null;
        $filename = null;
        if ($request->hasFile('contract')) {
            $file = $request->file('contract');
            $pathFile = $tod->cp_organization_id.'/'.$tod->cp_property_id.'/tod_'.$tod->id;
            $contractFile = $storageService->store($file, $pathFile);
            if (200 == $contractFile->getData()->statusCode) {
                $contractPath = $contractFile->getData()->pathImg;
                $filename = $file->getClientOriginalName();
            } else {
                return null;
            }
        }

        return ['path' => $contractPath, 'name' => $filename];
    }

    public function sp_index(PropertyCP $property, $id)
    {
        $distribution = TermsOfDistribution::where('cp_property_id', $property->id)->findOrFail($id);
        if (Property::ID_FOR_ADMIN === $distribution->sp_property_id) {
            $distribution->load('serviceProviders.organization');

            $selectedSp = $distribution->serviceProviders->map(function ($sp) {
                return [
                    'id' => $sp->id,
                    'name' => $sp->name,
                    'organization_name' => $sp->organization->name,
                    'logo' => PropertyImageService::getImageUrl($sp, 'logo', null, 90),
                ];
            });

            return view('manage.cp.exchange.distribution.own_sp.sp.select', [
                'active_menu' => 'exchange',
                'property_id' => $property->id,
                'property' => $property,
                'tod' => $distribution,
                'selectedSp' => $selectedSp,
            ]);
        } else {
            $internal_whitelist_for_sp_ids = $property->internalTod ? $property->internalTod->serviceProviders->pluck('id')->toArray() : [];
            //filter internal whitelist for sp
            $sps = $property->organization->service_providers()->whereNotIn('id', $internal_whitelist_for_sp_ids)->get();
            $property_id = $property->id;

            return view('manage.cp.exchange.distribution.sp.select', compact('property_id', 'id', 'sps'));
        }
    }

    public function sp_search(Request $request, $property_id, $id)
    {
        $uuid = $request->get('keywords');
        $cp = PropertyCP::findOrFail($property_id);
        $sps = PropertySP::with('organization')
            ->where('uuid', $uuid)
            ->where('organization_id', '<>', $cp->organization_id)
            ->get();

        return view('manage.cp.exchange.distribution.sp.list', compact('sps', 'id', 'property_id'))->render();
    }

    public function sp_select($property_id, $id, $sp_id)
    {
        $distribution = TermsOfDistribution::findOrFail($id);
        if ($distribution->cp_property_id != $property_id) {
            abort(403);
        }
        $distribution->update(['sp_property_id' => $sp_id]);

        session()->flash('success', __('manage/cp/exchange/distribution.sp_updated_successfully'));

        return redirect()->route('manage.cp.exchange.distribution.edit', [$property_id, $id]);
    }

    public function sp_list(Request $request, PropertyCP $property)
    {
        //filter external whitelist for sp
        $external_whitelist_for_sp_ids = $property->tods()->where('sp_property_id', '>', 0)->pluck('sp_property_id')->unique()->toArray();

        $sps = PropertySP::with('organization')
            ->where('organization_id', $property->organization_id)
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->get('search').'%');
            })
            ->whereNotIn('id', $external_whitelist_for_sp_ids)
            ->paginate(10)
            ->appends($request->all());

        return new PropertyCollection($sps);
    }

    public function sp_store(Request $request, PropertyCP $property, $id)
    {
        $request->validate([
            'sps' => 'required|array',
        ]);

        $termsOfDistribution = TermsOfDistribution::where('cp_property_id', $property->id)->findOrFail($id);
        if (!$termsOfDistribution || Property::ID_FOR_ADMIN !== $termsOfDistribution->sp_property_id) {
            abort(403);
        }

        $termsOfDistribution->serviceProviders()->sync($request->get('sps'));

        $termsOfDistribution->status = TermsOfDistribution::STATUS_ACTIVE;
        $termsOfDistribution->updater = Auth::User()->id;
        $termsOfDistribution->save();

        session()->flash('success', __('manage/cp/exchange/distribution.internal_sp_updated_successfully'));

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function sp_delete(Request $request, PropertyCP $property, $distribution_id)
    {
        $termsOfDistribution = TermsOfDistribution::where('cp_property_id', $property->id)->where('sp_property_id', Property::ID_FOR_ADMIN)->findOrFail($distribution_id);

        if ($termsOfDistribution->serviceProviders()->detach($request->get('sp'))) {
            session()->flash('success', __('manage/cp/exchange/distribution.sp_deleted_successfully'));
        } else {
            session()->flash('error', __('manage/cp/exchange/distribution.sp_deleted_failed'));
        }

        return redirect()->route('manage.cp.exchange.distribution.edit', [$property->id, $termsOfDistribution->id]);
    }
}
