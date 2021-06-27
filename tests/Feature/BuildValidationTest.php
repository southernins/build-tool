<?php

namespace SouthernIns\BuildTool\Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Lang;
use SouthernIns\BuildTool\ShellCommand;
use Tests\TestCase;


class BuildValidationTest extends TestCase
{


    public function setUp(): void
    {
        parent::setUp();
    }


    public function test_it_checks_protected_branches()
    {
        $testEnvironment = 'production';

//        $this->artisan( 'build --env=' . $testEnvironment )
//            ->expectsOutput( Lang::get('build-tool::validation.protected_branch' ) )
//            ->expectsOutput( Lang::get('build-tool::messages.terminated' ) )
//            ->assertExitCode(0);

//        Artisan::call( 'build', [ '--env' => $testEnvironment ]);
//
//
//        $cmdOutput = Artisan::getOutput();
//
//        dump( $cmdOutput );

        $testProcess = new ShellCommand(
            [
                'php',
                base_path() . '/artisan',
                'build',
                '--env=' . $testEnvironment,
            ]
        );

        $output = $testProcess->getOutput();

        $this->assertStringContainsString(
            Lang::get('build-tool::validation.protected_branch' ),
            $output
        );

        $this->assertStringContainsString(
            Lang::get('build-tool::messages.terminated' ),
            $output
        );


    } //- END test_it_checks_protected_branches()


    public function test_it_verifys_env_file_exists(){

        $testEnvironment = 'not-found';

        $testProcess = new ShellCommand(
            [
                'php',
                base_path() . '/artisan',
                'build',
                '--env=' . $testEnvironment,
            ]
        );

        $output = $testProcess->getOutput();

        $this->assertStringContainsString(
            Lang::get('build-tool::validation.env_file_not_found'),
            $output
        );

        $this->assertStringContainsString(
            Lang::get('build-tool::messages.terminated'),
            $output
        );


    } //- END test_it_checks_protected_branches()


} //- END BuildValidationTest{}
