<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\VideoCompare;
use Illuminate\Http\Request;
use App\Models\EntryFingerprint;
use App\Models\VideoCompareResult;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class BlockchainNotificationController extends Controller
{
    private $tag = '[blockchain notification - %s]: ';

    private function getTag()
    {
        return sprintf($this->tag, debug_backtrace()[1]['function']);
    }

    public function fingerprintExtraction(Request $request)
    {
        try {
            Log::debug($this->getTag().$request->getContent());
            $message = json_decode($request->getContent(), true);

            if (isset($message['state'])) {
                $entryFingerprint = EntryFingerprint::where('job_id', $message['job_id'])->first();
                if ($entryFingerprint) {
                    $entryFingerprint->status = 1 == $message['state'] ? EntryFingerprint::STATUS_FINGERPRINT_EXTRACTION_SUCCESS : EntryFingerprint::STATUS_FINGERPRINT_EXTRACTION_FAILED;
                    $entryFingerprint->save();
                }
            }
        } catch (Exception $e) {
            Log::error($this->getTag().print_r($e->getMessage(), true));
        }
    }

    public function fingerprintCompare(Request $request)
    {
        try {
            Log::debug($this->getTag().$request->getContent());
            $message = json_decode($request->getContent(), true);

            if (isset($message['job_id'])) {
                $videoCompare = VideoCompare::where('job_id', $message['job_id'])->first();
                if ($videoCompare) {
                    if (isset($message['match_result'])) {
                        $match_result = json_decode($message['match_result'], true);
                        $result = $match_result['MatchResult'];

                        $videoCompare->status = VideoCompare::STATUS_FINISHED;
                        $videoCompare->duration_ms = $result['length'] * 1000;
                        $videoCompare->save();

                        if (isset($result['MatchedVideos'])) {
                            $matched_videos = $result['MatchedVideos']['MatchedVideo'];
                            DB::beginTransaction();
                            try {
                                foreach ($matched_videos as $matched_video) {
                                    if (empty($matched_video['video_id'])) {
                                        continue;
                                    }

                                    $videoCompareResult = VideoCompareResult::create([
                                        'compare_id' => $videoCompare->id,
                                        'entry_id' => $matched_video['video_id'],
                                        'confidence' => $matched_video['confidence'],
                                        'distortion' => $matched_video['distortion'],
                                        'length_ms' => $matched_video['length'] * 1000,
                                    ]);

                                    if ($videoCompareResult) {
                                        if (isset($matched_video['Matches']['Match'])) {
                                            $match_fragments = [];
                                            if (count($matched_video['Matches']['Match']) != count($matched_video['Matches']['Match'], COUNT_RECURSIVE)) {
                                                foreach ($matched_video['Matches']['Match'] as $match) {
                                                    array_push($match_fragments, [
                                                        'searchedVideoStartingMatchedPosition_ms' => $match['SearchedVideoStartingMatchedPosition'] * 1000,
                                                        'matchedVideoStartingMatchedPosition_ms' => $match['MatchedVideoStartingMatchedPosition'] * 1000,
                                                        'duration_ms' => $match['Duration'] * 1000,
                                                    ]);
                                                }
                                            } else {
                                                array_push($match_fragments, [
                                                    'searchedVideoStartingMatchedPosition_ms' => $matched_video['Matches']['Match']['SearchedVideoStartingMatchedPosition'] * 1000,
                                                    'matchedVideoStartingMatchedPosition_ms' => $matched_video['Matches']['Match']['MatchedVideoStartingMatchedPosition'] * 1000,
                                                    'duration_ms' => $matched_video['Matches']['Match']['Duration'] * 1000,
                                                ]);
                                            }
                                            if (count($match_fragments) > 0) {
                                                $videoCompareResult->matchFragments()->createMany($match_fragments);
                                            }
                                        }
                                    }
                                }
                                DB::commit();
                            } catch (Exception $exception) {
                                Log::error($this->getTag().print_r($exception->getMessage(), true));
                                DB::rollback();

                                $videoCompare->status = VideoCompare::STATUS_FAILED;
                                $videoCompare->save();
                            }
                        }
                    } else {
                        $videoCompare->status = VideoCompare::STATUS_FAILED;
                        $videoCompare->save();
                    }
                }
            }
        } catch (Exception $e) {
            Log::error($this->getTag().print_r($e->getMessage(), true));
        }
    }
}
