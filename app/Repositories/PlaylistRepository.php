<?php

namespace App\Repositories;

use Carbon\Carbon;
use App\Models\Playlist;
use App\Models\Property;
use App\Models\PropertySP;
use App\Models\TermsOfDistribution;

class PlaylistRepository
{
    /**
     * @var \App\Models\Playlist
     */
    private $playlist;

    public function __construct(Playlist $playlist)
    {
        $this->playlist = $playlist;
    }

    public function createPlaylist(Property $property, array $data, $isPublished = false)
    {
        $this->playlist->fill($data);
        $this->playlist->publish = $isPublished ? 1 : 0;
        $this->playlist->property_id = $property->id;
        $this->playlist->save();

        return $this->playlist;
    }

    /**
     * Get all playlists by property_id
     * This method commonly used by CP Property.
     *
     * @param int $property_id
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAllPlaylistsByPropertyId($property_id)
    {
        return $this->playlist->where([
                'property_id' => $property_id,
                'status' => Playlist::STATUS_READY,
            ])
            ->latest()
            ->get();
    }

    public function getServiceProviders(Playlist $playlist)
    {
        $regionRights = ['regionRights' => function ($region) {
            $region->where(function ($q) {
                $q->whereNull('started_at');
                $q->whereNull('ended_at');
            });
            $region->orWhere(function ($q) {
                $q->where('started_at', '<=', Carbon::now());
                $q->where('ended_at', '>=', Carbon::now());
            });
        }];
        $playlist->load(
            [
                'contentProvider.internalTod',
                'tods' => function ($q) use ($regionRights) {
                    $q->where('status', TermsOfDistribution::STATUS_ACTIVE)
                      ->with($regionRights);
                },
            ]
        );

        $serviceProviders = collect([]);

        $playlist->tods->each(function ($termsOfDistribution) use (&$serviceProviders) {
            if (optional($termsOfDistribution->regionRights)->count() &&
                $termsOfDistribution->serviceProvider &&
                !$serviceProviders->contains('id', $termsOfDistribution->serviceProvider->id)
            ) {
                //external tod
                $serviceProviders->push($termsOfDistribution->serviceProvider);
            }
        });

        $internalTod = optional($playlist->contentProvider)->internalTod;
        if ($internalTod && optional($internalTod->regionRights)->count()) {
            //internal tod
            $internalTodSp = PropertySP::where('organization_id', $internalTod->cp_organization_id)
                ->get()
                ->whereNotIn('id', $serviceProviders->pluck('id')->all());
            foreach ($internalTodSp as $sp) {
                if (!$serviceProviders->contains('id', $sp->id)) {
                    $serviceProviders->push($sp);
                }
            }
        }

        return $serviceProviders;
    }
}
