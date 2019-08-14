<?php

namespace App\Services\Solr;

use Exception;
use Carbon\Carbon;
use Solarium\Client;

class SolrQueryService
{
    protected $client;
    protected $minimum_match;
    protected $today;
    const CONST_MSG = 'Please check parsed query.';

    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->minimum_match = config('solarium.marketplace.minimum_match');
        $this->today = Carbon::today()->timestamp;
    }

    public function facetQuery($queryKeywords)
    {
        $client = $this->client;
        $query = $client->createSelect();
        $facetSet = $query->getFacetSet();

        // create a facet query instance and set options
        $facetSet->setPrefix($queryKeywords);
        $facetSet->createFacetQuery('published');
        $facetSet->createFacetField('keywords')->setField('title_auto');
        $facetSet->setLimit(50);
        $facetSet->setSort('index');
        $query->setStart(0)->setRows(0);

        try {
            $resultset = $client->select($query);

            return $resultset->getResponse()->getBody();
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'messages' => self::CONST_MSG]);
        }
    }

    public function mltQuery($setKeywords, $setFilter, $filter, $start, $limit, $genre)
    {
        $client = $this->client;

        $query = $client->createMoreLikeThis(['handler' => 'select']);
        $query->setQuery($setKeywords.$setFilter);
        $query->createFilterQuery('dataPublished')->setQuery('published: true');
        $query->createFilterQuery('dataScheduled')->setQuery("published_at: [* TO $this->today]");
        $query->createFilterQuery('notDeleted')->setQuery('-deleted_at:[* TO *]');
        if (!empty(trim($genre))) {
            $query->createFilterQuery('dataGenre')->setQuery("genre: $genre");
        }
        if (!empty(trim($filter))) {
            $query->createFilterQuery('dataType')->setQuery("type: $filter");
        }
        $query->setMltFields('title,description,tags,stars,genre');
        $query->setMinimumDocumentFrequency(1);
        $query->setMinimumTermFrequency(1);
        $query->setBoost(true);
        $query->setQueryFields('title,description,tags,stars');
        $query->setInterestingTerms('title');
        $query->setMatchInclude(true);
        $query->setStart($start)->setRows($limit);

        try {
            $resultset = $client->select($query)->getResponse()->getBody();
            $resToArray = json_decode($resultset, true);
            if (0 == $resToArray['response']['numFound'] && '*' == $genre) {
                return $this->eDismaxQuery($setKeywords.$setFilter, $genre, $filter, $start, $limit, false, false, false);
            }

            return $resultset;
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'messages' => self::CONST_MSG]);
        }
    }

    public function eDismaxQuery($keywords, $genre, $filter, $start, $limit, $latestUpdate, $mostpopular = 0, $justIn = 0, $options = [])
    {
        $client = $this->client;
        $query = $client->createSelect();

        $query->setQuery($keywords);

        $today_time = Carbon::today()->timestamp;
        $query->createFilterQuery('dataStartdate')->setQuery('publish_start_date:[0 TO '.$today_time.']');
        $query->createFilterQuery('dataEnddate')->setQuery('publish_end_date:0 OR publish_end_date:['.$today_time.' TO *]');

        $query->createFilterQuery('dataPublished')->setQuery('published: true');
        $query->createFilterQuery('notDeleted')->setQuery('-deleted_at:[* TO *]');

        $edismax = $query->getEDisMax();
        $edismax->setMinimumMatch($this->minimum_match);
        $query->setStart($start)->setRows($limit);

        if ($latestUpdate) {
            $query->addSort('updated_at', $query::SORT_DESC);
        }

        if (!empty($options['playlist_ids'])) {
            $query->createFilterQuery('dataPlaylist')->setQuery($options['playlist_ids']);
            goto result;
        }

        if (!empty(trim($filter))) {
            $query->createFilterQuery('dataType')->setQuery("type: $filter");
        }
        if (!empty(trim($genre))) {
            $query->createFilterQuery('dataGenre')->setQuery("genre: $genre");
        }
        if ($mostpopular) {
            $query->createFilterQuery('mostpopular')->setQuery('sp_count:[1 TO *]');
            $query->addSort('sp_count', $query::SORT_DESC);
        }
        if ($justIn) {
            $query->addSort('created_at', $query::SORT_DESC);
        }

        if (isset($options['random']) && $options['random']) {
            $randString = mt_rand();
            $query->addSort('random_'.$randString, $query::SORT_DESC);
        }

        if (!empty($options['propertyId'])) {
            $query->createFilterQuery('dataPropertyId')->setQuery($options['propertyId']);
        }
        if (!empty($options['playlistId'])) {
            $query->createFilterQuery('dataPlaylistId')->setQuery('id: playlist-'.$options['playlistId']);
        }

        result:
        try {
            $resultset = $client->select($query)->getResponse()->getBody();
            $resToArray = json_decode($resultset, true);
            if (0 == $resToArray['response']['numFound'] && '*' == $genre) {
                return 'noresult';
            }

            return $resultset;
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'messages' => self::CONST_MSG]);
        }
    }

    public function generateKeywords($key, $type)
    {
        if (!empty(trim($key))) {
            // checking for non UTF-8 encoding character
            if (mb_strlen($key, 'Big5') == strlen($key)) {
                return $key;
            } else {
                $key = preg_replace('/\\s+/iu', '', $key);
                if ('space' == $type) {
                    preg_match_all('/./u', preg_replace('/\\s+/iu', '', $key), $matches);

                    return (count($matches[0])) ? implode(' ', $matches[0]) : '*';
                } else {
                    return $key;
                }
            }
        }

        return '*';
    }
}
