<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * @param $key
     * @param $token
     *
     * @return array
     */
    protected function header($key, $token)
    {
        return [
            'x-ivx-api-key' => $key,
            'x-ivx-api-token' => $token,
        ];
    }
}
