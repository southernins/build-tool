<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Lang;
use SouthernIns\BuildTool\ShellCommand;
use Tests\TestCase;


class BuildNpmCheckTest extends TestCase
{

    private $randomizedFolder;

    public function setUp(): void
    {

        $this->randomizedFolder = uniqid( 'node_modules_' );
        parent::setUp();

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

            $this->assertTrue( $testProcess->isSuccessful() );

            $this->assertStringContainsString(
                Lang::get('build-tool::messages.error.npm_check'),
                $output
            );

            $this->assertStringContainsString(
                Lang::get('build-tool::messages.terminated'),
                $output
            );

        } finally {
            $this->restoreNodeModulesAfterTest();
        }

    } //- END test_it_checks_npm_install()


    public function renameNodeModulesForTest(){
        // rename node_modules
        rename(base_path() . '/node_modules', base_path() . '/' . $this->randomizedFolder );
    }


    public function restoreNodeModulesAfterTest(){
        //restore node_modules
        rename(base_path() . '/' . $this->randomizedFolder , base_path() . '/node_modules');
    }


}
