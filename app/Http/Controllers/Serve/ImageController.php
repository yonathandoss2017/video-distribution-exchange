<?php

namespace App\Http\Controllers\Serve;

use App\Models\Entry;
use App\Models\Playlist;
use App\Models\Property;
use App\Models\EntryScene;
use Illuminate\Http\Request;
use App\Models\AiReviewVideoResult;
use App\Http\Controllers\Controller;
use App\Services\Serve\VideoImageService;
use App\Services\Serve\ScenesImageService;
use App\Services\Serve\AiReviewImageService;
use App\Services\Serve\PlaylistImageService;
use App\Services\Serve\PropertyImageService;

class ImageController extends Controller
{
    protected function getWidth($request)
    {
        return $request->filled('width') ? $request->input('width') : 0;
    }

    public function video(Request $request, $prop_id, Entry $entry, $timestamp)
    {
        return redirect(VideoImageService::getImage($entry, $prop_id, $this->getWidth($request)), 301);
    }

    public function playlist(Request $request, $prop_id, Playlist $playlist, $timestamp)
    {
        return redirect(PlaylistImageService::getImage($playlist, $prop_id, $this->getWidth($request)), 301);
    }

    public function scenes(Request $request, EntryScene $scenes, $timestamp)
    {
        return redirect(ScenesImageService::getImage($scenes, $this->getWidth($request)), 301);
    }

    public function property(Request $request, Property $property, $imagetype, $timestamp)
    {
        return redirect(PropertyImageService::getImage($property, $imagetype, $this->getWidth($request)), 301);
    }

    public function aiReviewResult(Request $request, AiReviewVideoResult $aiReviewVideoResult, $timestamp)
    {
        return redirect(AiReviewImageService::getImage($aiReviewVideoResult, $this->getWidth($request)), 301);
    }
}
