<?php

namespace App\Services\Blockchain;

use Log;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;

class AnZhengEvidenceService
{
    const LOG_TAG = '[services:evidence]: ';

    const RESULT_INNTER_ERROR = -1;
    const RESULT_OPERATE_FAIL = 0;
    const RESULT_OPERATE_SUCCESS = 1;
    const RESULT_PARAMTER_ERROR = 2;
    const RESULT_PARAMTER_EMPTY = 3;
    const RESULT_PARSE_JSON_ERROR = 4;
    const RESULT_TOKEN_TIMEOUT = 5;
    const RESULT_NO_CASE = 6;
    const RESULT_SAVE_CASE_FAIL_WHEN_EXIST = 7;

    public function getAuthor()
    {
        try {
            $anzheng_evidence_token = Cache::get('anzheng_evidence_token');
            if ($anzheng_evidence_token) {
                return ['status' => 'success', 'data' => $anzheng_evidence_token];
            }

            $randkey = rand();
            $hashval = sha1($randkey.config('evidence.vendors.Anzheng.key'));

            $client = new Client();
            $response = $client->post(config('evidence.vendors.Anzheng.domain').'api/GetCommAuthor', [
                'json' => [
                    'randkey' => $randkey,
                    'hashval' => $hashval,
                    'type' => 1,
                ],
            ]);
            $status_code = $response->getStatusCode();
            if (200 == $status_code) {
                $response_body = $response->getBody();
                Log::info(self::LOG_TAG.'request author result:'.$response_body);
                $common_author = json_decode($response_body, true);
                if (self::RESULT_OPERATE_SUCCESS == $common_author['result']) {
                    Cache::put('anzheng_evidence_token', $common_author['token'], 14390);

                    return ['status' => 'success', 'data' => $common_author['token']];
                } else {
                    Log::error(self::LOG_TAG.'get evidence token unsuccessully! [result:'.$common_author['result'].'] [message:'.$common_author['message'].']');

                    return ['status' => 'error', 'message' => $common_author['message']];
                }
            } else {
                Log::error(self::LOG_TAG.'get evidence token unsuccessully! http request unsuccessully! [status_code:'.$status_code.']');

                return ['status' => 'error', 'message' => 'get evidence token unsuccessully! http request unsuccessully! [status_code:'.$status_code.']'];
            }
        } catch (ClientException $e) {
            Log::error(self::LOG_TAG.'get evidence token unsuccessully! client exception!');
        } catch (RequestException $e) {
            Log::error(self::LOG_TAG.'get evidence token unsuccessully! request exception!');
        } catch (Exception $e) {
            Log::error(self::LOG_TAG.'get evidence token unsuccessully! exception!');
        }

        return ['status' => 'error', 'message' => 'get evidence token unsuccessully! unkown error!'];
    }

