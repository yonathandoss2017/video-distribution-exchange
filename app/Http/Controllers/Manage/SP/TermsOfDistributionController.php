<?php

namespace App\Http\Controllers\Manage\SP;

use Carbon\Carbon;
use App\Models\Property;
use App\Models\PropertyCP;
use App\Models\PropertySP;
use Illuminate\Http\Request;
use App\Models\TermsOfDistribution;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Models\DistributionRegionRight;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Exchange\SpDeclineTodEmail;

class TermsOfDistributionController extends Controller
{
    public function index(PropertySP $property, Request $request)
    {
        $spPendingTODCount = TermsOfDistribution::where('sp_property_id', $property->id)
            ->where('status', TermsOfDistribution::STATUS_SP_PENDING)
            ->count();

        $organizationId = $property->organization->id;

        $status = $this->getTransformStatus($request->get('status'));
        $termsOfDistributions = TermsOfDistribution::with(['contentProvider.organization'])
            ->withCount('playlists')
            ->when($status, function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->when(!$status, function ($query) use ($status) {
                $query->whereNotIn('status', [TermsOfDistribution::STATUS_DRAFT, TermsOfDistribution::STATUS_SP_PENDING]);
            })
            ->when($request->get('search'), function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->get('search').'%');
            })
            ->where(function ($query) use ($property, $organizationId) {
                $query->where('sp_property_id', $property->id)
                    ->orWhere(function ($q) use ($property, $organizationId) {
                        $q->where('cp_organization_id', $organizationId)
                            ->where('sp_property_id', Property::ID_FOR_ADMIN)
                            ->where('status', TermsOfDistribution::STATUS_ACTIVE)
                            ->whereHas('serviceProviders', function ($q1) use ($property) {
                                $q1->where('property_id', $property->id);
                            });
                    });
            })
            ->whereNull('sp_deleted_at')
            ->orderBy('status', 'asc')
            ->latest('updated_at')
            ->paginate(15);

        return view('manage.sp.tod.index', [
            'property_id' => $property->id,
            'termsOfDistributions' => $termsOfDistributions,
            'spPendingTODCount' => $spPendingTODCount,
            'status' => $request->get('status'),
        ]);
    }

    public function show(PropertySP $property, $id)
    {
        $tod = TermsOfDistribution::with(['contentProvider.organization', 'serviceProvider.organization', 'regionRights', 'userCreator', 'userUpdater', 'playlistsWithTrashed' => function ($q) {
            $q->withCount('entries');
        }])
            ->whereNull('sp_deleted_at')
            ->findOrFail($id);

        $data = [
            'property_id' => $property->id,
            'tod' => $tod,
        ];

        return view('manage.sp.tod.show', $data);
    }

    public function regionRight(PropertySP $property, $id, $region_right_id)
    {
        $regionRight = DistributionRegionRight::with('tod')->findOrFail($region_right_id);
        $data = [
            'property_id' => $property->id,
            'regionRight' => $regionRight,
        ];

        return view('manage.sp.tod.show_region_right', $data);
    }

    public function accept(PropertySP $property, $id)
    {
        $tod = TermsOfDistribution::with('playlists', 'contentProvider.license_notifications')
            ->findOrFail($id);
        if (TermsOfDistribution::STATUS_SP_PENDING != $tod->status) {
            abort(403);
        }
        $tod->status = TermsOfDistribution::STATUS_PLATFORM_REVIEW;
        $tod->save();

        session()->flash('success', __('manage/sp/exchange/tod.tod_accepted_successfully'));

        return redirect()->route('manage.sp.tod.index', $property->id);
    }

    public function delete(PropertySP $property, Request $request)
    {
        $deleteMode = $request->get('delete_mode');
        $tod = TermsOfDistribution::where('id', $request->get('tod_id'))
            ->where('sp_property_id', $property->id)
            ->with('playlists')
            ->firstOrFail();

        $cp = PropertyCP::findOrFail($tod->cp_property_id);
        $users = $cp->license_notifications;

        $message = '';
        switch ($deleteMode) {
            case 3: // decline
                $message = 'declined';
                $tod->status = TermsOfDistribution::STATUS_SP_DECLINED;
                $tod->save();
                Notification::send($users, new SpDeclineTodEmail($tod, $property, $cp, App::getLocale()));
            break;

            case 2: // delete
                $message = 'deleted';
                $tod->sp_deleted_at = Carbon::now();
                $tod->save();
            break;

            case 0: // only discontinue
            case 1: // discontinue & delete
                $message = 0 == $deleteMode ? 'discontinued' : 'discontinued & deleted';
                $tod->discontinue();
                if (1 == $deleteMode) {
                    $tod->sp_deleted_at = Carbon::now();
                }
                $tod->save();
            break;

            default:
                abort(403);
            break;
        }

        session()->flash('success', __('manage/sp/exchange/tod.terms_status_updated_success', ['status_params' => $message]));

        // if delete mode performed, we will redirect it to index tod page
        // due to prevent error 404 not found in detail page
        if (url()->previous() == route('manage.sp.tod.show', [$property->id, $tod->id])
            && in_array($deleteMode, [1, 2])) {
            return redirect()->route('manage.sp.tod.index', $property->id);
        }

        return redirect()->back();
    }

    /**
     * Get transformed status by given status from query string.
     *
     * @param string $status
     * @param bool   $is_for_url
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getTransformStatus($status, $is_for_url = true)
    {
        $haystack = $this->transformedStatusList();

        if (!$is_for_url) {
            $haystack = $haystack->flip();
        }

        return $haystack->get($status);
    }

    /**
     * List all transformed status that used in query string.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function transformedStatusList()
    {
        return collect([
            'pending' => TermsOfDistribution::STATUS_SP_PENDING,
            'active' => TermsOfDistribution::STATUS_ACTIVE,
            'declined' => TermsOfDistribution::STATUS_SP_DECLINED,
            'discontinue' => TermsOfDistribution::STATUS_SP_DISCONTINUE,
            'revoked' => TermsOfDistribution::STATUS_CP_REVOKED,
        ]);
    }
}
