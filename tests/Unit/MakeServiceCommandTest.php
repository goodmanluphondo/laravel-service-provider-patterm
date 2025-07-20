<?php

namespace GoodmanLuphondo\LaravelServiceRepositoryPattern\Tests\Unit;

use GoodmanLuphondo\LaravelServiceRepositoryPattern\Tests\TestCase;
use Illuminate\Support\Facades\File;

class MakeServiceCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->setupRequiredFiles();
    }

    protected function tearDown(): void
    {
        $this->cleanupTestFiles();
        
        parent::tearDown();
    }

    public function test_it_runs_make_service_command_successfully()
    {
        $this->artisan('make:service', ['name' => 'Test'])
             ->assertExitCode(0)
             ->expectsOutput('TestService.php created successfully.');
        
        $this->assertFileExists(app_path('Services/TestService.php'));
    }

    public function test_it_creates_service_with_repository_pattern()
    {
        $this->createTestModel();

        $this->artisan('make:service', ['name' => 'User', '--repository' => true])
             ->assertExitCode(0);
        
        $this->assertFileExists(app_path('Interfaces/UserRepositoryInterface.php'));
        $this->assertFileExists(app_path('Repositories/UserRepository.php'));
        $this->assertFileExists(app_path('Services/UserService.php'));
    }

    public function test_it_fails_when_base_files_dont_exist()
    {
        File::delete(app_path('Interfaces/BaseInterface.php'));
        
        $this->artisan('make:service', ['name' => 'Test'])
             ->assertExitCode(1)
             ->expectsOutput('The BaseInterface does not exist. Please create app/Interfaces/BaseInterface.php');
    }

    private function setupRequiredFiles(): void
    {
        File::ensureDirectoryExists(app_path('Interfaces'));
        File::ensureDirectoryExists(app_path('Repositories'));
        File::ensureDirectoryExists(app_path('Providers'));
        File::ensureDirectoryExists(app_path('Models'));

        File::put(app_path('Interfaces/BaseInterface.php'), '
            <?php

            namespace App\Interfaces;

            interface BaseInterface
            {
                //
            }
        ');

        File::put(app_path('Repositories/Repository.php'), '
            <?php

            namespace App\Repositories;

            abstract class Repository
            {
                //
            }
        ');

        File::put(app_path('Providers/RepositoryServiceProvider.php'), '
            <?php

            namespace App\Providers;

            use Illuminate\Support\ServiceProvider;

            class RepositoryServiceProvider extends ServiceProvider
            {
                public function boot(): void
                {
                    //
                }
            }
        ');

        $this->copyStubFiles();
    }

    private function copyStubFiles(): void
    {
        $stubsPath = base_path('stubs');
        File::ensureDirectoryExists($stubsPath);

        $packageStubsPath = __DIR__ . '/../../stubs';
        
        if (File::exists($packageStubsPath)) {
            $stubFiles = File::files($packageStubsPath);
            
            foreach ($stubFiles as $stubFile) {
                File::copy($stubFile->getPathname(), $stubsPath . '/' . $stubFile->getFilename());
            }
        }
    }

    private function createTestModel(): void
    {
        File::put(app_path('Models/User.php'), '
            <?php

            namespace App\Models;

            use Illuminate\Database\Eloquent\Model;

            class User extends Model
            {
                //
            }
        ');
    }

    private function cleanupTestFiles(): void
    {
        $files = [
            'Services/TestService.php',
            'Services/UserService.php',
            'Interfaces/UserRepositoryInterface.php',
            'Repositories/UserRepository.php',
            'Models/User.php',
        ];

        foreach ($files as $file) {
            File::delete(app_path($file));
        }

        // Clean up stub files
        $stubsPath = base_path('stubs');
        if (File::exists($stubsPath)) {
            File::deleteDirectory($stubsPath);
        }
    }
}