    public function syncFileInfo($filelist)
    {
        try {
            if (!is_array($filelist) || 0 == count($filelist)) {
                return ['status' => 'error', 'message' => 'file list is not an array or empty'];
            }

            $author = self::getAuthor();
            if ('error' == $author['status']) {
                return $author;
            }

            $zFileSize = 0;
            $fileCount = 0;
            foreach ($filelist as $file) {
                $zFileSize += $file['fileSize'];
                ++$fileCount;
            }

            $request_data = [
                'token' => $author['data'],
                'message' => [
                    'zFileSize' => $zFileSize,
                    'fileCount' => $fileCount,
                    'fileList' => $filelist,
                ],
            ];
            Log::info(self::LOG_TAG.'sync file info request data:'.json_encode($request_data));

            $client = new Client();
            $response = $client->post(config('evidence.vendors.Anzheng.domain').'api/SyncFileInfo', [
                'json' => [
                    'token' => $author['data'],
                    'message' => [
                        'zFileSize' => $zFileSize,
                        'fileCount' => $fileCount,
                        'fileList' => $filelist,
                    ],
                ],
            ]);
            $status_code = $response->getStatusCode();
            if (200 == $status_code) {
                $response_body = $response->getBody();
                Log::info(self::LOG_TAG.'sync file info request result:'.$response_body);
                $sync_file_info = json_decode($response_body, true);

                if (self::RESULT_OPERATE_SUCCESS == $sync_file_info['result']) {
                    return ['status' => 'success', 'data' => $sync_file_info['message']];
                } else {
                    if (self::RESULT_TOKEN_TIMEOUT == $sync_file_info['result']) {
                        Cache::forget('anzheng_evidence_token');

                        return self::syncFileInfo($filelist);
                    } else {
                        Log::error(self::LOG_TAG.'sync file info unsuccessully! [result:'.$sync_file_info['result'].'] [message:'.$sync_file_info['message'].']');

                        return ['status' => 'error', 'message' => $sync_file_info['message']];
                    }
                }
            } else {
                Log::error(self::LOG_TAG.'sync file info unsuccessully! http request unsuccessully! [status_code:'.$status_code.']');

                return ['status' => 'error', 'message' => 'sync file info unsuccessully! http request unsuccessully! [status_code:'.$status_code.']'];
            }
        } catch (ClientException $e) {
            Log::error(self::LOG_TAG.'sync file info unsuccessully! client exception!');
        } catch (RequestException $e) {
            Log::error(self::LOG_TAG.'sync file info unsuccessully! request exception!');
        } catch (Exception $e) {
            Log::error(self::LOG_TAG.'sync file info unsuccessully! exception!');
        }

        return ['status' => 'error', 'message' => 'sync file info unsuccessully! unkown error!'];
    }

    public function informInfo($video_info)
    {
        try {
            $author = self::getAuthor();
            if ('error' == $author['status']) {
                return $author;
            }

            $client = new Client();
            $response = $client->post(config('evidence.vendors.Anzheng.domain').'api/InformInfo', [
                'json' => [
                    'token' => $author['data'],
                    'fileName' => $video_info['fileName'],
                    'noperTime' => $video_info['noperTime'],
                    'fileSize' => $video_info['fileSize'],
                    'sha256' => $video_info['sha256'],
                    'uploadRoute' => $video_info['uploadRoute'],
                    'evTime' => $video_info['evTime'],
                ],
            ]);
            $status_code = $response->getStatusCode();
            if (200 == $status_code) {
                $response_body = $response->getBody();
                Log::info(self::LOG_TAG.'inform info request result:'.$response_body);
                $inform_info = json_decode($response_body, true);

                if (self::RESULT_OPERATE_SUCCESS == $inform_info['result']) {
                    return ['status' => 'success', 'message' => $inform_info['message']];
                } else {
                    if (self::RESULT_TOKEN_TIMEOUT == $inform_info['result']) {
                        Cache::forget('anzheng_evidence_token');

                        return self::informInfo($video_info);
                    } else {
                        Log::error(self::LOG_TAG.'inform info unsuccessully! [result:'.$inform_info['result'].'] [message:'.$inform_info['message'].']');

                        return ['status' => 'error', 'message' => $inform_info['message']];
                    }
                }
            } else {
                Log::error(self::LOG_TAG.'inform info unsuccessully! http request unsuccessully! [status_code:'.$status_code.']');

                return ['status' => 'error', 'message' => 'inform info unsuccessully! http request unsuccessully! [status_code:'.$status_code.']'];
            }
        } catch (ClientException $e) {
            Log::error(self::LOG_TAG.'inform info unsuccessully! client exception!');
        } catch (RequestException $e) {
            Log::error(self::LOG_TAG.'inform info unsuccessully! request exception!');
        } catch (Exception $e) {
            Log::error(self::LOG_TAG.'inform info unsuccessully! exception!');
        }

        return ['status' => 'error', 'message' => 'inform info unsuccessully! unkown error!'];
    }

