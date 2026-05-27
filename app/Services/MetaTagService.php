<?php

declare(strict_types=1);

namespace App\Services;

final class MetaTagService
{
    public function setBetMetaTags(string $title, ?string $description = null, ?string $image = null): void
    {
        view()->share([
            'pageTitle' => $title,
            'pageDescription' => $description ?? __('app.bet_meta_fallback', ['title' => $title]),
            'pageImage' => $image ?? asset('images/tipinuss-waschnusskönig.webp'),
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
