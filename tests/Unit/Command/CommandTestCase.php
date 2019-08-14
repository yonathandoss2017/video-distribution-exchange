<?php

namespace Tests\Unit\Command;

use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

class CommandTestCase extends TestCase
{
    /**
     * @param string|array $searchStrings
     */
    protected function seeInConsoleOutput($searchStrings)
    {
        if (!is_array($searchStrings)) {
            $searchStrings = [$searchStrings];
        }
        $output = Artisan::output();
        foreach ($searchStrings as $searchString) {
            $this->assertContains((string) $searchString, $output);
        }
    }
}
