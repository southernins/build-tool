<?php

namespace SouthernIns\BuildTool\Tests\Feature;

use Illuminate\Support\Facades\Lang;
use SouthernIns\BuildTool\ShellCommand;
use Tests\TestCase;


class BuildProductionTest extends TestCase
{


    public function setUp(): void
    {

        parent::setUp();
    }


    public function test_it_confirms_production_build()
    {
        $testEnvironment = 'production';

        $testProcess = new ShellCommand(
            [
                'php',
                base_path() . '/artisan',
                'build',
                '--env=' . $testEnvironment,
            ],
            [ 'yes' ]
        );

        $output = $testProcess->getOutput();

        $this->assertStringContainsString(
            Lang::get('build-tool::messages.confirmation'),
            $output
        );

        $this->assertStringContainsString(
            Lang::get('build-tool::messages.terminated'),
            $output
        );


    } //- END test_it_checks_npm_install()



}
