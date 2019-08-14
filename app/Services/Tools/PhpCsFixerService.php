<?php

namespace App\Services\Tools;

class PhpCsFixerService
{
    public static function check($autoFix = false)
    {
        $errors = [];
        $mode = $autoFix ? '' : '--dry-run';
        // Run linter in dry-run mode so it changes nothing.
        exec(
            './vendor/bin/php-cs-fixer fix '.$mode.' ',
            $output
        );

        // If we've got output, pop its first item ("Fixed all files...")
        // and trim whitespace from the rest so the below makes sense.
        if ($output) {
            array_pop($output);
            array_pop($output);
            $output = array_map('trim', $output);
            $errors = $output + $errors;
        }

        return $errors;
    }
}
