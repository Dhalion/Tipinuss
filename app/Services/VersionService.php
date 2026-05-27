<?php

declare(strict_types=1);

namespace App\Services;

final class VersionService
{
    public static function label(): string
    {
        $commitRaw = config('version.commit', 'dev');
        $commit = is_string($commitRaw) ? $commitRaw : 'dev';

        if ($commit === 'dev') {
            return '';
        }

        $dateRaw = config('version.date', '');
        $date = is_string($dateRaw) ? $dateRaw : '';

        $label = ' · '.$commit;

        if ($date !== '') {
            $label .= ' · '.$date;
        }

        return $label;
    }
}
