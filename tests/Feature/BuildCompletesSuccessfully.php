<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Lang;
use SouthernIns\BuildTool\ShellCommand;
use Tests\TestCase;


class BuildNpmCheckTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();
    }


    /**
     * @group testingtest
     */

    public function test_it_can_build_development()
    {
        $testEnvironment = 'example';


        $testProcess = new ShellCommand(
            [
                'php',
                base_path() . '/artisan',
                'build',
                '--env=' . $testEnvironment
            ]
        );

        $output = $testProcess->getOutput();

        $this->assertTrue($testProcess->isSuccessful());

        $this->assertStringContainsString(
            Lang::get('build-tool::messages.build-successful'),
            $output
        );

    } //- END test_it_checks_npm_install()


}
