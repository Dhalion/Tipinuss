<?php

declare(strict_types=1);

namespace Tests\Browser;

use App\Models\User;
use Database\Seeders\Test\TestDatabaseSeeder;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

abstract class BrowserTestCase extends DuskTestCase
{
    use DatabaseTruncation;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(TestDatabaseSeeder::class);
    }

    protected function loginAs(Browser $browser, string $email = 'alice@test.com'): void
    {
        $user = User::where('email', $email)->firstOrFail();
        $browser->loginAs($user);
    }
}
