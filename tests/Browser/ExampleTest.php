<?php

declare(strict_types=1);

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

final class ExampleTest extends DuskTestCase
{
    public function test_homepage_loads(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/datenschutz')
                ->assertSee('Tipinuss')
                ->assertSee('Datenschutz');
        });
    }
}