    public function toApplyInfo($entry)
    {
        try {
            $author = self::getAuthor();
            if ('error' == $author['status']) {
                return $author;
            }

            $client = new Client();
            $response = $client->post(config('evidence.vendors.Anzheng.domain').'api/ToApplyInfo', [
                'json' => [
                    'token' => $author['data'],
                    'CaseId' => $entry->anzhengEvidence->case_id,
                    'OutTime' => time(),
                    'pwd' => $entry->anzhengEvidence->pwd,
                ],
            ]);
            $status_code = $response->getStatusCode();
            if (200 == $status_code) {
                $response_body = $response->getBody();
                Log::info(self::LOG_TAG.'to apply info request result:'.$response_body);
                $apply_info = json_decode($response_body, true);

                if (self::RESULT_OPERATE_SUCCESS == $apply_info['result']) {
                    return ['status' => 'success', 'message' => $apply_info['message']];
                } else {
                    if (self::RESULT_TOKEN_TIMEOUT == $apply_info['result']) {
                        Cache::forget('anzheng_evidence_token');

                        return self::toApplyInfo($entry);
                    } else {
                        Log::error(self::LOG_TAG.'to apply info unsuccessully! [result:'.$apply_info['result'].'] [message:'.$apply_info['message'].']');

                        return ['status' => 'error', 'message' => $apply_info['message']];
                    }
                }
            } else {
                Log::error(self::LOG_TAG.'to apply info unsuccessully! http request unsuccessully! [status_code:'.$status_code.']');

                return ['status' => 'error', 'message' => 'to apply info unsuccessully! http request unsuccessully! [status_code:'.$status_code.']'];
            }
        } catch (ClientException $e) {
            Log::error(self::LOG_TAG.'to apply info unsuccessully! client exception!');
        } catch (RequestException $e) {
            Log::error(self::LOG_TAG.'to apply info unsuccessully! request exception!');
        } catch (Exception $e) {
            Log::error(self::LOG_TAG.'to apply info unsuccessully! exception:'.$e->getMessage());
        }

        return ['status' => 'error', 'message' => 'to apply info unsuccessully! unkown error!'];
    }

    public function downReceipt($entry)
    {
        try {
            $author = self::getAuthor();
            if ('error' == $author['status']) {
                return $author;
            }

            $client = new Client();
            $response = $client->post(config('evidence.vendors.Anzheng.domain').'api/DownReceipt', [
                'json' => [
                    'token' => $author['data'],
                    'Caseid' => $entry->anzhengEvidence->case_id,
                ],
            ]);
            $status_code = $response->getStatusCode();
            if (200 == $status_code) {
                $response_body = $response->getBody();
                Log::info(self::LOG_TAG.'receipt request result:'.$response_body);
                $receipt_info = json_decode($response_body, true);

                if (self::RESULT_OPERATE_SUCCESS == $receipt_info['result']) {
                    return ['status' => 'success', 'data' => $receipt_info['message']];
                } else {
                    if (self::RESULT_TOKEN_TIMEOUT == $receipt_info['result']) {
                        Cache::forget('anzheng_evidence_token');

                        return self::downReceipt($entry);
                    } else {
                        Log::error(self::LOG_TAG.'down receipt unsuccessully! [result:'.$receipt_info['result'].'] [message:'.$receipt_info['message'].']');

                        return ['status' => 'error', 'message' => $receipt_info['message']];
                    }
                }
            } else {
                Log::error(self::LOG_TAG.'down receipt unsuccessully! http request unsuccessully! [status_code:'.$status_code.']');

                return ['status' => 'error', 'message' => 'down receipt unsuccessully! http request unsuccessully! [status_code:'.$status_code.']'];
            }
        } catch (ClientException $e) {
            Log::error(self::LOG_TAG.'down receipt unsuccessully! client exception!');
        } catch (RequestException $e) {
            Log::error(self::LOG_TAG.'down receipt unsuccessully! request exception!');
        } catch (Exception $e) {
            Log::error(self::LOG_TAG.'down receipt unsuccessully! exception!');
        }

        return ['status' => 'error', 'message' => 'down receipt unsuccessully! unkown error!'];
    }
}
