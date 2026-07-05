<?php

declare(strict_types=1);

namespace Tests;

use App\Models\User;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Laravel\Dusk\TestCase as BaseTestCase;
use PHPUnit\Framework\Attributes\BeforeClass;

abstract class DuskTestCase extends BaseTestCase
{
    use DatabaseMigrations;

    private static bool $browserMacrosRegistered = false;

    #[BeforeClass]
    public static function prepare(): void
    {
        if (! static::runningInSail()) {
            static::startChromeDriver(['--port=9515']);
        }
    }

    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        self::registerBrowserMacros();
    }

    protected function driver(): RemoteWebDriver
    {
        $options = (new ChromeOptions)->addArguments(collect([
            $this->shouldStartMaximized() ? '--start-maximized' : '--window-size=1920,1080',
            '--disable-search-engine-choice-screen',
            '--disable-smooth-scrolling',
            '--disable-gpu',
            '--headless=new',
            '--no-sandbox',
        ])->all());

        return RemoteWebDriver::create(
            $_ENV['DUSK_DRIVER_URL'] ?? env('DUSK_DRIVER_URL') ?? 'http://localhost:9515',
            DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY, $options
            )
        );
    }

    private static function registerBrowserMacros(): void
    {
        if (self::$browserMacrosRegistered) {
            return;
        }

        Browser::macro('formLoginAs', function (User $user): Browser {
            /** @var Browser $this */
            return $this->visit(route('login'))
                ->waitFor('#login-form')
                ->type('input[name="email"]', $user->email)
                ->type('input[name="password"]', 'password')
                ->pause(500)
                ->click('#login-submit')
                ->pause(1500);
        });

        self::$browserMacrosRegistered = true;
    }
}
