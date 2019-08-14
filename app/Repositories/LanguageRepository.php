<?php

namespace App\Repositories;

use App\Models\Language;
use Illuminate\Support\Collection;

class LanguageRepository
{
    /**
     * @var Language
     */
    private $language;

    public function __construct(Language $language)
    {
        $this->language = $language;
    }

    public function getAll(): Collection
    {
        return $this->language->all();
    }

    public function getLanguageIvsCodes(): array
    {
        return $this->language
            ->select('code')
            ->distinct()
            ->get()
            ->pluck('code')
            ->toArray();
    }

    public function getKeyWithValue(): array
    {
        $other_option = ['others' => __('manage/cp/contents/playlists.others')];
        $languages = Language::select('code', 'name')
            ->distinct()
            ->orderBy('name')
            ->pluck('name', 'code')
            ->toArray();

        return array_merge($languages, $other_option);
    }
}
