<?php

declare(strict_types=1);

namespace App\Services;

final class MetaTagService
{
    public function setBetMetaTags(string $title, ?string $description = null, ?string $image = null): void
    {
        view()->share([
            'pageTitle' => $title,
            'pageDescription' => $description ?? 'Tippe auf '.$title.' und verdiene Waschnüsse auf Tipinuss',
            'pageImage' => $image ?? asset('images/logo-full.webp'),
        ]);
    }

    public function clearMetaTags(): void
    {
        view()->share([
            'pageTitle' => null,
            'pageDescription' => null,
            'pageImage' => null,
        ]);
    }
}
