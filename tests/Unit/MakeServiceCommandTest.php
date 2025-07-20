<?php

namespace GoodmanLuphondo\LaravelServiceRepositoryPattern\Tests\Unit;

use GoodmanLuphondo\LaravelServiceRepositoryPattern\Tests\TestCase;

class MakeServiceCommandTest extends TestCase
{
    /** @test */
    public function it_runs_make_service_command_successfully()
    {
        $this->artisan('make:service', ['name' => 'TestService'])
             ->assertExitCode(0);
        
        $this->assertFileExists(app_path('Services/TestService.php'));
    }
}
