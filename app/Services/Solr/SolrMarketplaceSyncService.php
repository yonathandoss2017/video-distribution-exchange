<?php

namespace App\Services\Solr;

use Carbon\Carbon;
use Solarium\Client;
use App\Models\Entry;
use App\Models\Playlist;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Repositories\EntryRepository;
use App\Services\Serve\VideoImageService;
use Solarium\QueryType\Update\Query\Query;
use App\Services\Serve\PlaylistImageService;
use App\Services\Serve\PropertyImageService;

class SolrMarketplaceSyncService
{
    public function entry(Entry $entry)
    {
        $client = $this->getClient();
        $query = $client->createUpdate();
        if (!$entry->trashed() && $entry->playlists && Entry::STATUS_READY == $entry->status) {
            $doc = $this->entryDocument($query, $entry);
            $query->addDocuments([$doc]);
        } else {
            $query->addDeleteById("entry-{$entry->id}");
        }
        $client->update($query);

        DB::table('entries')->where('id', $entry->id)->update([
            'indexed_at_marketplace' => Carbon::now(),
        ]);
    }

    public function entries(Collection $entries, Playlist $playlist = null)
    {
        $client = $this->getClient();
        $query = $client->createUpdate();
        $docs = $this->entryDocuments($query, $entries, $playlist);
        $query->addDocuments($docs);
        $client->update($query);

        $entryIdsToBeUpdated = array_map(function ($doc) {
            return $doc->ivx_id;
        }, $docs);

        DB::table('entries')->whereIn('id', $entryIdsToBeUpdated)->update([
            'indexed_at_marketplace' => Carbon::now(),
        ]);
    }

    public function playlist(Playlist $playlist)
    {
        $client = $this->getClient();
        $query = $client->createUpdate();

        if (!$playlist->trashed()
            && Playlist::STATUS_READY == $playlist->status
            && $playlist->content_provider
            && Playlist::PUBLISH_STATUS_PUBLISHED == $playlist->publish_status
        ) {
            $doc = $this->playlistDocument($query, $playlist);
            $query->addDocuments([$doc]);
        } else {
            $query->addDeleteById('playlist-'.$playlist->id);
        }
        $client->update($query);

        //using query build so Entry saved will not triggered
        DB::table('playlists')->where('id', $playlist->id)->update([
            'indexed_at_marketplace' => Carbon::now(),
        ]);
    }

    public function playlists(Collection $playlists)
    {
        $client = $this->getClient();
        $query = $client->createUpdate();
        $docs = $this->playlistDocuments($query, $playlists);
        $query->addDocuments($docs);
        $client->update($query);

        $playlistIdsToBeUpdated = array_map(function ($doc) {
            return $doc->ivx_id;
        }, $docs);

        DB::table('playlists')->whereIn('id', $playlistIdsToBeUpdated)->update([
            'indexed_at_marketplace' => Carbon::now(),
        ]);
    }

    private function getClient()
    {
        $config = SolrService::getSolrConfig(SolrService::CORE_MARKETPLACE);
        $client = new Client($config);
        $client->setDefaultEndpoint(Config('solarium.default'));

        return $client;
    }

