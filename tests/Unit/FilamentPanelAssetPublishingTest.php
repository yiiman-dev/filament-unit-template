<?php
namespace Tests\Unit;

use phpDocumentor\Reflection\DocBlock\Tags\Property;
use Tests\TestCase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\ServiceProvider;
class FilamentPanelAssetPublishingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Create test module structure
        $this->createTestModuleStructure();

        $this->registerTestServiceProvider();
    }

    protected function tearDown(): void
    {
        // Clean up test files
        $this->cleanupTestFiles();

        parent::tearDown();
    }

    private function registerTestServiceProvider(): void
    {
        $serviceProvider = new class($this->app) extends ServiceProvider {
            public function boot(): void
            {
                $this->publishes([
                    base_path('Modules/Units/TestUnit/Manage/public') => public_path('units/testunit/manage'),
                ], 'public-manage');
            }
        };

        $this->app->register($serviceProvider);
        $serviceProvider->boot();
    }
    private function createTestModuleStructure(): void
    {
        $modulePath = base_path('Modules/Units');
        $panelPath = $modulePath . '/TestUnit/Manage/public';

        // Create panel public directory
        File::makeDirectory($panelPath, 0755, true);

        // Create test assets
        File::put($panelPath . '/test.css', 'body { color: red; }');
        File::put($panelPath . '/test.js', 'console.log("test");');
    }

    private function cleanupTestFiles(): void
    {
        $modulePath = base_path('Modules/Units');
        $panelPath = $modulePath . '/TestUnit/Manage/public';

        if (File::exists($panelPath)) {
            File::deleteDirectory($panelPath);
        }
    }

    public function test_panel_assets_are_published_correctly(): void
    {
        $modulePath = base_path('Modules/Units');
        $panelPath = $modulePath . '/TestUnit/Manage/public';
        $this->assertTrue(File::exists($panelPath.'/test.css'));

        // Run the publish command
        $this->artisan('vendor:publish --tag=public-manage --force');
        sleep(1);

        //dd(scandir(public_path('units')));

        // Assert the assets were published correctly
        $this->assertTrue(File::exists(public_path('units/testunit/manage/test.css')));
        $this->assertTrue(File::exists(public_path('units/testunit/manage/test.js')));

        // Assert the content is correct
        $this->assertEquals(
            'body { color: red; }',
            File::get(public_path('units/testunit/manage/test.css'))
        );
        $this->assertEquals(
            'console.log("test");',
            File::get(public_path('units/testunit/manage/test.js'))
        );
    }
}
