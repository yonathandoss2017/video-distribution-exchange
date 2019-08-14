<?php

namespace App\Http\Controllers\Admin;

use App\Models\Property;
use Illuminate\Http\Request;
use App\Models\PlaylistProperty;
use App\Models\TermsOfDistribution;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Models\DistributionRegionRight;
use App\Jobs\Solr\SyncPlaylistToSolrJob;
use Illuminate\Support\Facades\Notification;
use App\Events\PlaylistWhitelistUpdatedEvent;
use App\Notifications\Exchange\SpAcceptTodEmail;
use App\Jobs\Solr\SyncPlaylistEntriesToSolrMarketplace;

class ExchangeController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status');
        $termsOfDistributions = TermsOfDistribution::with(['contentProvider.organization', 'serviceProvider.organization'])
            ->withCount('playlists')
            ->when($status, function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->when($request->get('search'), function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->get('search').'%');
            })
            ->whereIn('status', [TermsOfDistribution::STATUS_PLATFORM_REVIEW, TermsOfDistribution::STATUS_PLATFORM_REJECTED])
            ->latest('created_at')
            ->paginate(10);

        return view('admin.pages.exchange.index', [
            'termsOfDistributions' => $termsOfDistributions,
            'status' => $status,
        ]);
    }

    public function show($id)
    {
        $termsOfDistribution = TermsOfDistribution::with([
            'serviceProvider.organization',
            'regionRights',
            'playlistsWithTrashed' => function ($q) {
                $q->withCount(['entries' => function ($query) {
                    $query->published();
                }]);
            },
        ])->whereIn('status', [
            TermsOfDistribution::STATUS_PLATFORM_REVIEW,
            TermsOfDistribution::STATUS_PLATFORM_REJECTED,
        ])->findOrFail($id);

        return view('admin.pages.exchange.show', compact('termsOfDistribution'));
    }

    public function regionRight($id, $region_right_id)
    {
        $regionRight = DistributionRegionRight::whereHas('tod', function ($q) use ($id) {
            $q->where('id', $id);
        })->with('tod')->findOrFail($region_right_id);

        return view('admin.pages.exchange.show_region_right', compact('regionRight'));
    }

    public function approve($id)
    {
        $tod = TermsOfDistribution::with('playlists', 'contentProvider', 'contentProvider.license_notifications', 'serviceProvider')
            ->where('status', TermsOfDistribution::STATUS_PLATFORM_REVIEW)
            ->findOrFail($id);

        $tod->status = TermsOfDistribution::STATUS_ACTIVE;
        $tod->save();

        if (Property::ID_FOR_ADMIN == $tod->sp_property_id) {
            $playlists = $tod->contentProvider->playlists()
                ->published()
                ->get();
            foreach ($playlists as $playlist) {
                SyncPlaylistToSolrJob::dispatch($playlist);
                SyncPlaylistEntriesToSolrMarketplace::dispatch($playlist);
            }
        } else {
            //sync data to solr
            foreach ($tod->playlists as $playlist) {
                event(new PlaylistWhitelistUpdatedEvent($playlist));
            }

            //send email to cp users
            $users = $tod->contentProvider->license_notifications;
            Notification::send($users, new SpAcceptTodEmail($tod->serviceProvider, $tod, App::getLocale()));

            foreach ($tod->playlists as $playlist) {
                $count = PlaylistProperty::findRecord($playlist->property_id, $tod->serviceProvider->id, $playlist->id)->count();
                if (0 == $count) {
                    PlaylistProperty::createOrUpdateRecord($playlist->property_id, $tod->serviceProvider->id, $playlist->id);
                }
            }
        }
        session()->flash('success', __('manage/sp/exchange/tod.tod_accepted_successfully'));

        return redirect()->route('admin.exchange.index', $id);
    }

    public function reject($id)
    {
        $tod = TermsOfDistribution::where('status', TermsOfDistribution::STATUS_PLATFORM_REVIEW)->findOrFail($id);

        $tod->status = TermsOfDistribution::STATUS_PLATFORM_REJECTED;
        $tod->save();

        session()->flash('success', __('manage/sp/exchange/tod.tod_rejected_successfully'));

        return redirect()->route('admin.exchange.index', $id);
    }
}
