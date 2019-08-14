<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

abstract class SecureApiController extends ApiController
{
    protected $key;
    protected $token;

    public function authenticate(Request $request)
    {
        $this->key = $request->query('key');
        if (!isset($this->key)) {
            $this->key = $request->header(config('api.header.key'));
        }

        $this->token = $request->query('token');
        if (!isset($this->token)) {
            $this->token = $request->header(config('api.header.token'));
        }

        if (!isset($this->key) || !isset($this->token)) {
            abort(401);
        }
    }
}
