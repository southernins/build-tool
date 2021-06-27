<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;
use SouthernIns\BuildTool\BuildRules\ComposerInstalledRule;
use SouthernIns\BuildTool\BuildRules\EnvFileExistsRule;
use Tests\TestCase;


class EnvFileExistsRuleTest extends TestCase
{


    public function test_env_file_exists_rule_passes()
    {

        $testEnvironment = 'example';
        $rule = new EnvFileExistsRule();

        File::shouldReceive( 'exists' )->once()
            ->andReturn( true );

        $this->assertTrue( $rule->passes('build', $testEnvironment ));

    } //- END test_it_checks_npm_install()


    public function test_env_file_exists_rule_fails()
    {

        $testEnvironment = 'example';
        $rule = new EnvFileExistsRule();

        File::shouldReceive( 'exists' )->once()
            ->andReturn( false );

        $this->assertFalse( $rule->passes('build', $testEnvironment ));

    } //- END test_it_checks_npm_install()


}
