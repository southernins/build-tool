<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Lang;
use SouthernIns\BuildTool\ShellCommand;
use Tests\TestCase;


class BuildTest extends TestCase
{


    public function test_it_can_create_build_file()
    {
    }

    /**
     * @group testingtest
     */

    public function test_it_checks_npm_install()
    {
        $testEnvironment = 'example';


        try {
            $this->renameNodeModulesForTest();

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

            $this->assertStringContainsString(Lang::get('build-tool::errors.npm_check'), $output);
            $this->assertStringContainsString(Lang::get('build-tool::errors.terminated'), $output);

        } finally {
            $this->restoreNodeModulesAfterTest();
        }

    } //- END test_it_checks_npm_install()


    public function renameNodeModulesForTest()
    {
        // rename node_modules
        rename(base_path() . '/node_modules', base_path() . '/test_node_modules');
    }


    public function restoreNodeModulesAfterTest()
    {
        //restore node_modules
        rename(base_path() . '/test_node_modules', base_path() . '/node_modules');
    }


}