    private function entryDocument(Query $query, Entry $entry, Playlist $playlist = null)
    {
        $doc = $query->createDocument();
        $doc->id = "entry-{$entry->id}";
        $doc->ivx_id = $entry->id;
        $doc->title = $entry->name;
        $doc->type = 'video';
        $doc->cover_image = VideoImageService::getImageUrl($entry, $entry->property_id);
        $doc->property_id = $entry->property_id;
        $doc->property_name = $entry->content_provider->name;
        $doc->property_logo = PropertyImageService::getImageUrl($entry->content_provider);
        $doc->entry_duration = $entry->duration;
        $doc->videos_count = null;
        $doc->created_at = $entry->created_at->timestamp;
        $doc->updated_at = $entry->updated_at->timestamp;
        $doc->published = $entry->hasPublishedPlaylist($playlist);
        $doc->published_at = $entry->published_at->timestamp ?? null;

        $doc->publish_start_date = 0;
        $doc->publish_end_date = 0;
        $playlist_of_entry = null;
        if ($playlist) {
            $playlist_of_entry = $playlist;
        } elseif (isset($entry->playlists[0])) {
            $playlist_of_entry = $entry->playlists[0];
        }
        if ($playlist_of_entry) {
            if ($playlist_of_entry->publish_start_date) {
                $doc->publish_start_date = strtotime($playlist_of_entry->publish_start_date);
            }
            if ($playlist_of_entry->publish_end_date) {
                $doc->publish_end_date = strtotime($playlist_of_entry->publish_end_date);
            }
        }

        $doc->deleted_at = $entry->deleted_at->timestamp ?? null;
        $doc->description = $entry->description;
        $doc->tags = $entry->metadata->tags ?? null;
        $doc->genre = implode(',', $entry->getGenre());
        $doc->language = null;
        $doc->region = null;
        $doc->stars = null;
        $doc->sp_count = count(EntryRepository::getEntryServiceProviderIds($entry));
        $doc->playlist_ids = $entry->playlists->pluck('id')->all();

        return $doc;
    }

    private function entryDocuments(Query $query, Collection $entries, Playlist $playlist = null)
    {
        $docs = [];
        foreach ($entries as $entry) {
            //Only process entry with ivideostream or alivod ready, only process entry with playlists
            if (!($entry->hasPlatform(Entry::PLATFORM_IVIDEOSTREAM) || $entry->hasPlatform(Entry::PLATFORM_ALIVOD))
                || !count($entry->playlists)
                || Entry::STATUS_READY != $entry->status
            ) {
                continue;
            }
            $doc = $this->entryDocument($query, $entry, $playlist);
            $docs[] = $doc;
        }

        return $docs;
    }

    private function playlistDocument(Query $query, Playlist $playlist)
    {
        $doc = $query->createDocument();
        $doc->id = "playlist-{$playlist->id}";
        $doc->ivx_id = $playlist->id;
        $doc->title = $playlist->name;
        $doc->type = 'playlist';
        $doc->cover_image = PlaylistImageService::getImageUrl($playlist, $playlist->property_id);
        $doc->property_id = $playlist->property_id;
        $doc->property_name = $playlist->content_provider->name;
        $doc->property_logo = PropertyImageService::getImageUrl($playlist->content_provider);
        $doc->entry_duration = null;
        $doc->videos_count = $playlist->entries_count;
        $doc->created_at = $playlist->created_at->timestamp;
        $doc->updated_at = $playlist->updated_at->timestamp;
        $doc->published = 1;
        $doc->published_at = null;
        $doc->publish_start_date = $playlist->publish_start_date ? strtotime($playlist->publish_start_date) : 0;
        $doc->publish_end_date = $playlist->publish_end_date ? strtotime($playlist->publish_end_date) : 0;
        $doc->deleted_at = $playlist->deleted_at->timestamp ?? null;
        $doc->description = $playlist->description;
        $doc->tags = null;
        $doc->genre = $playlist->genre;
        $doc->language = $playlist->language;
        $doc->region = $playlist->region;
        $doc->stars = $playlist->stars;
        $doc->sp_count = $playlist->termsOfDistributionServiceProvidersCount();
        $doc->playlist_ids = null;

        return $doc;
    }

    private function playlistDocuments(Query $query, Collection $playlists)
    {
        $docs = [];
        foreach ($playlists as $playlist) {
            if (Playlist::STATUS_READY == $playlist->status
            && $playlist->content_provider
            && Playlist::PUBLISH_STATUS_PUBLISHED == $playlist->publish_status) {
                $doc = $this->playlistDocument($query, $playlist);
                $docs[] = $doc;
            }
        }

        return $docs;
    }
}
