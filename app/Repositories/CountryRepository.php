<?php

namespace App\Repositories;

use App\Models\Country;
use Webpatser\Countries\Countries;

class CountryRepository
{
    /**
     * @var Country
     */
    private $country;

    public function __construct(Country $country)
    {
        $this->country = $country;
    }

    public function getAll()
    {
        return $this->country->all();
    }

    /**
     * @return array
     */
    public function getCountryIsoCode()
    {
        $countries = $this->getAll()->toArray();

        return array_map(
            function ($country) {
                return $country['iso_3166_2'];
            },
            $countries
        );
    }

    public function getCountryIsoCodeWithValue(): array
    {
        $countries_keys = Countries::pluck('iso_3166_2')->toArray();
        $countries_keys = array_flip($countries_keys);
        $countries = array_merge($countries_keys, __('country'));
        asort($countries); // sort value by alphabetical
        $otherOption = ['others' => __('manage/cp/contents/playlists.others')];

        return  array_merge($countries, $otherOption);
    }
}
